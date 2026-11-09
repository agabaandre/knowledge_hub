import fs from 'node:fs/promises';
import path from 'node:path';
import { pathToFileURL } from 'node:url';
import { MODULES, isKnownModule } from '@khub/shared';
import { HttpError } from '../http/errors.js';

/**
 * Discovers `src/modules/<name>/index.js` files. Each exports
 * `{ name, router(context) }`. A request only reaches a module's router when
 * the module is enabled for the request's site, so switching a module off in
 * the admin area immediately disables its API and its pages.
 */
export class ModuleRegistry {
  #modules = new Map();

  constructor({ modulesDir, logger }) {
    this.modulesDir = modulesDir;
    this.logger = logger;
  }

  async load() {
    const entries = await fs.readdir(this.modulesDir, { withFileTypes: true });
    for (const entry of entries) {
      if (!entry.isDirectory()) continue;
      const file = path.join(this.modulesDir, entry.name, 'index.js');
      const definition = (await import(pathToFileURL(file).href)).default;
      if (!isKnownModule(definition.name)) {
        throw new Error(`Module folder "${entry.name}" exports unknown module name "${definition.name}"; add it to shared/src/modules.js`);
      }
      this.#modules.set(definition.name, definition);
    }
    this.logger.info({ modules: [...this.#modules.keys()] }, 'modules loaded');
  }

  all() {
    return [...this.#modules.values()];
  }

  /** Express middleware factory mounting a module router behind its enabled flag. */
  guarded(definition, context) {
    const router = definition.router(context);
    return async (req, res, next) => {
      try {
        const flags = await context.sites.moduleFlags(req.site);
        if (!flags[definition.name]) return next(new HttpError(404, `Module "${definition.name}" is disabled on this site`));
        return router(req, res, next);
      } catch (error) {
        return next(error);
      }
    };
  }

  /** Catalogue with per-site enabled state for the admin UI and the frontend manifest. */
  async manifest(sites, site) {
    const flags = await sites.moduleFlags(site);
    return Object.entries(MODULES).map(([name, meta]) => ({
      name,
      label: meta.label,
      description: meta.description,
      core: meta.core,
      enabled: flags[name],
      implemented: this.#modules.has(name),
    }));
  }
}
