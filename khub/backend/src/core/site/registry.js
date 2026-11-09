import fs from 'node:fs/promises';
import path from 'node:path';
import { normalizeSiteConfig, resolveModuleFlags } from '@khub/shared';
import { siteIdFromUrl, sanitizeSiteId } from './identifier.js';
import { sitePaths } from '../storage/paths.js';
import { ModuleSettingsRepository } from './module-settings-repository.js';
import { TtlCache, MINUTE } from '../cache.js';

/**
 * Loads every `sites/<id>/site.json`, resolves the site for a request and
 * answers which modules are enabled for it (site.json defaults + admin
 * overrides stored in the site's database).
 */
export class SiteRegistry {
  #sites = new Map();
  #cache = new TtlCache();

  constructor({ env, connections, logger }) {
    this.env = env;
    this.connections = connections;
    this.logger = logger;
  }

  async load() {
    const entries = await fs.readdir(this.env.sitesDir, { withFileTypes: true });
    for (const entry of entries) {
      if (!entry.isDirectory()) continue;
      const file = path.join(this.env.sitesDir, entry.name, 'site.json');
      const config = normalizeSiteConfig(JSON.parse(await fs.readFile(file, 'utf8')), entry.name);
      if (!config.enabled) {
        this.logger.info({ site: config.id }, 'site disabled in site.json; skipped');
        continue;
      }
      this.#register(config);
    }
    if (!this.#sites.has('default')) throw new Error(`sites/default/site.json is required in ${this.env.sitesDir}`);
    this.logger.info({ sites: [...this.#sites.keys()] }, 'sites loaded');
  }

  #register(config) {
    const storageId = config.storageId
      ? sanitizeSiteId(config.storageId)
      : config.id === 'default' && this.env.hubSiteId
        ? sanitizeSiteId(this.env.hubSiteId)
        : siteIdFromUrl(this.env.appUrl, config.pathPrefix);

    const database = config.database ?? this.env.database;
    const site = Object.freeze({
      ...config,
      storageId,
      paths: sitePaths(this.env.hubDataRoot, storageId),
      database,
      db: this.connections.get(database),
      publicUrl: config.pathPrefix ? `${this.env.appUrl}/${config.pathPrefix}` : this.env.appUrl,
    });
    this.#sites.set(site.id, site);
  }

  all() {
    return [...this.#sites.values()];
  }

  get(id) {
    return this.#sites.get(id) ?? null;
  }

  get default() {
    return this.#sites.get('default');
  }

  /** @returns {{ site: object, pathPrefix: string }} */
  resolve({ hostname, path: requestPath }) {
    if (this.env.siteResolution === 'host') {
      const host = String(hostname ?? '').toLowerCase();
      const byHost = this.all().find((site) => site.hosts.includes(host));
      return { site: byHost ?? this.default, pathPrefix: '' };
    }

    const firstSegment = String(requestPath ?? '').split('/').filter(Boolean)[0] ?? '';
    const byPrefix = firstSegment ? this.all().find((site) => site.pathPrefix === firstSegment) : null;
    return byPrefix ? { site: byPrefix, pathPrefix: `/${byPrefix.pathPrefix}` } : { site: this.default, pathPrefix: '' };
  }

  /** Enabled modules for a site: site.json defaults, then admin overrides from DB. */
  moduleFlags(site) {
    return this.#cache.remember(`modules:${site.id}`, 5 * MINUTE, async () => {
      const overrides = await new ModuleSettingsRepository(site.db).all();
      return resolveModuleFlags(site.modules, overrides);
    });
  }

  async setModuleEnabled(site, name, enabled, userId) {
    await new ModuleSettingsRepository(site.db).set(name, enabled, userId);
    this.#cache.forget(`modules:${site.id}`);
    return this.moduleFlags(site);
  }
}
