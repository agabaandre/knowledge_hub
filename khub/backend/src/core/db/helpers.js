/**
 * Helpers that hide the few differences between MySQL and PostgreSQL.
 */
export const isPostgres = (db) => db.client.config.client === 'pg';

/** ORDER BY random(), portable. */
export const randomOrder = (db) => db.raw(isPostgres(db) ? 'random()' : 'rand()');

/** MySQL returns 1/0 for tinyint(1); PostgreSQL returns booleans. */
export const toBool = (value) => value === true || value === 1 || value === '1';

/** Laravel stored some flags as the strings "Active"/"Yes". */
export const isActiveFlag = (value) => toBool(value) || String(value).toLowerCase() === 'active';

/** Escape LIKE wildcards in user input before building a pattern. */
export const likePattern = (term) => `%${String(term).replace(/[\\%_]/g, (c) => `\\${c}`)}%`;

/** Date → DB-friendly ISO string (both drivers accept it). */
export const now = () => new Date().toISOString().slice(0, 19).replace('T', ' ');

/** Count rows of a query builder without the ORDER BY/limit. */
export const countOf = async (query) => {
  const row = await query.clone().clearOrder().clearSelect().count({ total: '*' }).first();
  return Number(row?.total ?? 0);
};
