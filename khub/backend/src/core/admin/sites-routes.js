import fs from 'node:fs/promises';
import path from 'node:path';
import { Router } from 'express';
import { z } from 'zod';
import { asyncHandler, HttpError } from '../http/errors.js';
import { validate } from '../http/validate.js';
import { requirePermission } from '../auth/middleware.js';
import { HubStorage } from '../storage/hub-storage.js';

const createSchema = z.object({
  id: z.string().trim().toLowerCase().regex(/^[a-z0-9][a-z0-9-]{1,40}$/, 'Lowercase letters, digits, and dashes only.'),
  name: z.string().trim().min(3).max(120),
  pathPrefix: z.string().trim().regex(/^\/[a-z0-9-]+$/, 'Like /nigeria').optional(),
  locales: z.array(z.string().trim().toLowerCase().length(2)).min(1).optional().default(['en']),
});

/**
 * Hub Sites: the multi-site registry (sites/<id>/site.json). Creating a site
 * writes its config, provisions its storage folders, and reloads the registry —
 * the new site inherits the default database until its own is configured.
 */
export const sitesRoutes = ({ sites, env }) => {
  const router = Router();

  router.get(
    '/sites',
    asyncHandler(async (req, res) => {
      const entries = await fs.readdir(env.sitesDir, { withFileTypes: true });
      const report = [];
      for (const entry of entries) {
        if (!entry.isDirectory()) continue;
        const config = JSON.parse(await fs.readFile(path.join(env.sitesDir, entry.name, 'site.json'), 'utf8').catch(() => 'null'));
        if (!config) continue;
        report.push({
          id: config.id ?? entry.name,
          name: config.name ?? entry.name,
          enabled: config.enabled !== false,
          pathPrefix: config.pathPrefix ?? '',
          hosts: config.hosts ?? [],
          storageId: config.storageId ?? entry.name,
          locales: config.locales ?? ['en'],
          current: (config.id ?? entry.name) === req.site.id,
        });
      }
      res.json({ sites: report });
    }),
  );

  router.post(
    '/sites',
    requirePermission('admin.sites.manage'),
    validate(createSchema),
    asyncHandler(async (req, res) => {
      const { id, name, pathPrefix, locales } = req.body;
      const directory = path.join(env.sitesDir, id);
      if (await fs.access(path.join(directory, 'site.json')).then(() => true).catch(() => false)) {
        throw new HttpError(422, `A site called "${id}" already exists.`);
      }
      const config = {
        id,
        name,
        enabled: true,
        pathPrefix: pathPrefix ?? `/${id}`,
        hosts: [],
        storageId: id,
        locales,
        branding: {},
        modules: {},
        features: {},
      };
      await fs.mkdir(directory, { recursive: true });
      await fs.writeFile(path.join(directory, 'site.json'), `${JSON.stringify(config, null, 2)}\n`);
      await sites.load();
      const site = sites.all().find((entry) => entry.id === id);
      if (site) await new HubStorage(site).ensureDirectories();
      res.status(201).json({ ok: true, id, pathPrefix: config.pathPrefix });
    }),
  );

  router.put(
    '/sites/:id',
    requirePermission('admin.sites.manage'),
    validate(z.object({ enabled: z.boolean() })),
    asyncHandler(async (req, res) => {
      if (req.params.id === 'default') throw new HttpError(422, 'The default site cannot be disabled.');
      const file = path.join(env.sitesDir, req.params.id, 'site.json');
      const config = JSON.parse(await fs.readFile(file, 'utf8').catch(() => { throw new HttpError(404, 'Site not found.'); }));
      config.enabled = req.body.enabled;
      await fs.writeFile(file, `${JSON.stringify(config, null, 2)}\n`);
      await sites.load();
      res.json({ ok: true, enabled: req.body.enabled });
    }),
  );

  return router;
};
