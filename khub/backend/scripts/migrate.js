#!/usr/bin/env node
/**
 * Run (or roll back) the knex migrations for one site's database.
 *
 *   npm run migrate                 # default site
 *   npm run migrate -- --site=nigeria
 *   npm run migrate:rollback -- --site=nigeria
 */
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { loadEnv } from '../src/core/config/env.js';
import { createLogger } from '../src/core/logger.js';
import { ConnectionRegistry } from '../src/core/db/connection.js';
import { SiteRegistry } from '../src/core/site/registry.js';

const args = Object.fromEntries(process.argv.slice(2).map((arg) => arg.replace(/^--/, '').split('=')).map(([key, value]) => [key, value ?? true]));

const env = loadEnv();
const logger = createLogger(env);
const connections = new ConnectionRegistry();
const sites = new SiteRegistry({ env, connections, logger });
await sites.load();

const site = sites.get(args.site ?? 'default');
if (!site) {
  logger.error(`Unknown site "${args.site}"`);
  process.exit(1);
}

const config = { directory: path.resolve(path.dirname(fileURLToPath(import.meta.url)), '../migrations'), loadExtensions: ['.js'] };

try {
  if (args.rollback) {
    const [batch, files] = await site.db.migrate.rollback(config);
    logger.info({ batch, files }, `rolled back ${site.id}`);
  } else {
    const [batch, files] = await site.db.migrate.latest(config);
    logger.info({ batch, files }, files.length ? `migrated ${site.id}` : `${site.id} already up to date`);
  }
} finally {
  await connections.destroyAll();
}
