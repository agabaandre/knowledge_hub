/**
 * Optional shared cache backend. When REDIS_URL is set, named TtlCaches
 * read/write through Redis so several API instances share entries and
 * invalidations. Redis being down must never take the hub down: every
 * operation degrades to a cache miss and the failure is logged once.
 */
const SET_MARKER = '__khub_set__';

let prefix = 'khub:';
let client = null;
let logger = console;
let warned = false;

const warnOnce = (error) => {
  if (warned) return;
  warned = true;
  logger.warn({ err: error }, 'Redis unavailable — falling back to in-process cache only');
};

const serialize = (value) => JSON.stringify(value, (key, val) => (val instanceof Set ? { [SET_MARKER]: [...val] } : val));

const deserialize = (text) => JSON.parse(text, (key, val) => (val && typeof val === 'object' && SET_MARKER in val ? new Set(val[SET_MARKER]) : val));

/**
 * Connect at boot; without a URL the store stays inert. keyPrefix separates
 * instances that share one Redis (e.g. the MySQL and PG hubs in Docker).
 */
export const configureRedis = async (url, log, keyPrefix = 'khub') => {
  if (log) logger = log;
  if (keyPrefix) prefix = `${keyPrefix}:`;
  if (!url) return false;
  const { createClient } = await import('redis');
  const candidate = createClient({ url, socket: { reconnectStrategy: (retries) => Math.min(retries * 500, 5000) } });
  candidate.on('error', warnOnce);
  try {
    await candidate.connect();
    client = candidate;
    warned = false;
    logger.info({ url: url.replace(/\/\/[^@]*@/, '//***@') }, 'Redis cache connected');
    return true;
  } catch (error) {
    warnOnce(error);
    candidate.destroy();
    return false;
  }
};

export const disconnectRedis = async () => {
  if (!client) return;
  const closing = client;
  client = null;
  await closing.quit().catch(() => closing.destroy());
};

const ready = () => client !== null && client.isReady;

export const redisStore = {
  enabled: () => ready(),

  /** @returns {Promise<{hit: boolean, value?: unknown}>} */
  async get(key) {
    if (!ready()) return { hit: false };
    try {
      const text = await client.get(prefix + key);
      return text === null ? { hit: false } : { hit: true, value: deserialize(text) };
    } catch (error) {
      warnOnce(error);
      return { hit: false };
    }
  },

  async set(key, value, ttlMs) {
    if (!ready()) return;
    try {
      await client.set(prefix + key, serialize(value), { PX: Math.max(1, Math.round(ttlMs)) });
    } catch (error) {
      warnOnce(error);
    }
  },

  async del(key) {
    if (!ready()) return;
    try {
      await client.del(prefix + key);
    } catch (error) {
      warnOnce(error);
    }
  },

  async delPrefix(keyStart) {
    if (!ready()) return;
    try {
      for await (const keys of client.scanIterator({ MATCH: `${prefix}${keyStart}*`, COUNT: 100 })) {
        const batch = Array.isArray(keys) ? keys : [keys];
        if (batch.length) await client.del(batch);
      }
    } catch (error) {
      warnOnce(error);
    }
  },
};
