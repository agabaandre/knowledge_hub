import { Router } from 'express';
import { asyncHandler, HttpError } from '../http/errors.js';
import { requirePermission } from '../auth/middleware.js';
import { HubStorage } from '../storage/hub-storage.js';
import { CONTENT_PREFIXES } from '../storage/paths.js';
import { TtlCache, MINUTE } from '../cache.js';

const snapshotCache = new TtlCache();

/**
 * GET /metrics            Prometheus text format (optional bearer token)
 * GET /api/admin/metrics  JSON overview for the admin dashboard (?fresh=1 bypasses the 5-minute cache)
 */
export const metricsRouter = ({ env, collector }) => {
  const router = Router();

  router.get(
    '/metrics',
    asyncHandler(async (req, res) => {
      if (env.metrics.token && req.get('authorization') !== `Bearer ${env.metrics.token}`) {
        throw new HttpError(401, 'Metrics token required');
      }
      res.setHeader('Content-Type', collector.registry.contentType);
      res.send(await collector.registry.metrics());
    }),
  );

  router.get(
    '/api/admin/metrics',
    requirePermission('admin.metrics.view'),
    asyncHandler(async (req, res) => {
      const site = req.site;
      const key = `metrics:${site.id}`;
      if (req.query.fresh === '1') snapshotCache.forget(key);

      const snapshot = await snapshotCache.remember(key, 5 * MINUTE, async () => {
        const storage = new HubStorage(site);
        const [database, disk, uploads, backups] = await Promise.all([
          collector.databaseSnapshot(site),
          collector.diskSnapshot(site.paths.siteRoot),
          storage.usage(),
          storage.usage('..').catch(() => null),
        ]);
        const areas = {};
        for (const [name, prefix] of Object.entries(CONTENT_PREFIXES)) {
          areas[name] = await storage.usage(prefix);
        }
        return {
          generatedAt: new Date().toISOString(),
          site: { id: site.id, storageId: site.storageId, publicUrl: site.publicUrl, paths: site.paths },
          runtime: collector.runtimeSnapshot(),
          database,
          disk,
          uploads: { ...uploads, areas },
          backups: backups ? { bytes: backups.bytes, files: backups.files } : null,
        };
      });

      res.json(snapshot);
    }),
  );

  return router;
};
