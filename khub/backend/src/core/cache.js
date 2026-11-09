import { redisStore } from './redis-store.js';

/**
 * TTL cache: always memory-first, and — for caches constructed with a name —
 * backed by shared Redis when REDIS_URL is configured (see redis-store.js).
 * Named caches share entries and invalidations across API instances; unnamed
 * ones stay per-process (for values that cannot serialize, like transports).
 */
export class TtlCache {
  #entries = new Map();
  #name;

  /** @param {{name?: string}} [options] name opts the cache into shared Redis. */
  constructor({ name } = {}) {
    this.#name = name ?? null;
  }

  #sharedKey(key) {
    return `${this.#name}:${key}`;
  }

  #shared() {
    return this.#name !== null && redisStore.enabled();
  }

  /**
   * @template T
   * @param {string} key
   * @param {number} ttlMs
   * @param {() => Promise<T>|T} produce
   * @returns {Promise<T>}
   */
  async remember(key, ttlMs, produce) {
    const hit = this.#entries.get(key);
    if (hit && hit.expiresAt > Date.now()) return hit.value;

    if (this.#shared()) {
      const shared = await redisStore.get(this.#sharedKey(key));
      if (shared.hit) {
        this.#entries.set(key, { value: shared.value, expiresAt: Date.now() + ttlMs });
        return shared.value;
      }
    }

    const value = await produce();
    this.#entries.set(key, { value, expiresAt: Date.now() + ttlMs });
    if (this.#shared()) await redisStore.set(this.#sharedKey(key), value, ttlMs);
    return value;
  }

  forget(key) {
    this.#entries.delete(key);
    if (this.#shared()) void redisStore.del(this.#sharedKey(key));
  }

  forgetPrefix(prefix) {
    for (const key of this.#entries.keys()) {
      if (key.startsWith(prefix)) this.#entries.delete(key);
    }
    if (this.#shared()) void redisStore.delPrefix(this.#sharedKey(prefix));
  }

  clear() {
    this.#entries.clear();
    if (this.#shared()) void redisStore.delPrefix(`${this.#name}:`);
  }
}

export const MINUTE = 60 * 1000;
export const HOUR = 60 * MINUTE;
