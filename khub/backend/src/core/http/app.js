import express from 'express';
import helmet from 'helmet';
import compression from 'compression';
import cookieParser from 'cookie-parser';
import pinoHttp from 'pino-http';
import { siteMiddleware } from '../site/middleware.js';
import { siteRouter } from '../site/routes.js';
import { authenticate } from '../auth/middleware.js';
import { ipBlockMiddleware } from '../security/ip-block.js';
import { authRouter } from '../auth/routes.js';
import { accountRouter } from '../auth/account-routes.js';
import { activityRouter } from '../auth/activity-routes.js';
import { mediaRouter } from '../storage/media-route.js';
import { metricsRouter } from '../metrics/routes.js';
import { seoRouter } from '../seo/routes.js';
import { adminRouter } from '../admin/routes.js';
import { themesRouter } from '../themes/routes.js';
import { i18nRouter } from '../i18n/routes.js';
import path from 'node:path';
import fs from 'node:fs';
import { errorHandler, HttpError } from './errors.js';

/**
 * Builds the Express app. Order matters:
 *   security/parsing → site resolution → auth → core routes → feature modules → errors
 */
export const createApp = (context) => {
  const { env, logger, sites, modules, settings, metrics, i18n } = context;
  const app = express();

  app.set('trust proxy', 1);
  app.disable('x-powered-by');
  app.use(helmet({ contentSecurityPolicy: false, crossOriginResourcePolicy: { policy: 'cross-origin' } }));
  app.use(compression());
  app.use(express.json({ limit: '1mb' }));
  app.use(express.urlencoded({ extended: false }));
  app.use(cookieParser());
  app.use(pinoHttp({ logger, autoLogging: { ignore: (req) => req.url === '/health' || req.url === '/metrics' } }));

  app.get('/health', (req, res) => res.json({ status: 'ok', uptime: Math.round(process.uptime()) }));

  app.use(siteMiddleware(sites));
  app.use((req, res, next) => { req.settingsService = settings; next(); });
  app.use(authenticate(env.secret));
  app.use(ipBlockMiddleware());
  if (env.metrics.enabled) app.use(metrics.httpMiddleware());

  app.use(seoRouter({ sites, settings }));
  app.use(mediaRouter());
  app.use(siteRouter({ sites, modules, settings, i18n }));
  app.use(i18nRouter({ i18n, env }));
  app.use(adminRouter(context));
  app.use(themesRouter({ env, settings }));
  app.use('/api/auth', authRouter({ env }));
  app.use('/api/account', accountRouter());
  app.use('/api/account', activityRouter());
  if (env.metrics.enabled) app.use(metricsRouter({ env, collector: metrics }));

  for (const definition of modules.all()) {
    app.use(`/api/${definition.name}`, modules.guarded(definition, context));
  }

  // Serve the built React app (and its SPA fallback) for every non-API page request.
  const dist = env.frontendDist;
  if (dist && fs.existsSync(dist)) {
    app.use(express.static(dist, { index: false, maxAge: '1h' }));
    app.get(/^\/(?!api\/|hub-media\/|hub-theme\/|metrics|health).*/, (req, res, next) => {
      if (req.method !== 'GET' && req.method !== 'HEAD') return next();
      res.sendFile(path.join(dist, 'index.html'));
    });
  }

  app.use((req, res, next) => next(new HttpError(404, `No route for ${req.method} ${req.originalUrl}`)));
  app.use(errorHandler({ logger, isProduction: env.isProduction }));

  return app;
};
