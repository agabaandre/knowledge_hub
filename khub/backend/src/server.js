import path from 'node:path';
import { loadEnv } from './core/config/env.js';
import { createLogger } from './core/logger.js';
import { ConnectionRegistry } from './core/db/connection.js';
import { SiteRegistry } from './core/site/registry.js';
import { SettingsService } from './core/site/settings-service.js';
import { ModuleRegistry } from './core/modules/registry.js';
import { MetricsCollector } from './core/metrics/collector.js';
import { I18nService } from './core/i18n/service.js';
import { HubStorage, mediaUrl } from './core/storage/hub-storage.js';
import { createApp } from './core/http/app.js';
import { startRetentionSweeps } from './core/documents/service.js';
import { configureRedis, disconnectRedis } from './core/redis-store.js';

/** Wire every core service together; modules receive this context. */
export const bootstrap = async (overrides = {}) => {
  const env = overrides.env ?? loadEnv();
  const logger = overrides.logger ?? createLogger(env);
  const connections = new ConnectionRegistry();
  await configureRedis(env.redisUrl, logger, env.redisPrefix);

  const sites = new SiteRegistry({ env, connections, logger });
  await sites.load();

  for (const site of sites.all()) {
    await new HubStorage(site).ensureDirectories().catch((error) => logger.warn({ site: site.id, err: error }, 'could not create storage directories'));
  }

  const modules = new ModuleRegistry({ modulesDir: path.join(env.backendRoot, 'src', 'modules'), logger });
  await modules.load();

  const context = { env, logger, connections, sites, modules, settings: new SettingsService(mediaUrl), metrics: new MetricsCollector(), i18n: new I18nService(env) };
  const app = createApp(context);
  startRetentionSweeps(context);
  return { app, context };
};

const isMain = process.argv[1] && import.meta.url === new URL(`file://${path.resolve(process.argv[1])}`).href;

if (isMain) {
  const { app, context } = await bootstrap();
  const server = app.listen(context.env.port, () => context.logger.info(`API listening on http://localhost:${context.env.port}`));

  const shutdown = async (signal) => {
    context.logger.info({ signal }, 'shutting down');
    server.close();
    await disconnectRedis();
    await context.connections.destroyAll();
    process.exit(0);
  };
  process.on('SIGINT', shutdown);
  process.on('SIGTERM', shutdown);
}
