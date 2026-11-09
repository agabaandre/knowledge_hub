import { Router } from 'express';
import { z } from 'zod';
import { asyncHandler } from '../http/errors.js';
import { validate } from '../http/validate.js';
import { requirePermission } from '../auth/middleware.js';
import { HubStorage } from '../storage/hub-storage.js';
import { now } from '../db/helpers.js';

const FOLDERS = [
  ['uploads/publications', 'Publications & attachments'],
  ['uploads/users', 'User photos'],
  ['uploads/config', 'Branding & configuration'],
  ['uploads/forums', 'Forum images'],
  ['themes', 'Uploaded themes'],
  ['backups', 'Backups'],
];

const settingsSchema = z.object({
  auto_sql_backup: z.boolean().optional(),
  sql_backup_retention_days: z.coerce.number().int().min(1).max(365).optional(),
}).refine((value) => Object.keys(value).length > 0, 'Nothing to update');

/** Storage Management: per-site usage breakdown and backup policy (hub_storage_settings). */
export const storageRoutes = ({ sites }) => {
  const router = Router();

  router.get(
    '/storage',
    asyncHandler(async (req, res) => {
      const report = [];
      for (const site of sites.all()) {
        const storage = new HubStorage(site);
        const folders = [];
        for (const [relative, label] of FOLDERS) {
          const usage = await storage.usage(relative);
          folders.push({ path: relative, label, ...usage });
        }
        const total = await storage.usage();
        const settings = await site.db('hub_storage_settings').where({ site_storage_id: site.storageId }).first().catch(() => null);
        report.push({
          id: site.id,
          name: site.name,
          storageId: site.storageId,
          current: site.id === req.site.id,
          totalBytes: total.bytes,
          totalFiles: total.files,
          folders,
          backup: settings ? {
            autoSqlBackup: Boolean(settings.auto_sql_backup),
            retentionDays: Number(settings.sql_backup_retention_days ?? 14),
            lastBackupAt: settings.last_sql_backup_at,
          } : null,
        });
      }
      res.json({ sites: report });
    }),
  );

  router.put(
    '/storage/:storageId',
    requirePermission('admin.storage.manage'),
    validate(settingsSchema),
    asyncHandler(async (req, res) => {
      const storageId = req.params.storageId;
      const existing = await req.db('hub_storage_settings').where({ site_storage_id: storageId }).first('id');
      if (existing) await req.db('hub_storage_settings').where({ id: existing.id }).update({ ...req.body, updated_at: now() });
      else await req.db('hub_storage_settings').insert({ site_storage_id: storageId, files_driver: 'local', ...req.body, created_at: now(), updated_at: now() });
      res.json({ ok: true });
    }),
  );

  return router;
};
