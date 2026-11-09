import knex from 'knex';
import { dbClientFromEnv } from '../config/env.js';

/** Replace `${VAR}` placeholders in site.json database values with env vars. */
const expandEnv = (value) =>
  typeof value === 'string' ? value.replace(/\$\{([A-Z0-9_]+)\}/g, (_, name) => process.env[name] ?? '') : value;

export const buildKnexConfig = (database) => {
  const client = dbClientFromEnv(database.client);
  return {
    client,
    connection: {
      host: expandEnv(database.host),
      port: Number(expandEnv(database.port)),
      database: expandEnv(database.database),
      user: expandEnv(database.user),
      password: expandEnv(database.password),
      ...(client === 'mysql2' ? { charset: 'utf8mb4', timezone: 'Z' } : {}),
    },
    pool: { min: 0, max: Number(database.poolMax ?? 10) },
  };
};

/**
 * One knex instance per distinct database. Sites that share the global
 * connection share the pool.
 */
export class ConnectionRegistry {
  #instances = new Map();

  get(database) {
    const key = JSON.stringify(buildKnexConfig(database).connection);
    if (!this.#instances.has(key)) {
      this.#instances.set(key, knex(buildKnexConfig(database)));
    }
    return this.#instances.get(key);
  }

  async destroyAll() {
    await Promise.all([...this.#instances.values()].map((db) => db.destroy()));
    this.#instances.clear();
  }
}
