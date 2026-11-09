#!/usr/bin/env node
/**
 * Copy data from an existing Laravel Knowledge Hub database into a site of the
 * new architecture, and verify the upload folder is in place.
 *
 *   npm run migrate:from-laravel                      # into the default site
 *   npm run migrate:from-laravel -- --site=nigeria    # into another site
 *   npm run migrate:from-laravel -- --dry-run         # count only
 *   npm run migrate:from-laravel -- --tables=users,publication
 *
 * Source connection: SOURCE_DB_* env vars (mysql2 or pg). The target is the
 * site's database (run `npm run migrate` first so the tables exist).
 * Both directions work: MySQL → PostgreSQL, PostgreSQL → MySQL, same → same.
 *
 * Files are not copied: the new backend reads the same
 * {HUB_DATA_ROOT}/{site-id}/files tree. Set SOURCE_SITE_ID when the Laravel
 * site id differs from the new one and the script will tell you what to move.
 */
import fs from 'node:fs/promises';
import knex from 'knex';
import { loadEnv, dbClientFromEnv } from '../src/core/config/env.js';
import { createLogger } from '../src/core/logger.js';
import { ConnectionRegistry } from '../src/core/db/connection.js';
import { SiteRegistry } from '../src/core/site/registry.js';
import { isPostgres } from '../src/core/db/helpers.js';
import { LARAVEL_TABLES, coerceBooleans } from './laravel-table-map.js';

const CHUNK = 500;

const args = Object.fromEntries(process.argv.slice(2).map((arg) => arg.replace(/^--/, '').split('=')).map(([key, value]) => [key, value ?? true]));
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
const site = sites.get(args.site ?? 'default');
if (!site) {
  logger.error(`Unknown site "${args.site}"`);
  process.exit(1);
}
const target = site.db;

const sameDatabase = () => {
  const src = source.client.config.connection;
  const dst = target.client.config.connection;
  return source.client.config.client === target.client.config.client && src.host === dst.host && String(src.port) === String(dst.port) && src.database === dst.database;
};
if (sameDatabase()) {
  logger.error("source and target are the same database; point SOURCE_DB_* at the Laravel database");
  await source.destroy();
  await connections.destroyAll();
  process.exit(1);
}

const wanted = typeof args.tables === 'string' ? new Set(args.tables.split(',')) : null;
const dryRun = Boolean(args['dry-run']);

const targetColumns = async (table) => {
  const info = await target(table).columnInfo();
  const booleans = Object.entries(info).filter(([, meta]) => meta.type === 'boolean' || meta.type === 'tinyint').map(([name]) => name);
  return { names: new Set(Object.keys(info)), booleans };
};

const copyTable = async ({ name, skipColumns = [], overflowColumn = null }) => {
  if (!(await source.schema.hasTable(name))) return logger.warn(`source has no table ${name}; skipped`);
  if (!(await target.schema.hasTable(name))) return logger.warn(`target has no table ${name}; run npm run migrate first`);

  const { names, booleans } = await targetColumns(name);
  const sourceColumns = Object.keys(await source(name).columnInfo());
  const dropped = sourceColumns.filter((column) => !names.has(column) && !skipColumns.includes(column));
  const overflow = overflowColumn && names.has(overflowColumn) ? dropped : [];
  if (overflow.length) logger.info({ table: name, columns: overflow.length }, `${name}: ${overflow.length} unmodelled columns kept in ${overflowColumn}`);
  else if (dropped.length) logger.warn({ table: name, columns: dropped }, `${name}: source columns with no target column (not copied)`);
  const total = Number((await source(name).count({ total: '*' }).first()).total);
  const existing = Number((await target(name).count({ total: '*' }).first()).total);
  if (existing > 0 && !args.force) return logger.warn(`${name}: target already has ${existing} rows; use --force to append`);
  if (dryRun) return logger.info(`${name}: would copy ${total} rows`);

  let copied = 0;
  let lastId = 0;
  const hasId = names.has('id') && (await source.schema.hasColumn(name, 'id'));
  while (copied < total) {
    const query = source(name).limit(CHUNK);
    const rows = hasId ? await query.where('id', '>', lastId).orderBy('id') : await query.offset(copied);
    if (rows.length === 0) break;

    const cleaned = rows.map((row) => {
      const out = {};
      for (const [column, value] of Object.entries(row)) {
        if (names.has(column) && !skipColumns.includes(column)) out[column] = value;
      }
      if (overflow.length) out[overflowColumn] = JSON.stringify(Object.fromEntries(overflow.map((column) => [column, row[column]])));
      return coerceBooleans(out, booleans);
    });
    await target.batchInsert(name, cleaned, CHUNK);
    copied += rows.length;
    if (hasId) lastId = rows.at(-1).id;
  }

  if (hasId && isPostgres(target) && copied > 0) {
    await target.raw(`SELECT setval(pg_get_serial_sequence('??', 'id'), (SELECT COALESCE(MAX(id), 1) FROM ??))`, [name, name]);
  }
  logger.info(`${name}: copied ${copied}/${total}`);
};

const checkFiles = async () => {
  const sourceSiteId = process.env.SOURCE_SITE_ID ?? site.storageId;
  const sourceFiles = `${env.hubDataRoot}/${sourceSiteId}/files`;
  const exists = await fs.stat(sourceFiles).then((stat) => stat.isDirectory()).catch(() => false);
  if (!exists) return logger.warn(`uploads folder ${sourceFiles} not found; copy the Laravel files tree to ${site.paths.files}`);
  if (sourceFiles === site.paths.files) return logger.info(`uploads already in place at ${site.paths.files}`);
  logger.warn(`uploads live in ${sourceFiles}; move or symlink them to ${site.paths.files} (e.g. rsync -a "${sourceFiles}/" "${site.paths.files}/")`);
};

try {
  logger.info({ from: source.client.config.connection.database, to: `${site.id} (${target.client.config.client})`, dryRun }, 'starting Laravel → Node migration');
  for (const table of LARAVEL_TABLES) {
    if (wanted && !wanted.has(table.name)) continue;
    await copyTable(table);
  }
  await checkFiles();
  logger.info('done');
} catch (error) {
  logger.error({ err: error }, 'migration failed');
  process.exitCode = 1;
} finally {
  await source.destroy();
  await connections.destroyAll();
}
