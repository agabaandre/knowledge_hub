import path from 'node:path';
import { fileURLToPath } from 'node:url';

const backendRoot = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '../../..');

const bool = (value, fallback) => {
  if (value === undefined || value === '') return fallback;
  return ['1', 'true', 'yes', 'on'].includes(String(value).toLowerCase());
};

const int = (value, fallback) => {
  const parsed = Number.parseInt(value ?? '', 10);
  return Number.isNaN(parsed) ? fallback : parsed;
};

const DB_CLIENTS = new Set(['mysql2', 'pg']);

export const dbClientFromEnv = (client) => {
  const normalized = { mysql: 'mysql2', pgsql: 'pg', postgres: 'pg', postgresql: 'pg' }[client] ?? client;
  if (!DB_CLIENTS.has(normalized)) {
    throw new Error(`Unsupported DB client "${client}". Use mysql2 or pg.`);
  }
  return normalized;
};

/**
 * Typed view of process.env. Read once at boot so a missing value fails early.
 */
export const loadEnv = (env = process.env) => {
  const nodeEnv = env.NODE_ENV ?? 'development';
  const secret = env.APP_SECRET ?? '';
  if (nodeEnv === 'production' && (secret === '' || secret === 'change-me')) {
    throw new Error('APP_SECRET must be set to a random value in production');
  }

  return Object.freeze({
    nodeEnv,
    isProduction: nodeEnv === 'production',
    appUrl: (env.APP_URL ?? 'http://localhost:3000').replace(/\/+$/, ''),
    port: int(env.BACKEND_PORT, 4000),
    secret: secret || 'development-only-secret',
    backendRoot,
    sitesDir: path.resolve(backendRoot, '..', env.SITES_DIR ?? 'sites'),
    frontendDist: env.FRONTEND_DIST ? path.resolve(env.FRONTEND_DIST) : path.resolve(backendRoot, '..', 'frontend', 'dist'),
    siteResolution: env.SITE_RESOLUTION === 'host' ? 'host' : 'path',
    hubDataRoot: env.HUB_DATA_ROOT ?? (process.platform === 'win32' ? 'C:\\khubdata' : '/var/khubdata'),
    hubSiteId: env.HUB_SITE_ID ?? '',
    database: Object.freeze({
      client: dbClientFromEnv(env.DB_CLIENT ?? 'mysql2'),
      host: env.DB_HOST ?? '127.0.0.1',
      port: int(env.DB_PORT, env.DB_CLIENT === 'pg' ? 5432 : 3306),
      database: env.DB_DATABASE ?? 'knowledge_hub',
      user: env.DB_USERNAME ?? 'root',
      password: env.DB_PASSWORD ?? '',
    }),
    metrics: Object.freeze({
      enabled: bool(env.METRICS_ENABLED, true),
      token: env.METRICS_TOKEN ?? '',
    }),
    logLevel: env.LOG_LEVEL ?? (nodeEnv === 'production' ? 'info' : 'debug'),
  });
};
