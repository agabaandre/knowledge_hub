import { Router } from 'express';
import { z } from 'zod';
import { asyncHandler } from '../http/errors.js';
import { validate } from '../http/validate.js';
import { requirePermission } from '../auth/middleware.js';
import { publicSettings } from './public-settings.js';
import { ThemeService } from '../themes/service.js';
import { userPhotoUrl } from '../../modules/publications/serializer.js';

const toggleSchema = z.object({ enabled: z.boolean() });

/**
 * GET /api/site/manifest           what the frontend needs to boot: settings, modules, locales, features
 * GET /api/admin/modules           module catalogue with enabled state
 * PUT /api/admin/modules/:name     switch a module on/off for this site
 */
export const siteRouter = ({ sites, modules, settings, i18n }) => {
  const router = Router();

  router.get(
    '/api/site/manifest',
    asyncHandler(async (req, res) => {
      const [siteSettings, moduleList] = await Promise.all([settings.forSite(req.site), modules.manifest(sites, req.site)]);
      const theme = await new ThemeService(req.site).active(siteSettings);
      const [languages, locale] = await Promise.all([i18n.languages(req.site), i18n.resolveLocale(req)]);
      res.json({
        theme: { slug: theme.slug, tokens: theme.tokens, stylesheet: theme.stylesheet ?? null },
        languages,
        locale,
        site: { id: req.site.id, name: req.site.name, pathPrefix: req.site.pathPrefix, publicUrl: req.site.publicUrl, locales: req.site.locales, features: req.site.features },
        settings: publicSettings(siteSettings),
        modules: Object.fromEntries(moduleList.map((module) => [module.name, module.enabled && module.implemented])),
        user: req.user
          ? {
              id: req.user.id,
              name: req.user.name,
              email: req.user.email,
              organization: req.user.organization_name ?? '',
              photo: userPhotoUrl(req.site, req.user),
              isAdmin: req.user.access.isAdmin,
              permissions: req.user.permissions,
              access: req.user.access,
            }
          : null,
      });
    }),
  );

  router.get(
    '/api/admin/modules',
    requirePermission('admin.modules.manage'),
    asyncHandler(async (req, res) => res.json({ modules: await modules.manifest(sites, req.site) })),
  );

  router.put(
    '/api/admin/modules/:name',
    requirePermission('admin.modules.manage'),
    validate(toggleSchema),
    asyncHandler(async (req, res) => {
      await sites.setModuleEnabled(req.site, req.params.name, req.body.enabled, req.user.id);
      res.json({ modules: await modules.manifest(sites, req.site) });
    }),
  );

  router.post(
    '/api/admin/settings/clear-cache',
    requirePermission('admin.settings.edit'),
    (req, res) => {
      settings.forget(req.site);
      res.status(204).end();
    },
  );

  return router;
};
