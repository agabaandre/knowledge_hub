/**
 * Run a create-table block only when the table does not exist yet. Lets the
 * Node migrations run on top of a Laravel database that already has the same
 * tables (adopt-in-place), while still building a brand new database from scratch.
 */
export const createIfMissing = async (knex, table, build) => {
  if (await knex.schema.hasTable(table)) return false;
  await knex.schema.createTable(table, build);
  return true;
};

/** Add a column only when it is absent (Laravel may already have it). */
export const addColumnIfMissing = async (knex, table, column, build) => {
  if (await knex.schema.hasColumn(table, column)) return false;
  await knex.schema.alterTable(table, build);
  return true;
};

const MAX_IDENTIFIER = 60;

/**
 * Index name that fits MySQL's 64-character limit (PostgreSQL truncates at 63
 * silently, which would hide the problem). Long names keep a readable prefix
 * and end with a short hash so they stay unique.
 */
export const indexName = (table, column, suffix = 'idx') => {
  const full = `${table}_${column}_${suffix}`;
  if (full.length <= MAX_IDENTIFIER) return full;
  let hash = 0;
  for (const char of full) hash = (hash * 31 + char.charCodeAt(0)) >>> 0;
  return `${full.slice(0, MAX_IDENTIFIER - 9)}_${hash.toString(36).padStart(8, '0').slice(0, 8)}`;
};
