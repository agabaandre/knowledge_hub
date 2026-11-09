#!/usr/bin/env node
/**
 * Compare row counts of every known table between a source database and the
 * target site database. Exits 1 when any table differs.
 *
 *   SOURCE_DB_* env = source connection (mysql2 or pg)
 *   default site    = target connection (or --site=<id>)
 *
 *   node scripts/verify-counts.js
 *   node scripts/verify-counts.js --site=nigeria --tables=users,publication
 */
import knex from 'knex';
import { loadEnv, dbClientFromEnv } from '../src/core/config/env.js';
import { createLogger } from '../src/core/logger.js';
import { ConnectionRegistry } from '../src/core/db/connection.js';
import { SiteRegistry } from '../src/core/site/registry.js';
import { LARAVEL_TABLES } from './laravel-table-map.js';

const args = Object.fromEntries(process.argv.slice(2).map((arg) => arg.replace(/^--/, '').split('=')).map(([k, v]) => [k, v ?? true]));
const env = loadEnv();
const logger = createLogger(env);

const source = knex({
  client: dbClientFromEnv(process.env.SOURCE_DB_CLIENT ?? 'mysql2'),
  connection: {
    host: process.env.SOURCE_DB_HOST ?? '127.0.0.1',
    port: Number(process.env.SOURCE_DB_PORT ?? 3306),
    database: process.env.SOURCE_DB_DATABASE ?? 'knowledge_hub',
    user: process.env.SOURCE_DB_USERNAME ?? 'root',
    password: process.env.SOURCE_DB_PASSWORD ?? '',
  },
});

const connections = new ConnectionRegistry();
const sites = new SiteRegistry({ env, connections, logger });
await sites.load();
const target = sites.get(args.site ?? 'default').db;
const names = typeof args.tables === 'string' ? args.tables.split(',') : LARAVEL_TABLES.map((t) => t.name);

const countRows = async (db, table) => {
  if (!(await db.schema.hasTable(table))) return null;
  const row = await db(table).count({ total: '*' }).first();
  return Number(row.total);
};

let mismatches = 0;
try {
  for (const table of names) {
    const [src, dst] = [await countRows(source, table), await countRows(target, table)];
    const status = src === null ? 'missing-in-source' : dst === null ? 'missing-in-target' : src === dst ? 'ok' : 'MISMATCH';
    if (status !== 'ok') mismatches += 1;
    logger.info({ table, source: src, target: dst, status }, 'row count');
  }
  logger.info({ mismatches }, mismatches ? 'verification FAILED' : 'verification passed: all counts match');
  process.exitCode = mismatches ? 1 : 0;
} finally {
  await source.destroy();
  await connections.destroyAll();
}
