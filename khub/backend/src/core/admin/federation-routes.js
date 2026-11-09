import { Router } from 'express';
import { z } from 'zod';
import { asyncHandler, HttpError } from '../http/errors.js';
import { validate } from '../http/validate.js';
import { requirePermission } from '../auth/middleware.js';
import { now } from '../db/helpers.js';
import { FederationService } from '../federation/service.js';

const hubSchema = z.object({
  name: z.string().trim().min(2).max(150),
  base_url: z.string().trim().url().max(255),
  api_token: z.string().trim().min(8).max(255),
  direction: z.enum(['pull', 'push', 'both']).optional().default('both'),
  is_active: z.boolean().optional().default(true),
});

const serialize = (row) => ({
  id: row.id,
  name: row.name,
  baseUrl: row.base_url,
  direction: row.direction,
  isActive: Boolean(row.is_active),
  lastStatus: row.last_status,
  lastSyncedAt: row.last_synced_at,
  createdAt: row.created_at,
});

/** Federated Knowledge Hubs: register partners, test the link, and sync content. */
export const federationAdminRoutes = () => {
  const router = Router();

  router.get(
    '/federation/hubs',
    asyncHandler(async (req, res) => {
      const rows = await req.db('federated_hubs').orderBy('name');
      const imported = await req.db('publication').whereNotNull('federated_source').count({ total: '*' }).first();
      res.json({ hubs: rows.map(serialize), importedPublications: Number(imported.total) });
    }),
  );

  router.post(
    '/federation/hubs',
    requirePermission('admin.federation.manage'),
    validate(hubSchema),
    asyncHandler(async (req, res) => {
      const [inserted] = await req.db('federated_hubs').insert({ ...req.body, created_at: now(), updated_at: now() }).returning('id');
      res.status(201).json({ id: typeof inserted === 'object' ? inserted.id : inserted });
    }),
  );

  router.put(
    '/federation/hubs/:id',
    requirePermission('admin.federation.manage'),
    validate(hubSchema.partial().refine((value) => Object.keys(value).length > 0, 'Nothing to update')),
    asyncHandler(async (req, res) => {
      const changed = await req.db('federated_hubs').where({ id: Number(req.params.id) }).update({ ...req.body, updated_at: now() });
      if (!changed) throw new HttpError(404, 'Federated hub not found.');
      res.json({ ok: true });
    }),
  );

  router.delete(
    '/federation/hubs/:id',
    requirePermission('admin.federation.manage'),
    asyncHandler(async (req, res) => {
      await req.db('federated_hubs').where({ id: Number(req.params.id) }).del();
      res.status(204).end();
    }),
  );

  const loadHub = async (req) => {
    const hub = await req.db('federated_hubs').where({ id: Number(req.params.id) }).first();
    if (!hub) throw new HttpError(404, 'Federated hub not found.');
    return hub;
  };

  router.post(
    '/federation/hubs/:id/test',
    requirePermission('admin.federation.manage'),
    asyncHandler(async (req, res) => {
      const hub = await loadHub(req);
      try {
        const manifest = await new FederationService(req.site).testConnection(hub);
        await req.db('federated_hubs').where({ id: hub.id }).update({ last_status: `Connected: ${manifest.hub}`, updated_at: now() });
        res.json({ ok: true, manifest });
      } catch (error) {
        await req.db('federated_hubs').where({ id: hub.id }).update({ last_status: `Connection failed: ${error.message}`.slice(0, 500), updated_at: now() });
        res.status(502).json({ ok: false, error: error.message });
      }
    }),
  );

  router.post(
    '/federation/hubs/:id/sync',
    requirePermission('admin.federation.manage'),
    asyncHandler(async (req, res) => {
      const hub = await loadHub(req);
      if (!hub.is_active) throw new HttpError(422, 'This hub is inactive — activate it before syncing.');
      try {
        const result = await new FederationService(req.site).sync(hub);
        const summary = `Synced: ${result.pulled ?? 0} pulled (${result.created ?? 0} new), ${result.pushed ?? 0} pushed`;
        await req.db('federated_hubs').where({ id: hub.id }).update({ last_status: summary, last_synced_at: now(), updated_at: now() });
        res.json({ ok: true, ...result, summary });
      } catch (error) {
        await req.db('federated_hubs').where({ id: hub.id }).update({ last_status: `Sync failed: ${error.message}`.slice(0, 500), updated_at: now() });
        res.status(502).json({ ok: false, error: error.message });
      }
    }),
  );

  return router;
};
