/**
 * Small in-process TTL cache. Laravel used Redis for settings/menu counts; a
 * per-process cache is enough here because every value is cheap to rebuild.
 * Swap for Redis behind the same interface if several API instances run.
 */
export class TtlCache {
  #entries = new Map();

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

    const value = await produce();
    this.#entries.set(key, { value, expiresAt: Date.now() + ttlMs });
    return value;
  }

  forget(key) {
    this.#entries.delete(key);
  }

  forgetPrefix(prefix) {
    for (const key of this.#entries.keys()) {
      if (key.startsWith(prefix)) this.#entries.delete(key);
    }
  }

  clear() {
    this.#entries.clear();
  }
}

export const MINUTE = 60 * 1000;
export const HOUR = 60 * MINUTE;
