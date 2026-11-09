import { Router } from 'express';
import path from 'node:path';
import fs from 'node:fs/promises';
import multer from 'multer';
import { asyncHandler, HttpError } from '../http/errors.js';
import { requirePermission } from '../auth/middleware.js';
import { ThemeService } from './service.js';

const upload = multer({ storage: multer.memoryStorage(), limits: { fileSize: 12 * 1024 * 1024 } });
const guidelinesPath = (env) => path.resolve(env.backendRoot, '..', 'docs', 'THEME_GUIDELINES.md');

/**
 * /hub-theme/{slug}/{file}        public theme assets
 * /api/admin/themes ...           list, upload, activate, delete, guidelines
 */
export const themesRouter = ({ env, settings }) => {
  const router = Router();

  router.get(
    /^\/hub-theme\/([a-z0-9-]+)\/(.+)$/,
    asyncHandler(async (req, res) => {
      const absolute = await new ThemeService(req.site).resolveFile(req.params[0], decodeURIComponent(req.params[1]));
      if (!absolute) throw new HttpError(404, 'Theme file not found');
      res.setHeader('Cache-Control', 'public, max-age=300');
      res.sendFile(absolute);
    }),
  );

  router.get(
    '/api/admin/themes',
    requirePermission('admin.themes.manage'),
    asyncHandler(async (req, res) => {
      const service = new ThemeService(req.site);
      const siteSettings = await settings.forSite(req.site);
      const [themes, active] = await Promise.all([service.list(), service.active(siteSettings)]);
      res.json({ themes, active: active.slug });
    }),
  );

  router.get(
    '/api/admin/themes/guidelines',
    requirePermission('admin.themes.manage'),
    asyncHandler(async (req, res) => {
      res.json({ markdown: await fs.readFile(guidelinesPath(env), 'utf8') });
    }),
  );

  router.post(
    '/api/admin/themes/upload',
    requirePermission('admin.themes.manage'),
    upload.single('theme'),
    asyncHandler(async (req, res) => {
      if (!req.file) throw new HttpError(422, 'Attach the theme zip as the "theme" field.');
      const manifest = await new ThemeService(req.site).installZip(req.file.buffer);
      res.status(201).json({ theme: manifest });
    }),
  );

  router.post(
    '/api/admin/themes/:slug/activate',
    requirePermission('admin.themes.manage'),
    asyncHandler(async (req, res) => {
      await new ThemeService(req.site).activate(req.params.slug);
      settings.forget(req.site);
      res.json({ active: req.params.slug });
    }),
  );

  router.delete(
    '/api/admin/themes/:slug',
    requirePermission('admin.themes.manage'),
    asyncHandler(async (req, res) => {
      await new ThemeService(req.site).remove(req.params.slug, await settings.forSite(req.site));
      res.status(204).end();
    }),
  );

  return router;
};
