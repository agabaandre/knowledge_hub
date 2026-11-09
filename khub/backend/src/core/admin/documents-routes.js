import { Router } from 'express';
import { z } from 'zod';
import { asyncHandler, HttpError } from '../http/errors.js';
import { validate } from '../http/validate.js';
import { requirePermission } from '../auth/middleware.js';
import { likePattern, now } from '../db/helpers.js';
import { DocumentsService, attachmentRelativePath } from '../documents/service.js';
import { publicationFileUrl } from '../../modules/publications/serializer.js';

const updateSchema = z.object({
  description: z.string().trim().max(500).optional(),
  retention_until: z.string().trim().regex(/^\d{4}-\d{2}-\d{2}$/).nullable().optional(), // "delete after" date; null clears it
}).refine((value) => Object.keys(value).length > 0, 'Nothing to update');

const serialize = (site, row) => ({
  id: row.id,
  name: row.original_filename || row.description || row.file,
  description: row.description ?? '',
  file: row.file,
  url: publicationFileUrl(site, row.file),
  external: attachmentRelativePath(row.file) === null,
  publication: row.publication_id ? { id: row.publication_id, title: row.publication_title, slug: row.publication_slug } : null,
  checksum: row.checksum_sha256,
  sizeBytes: row.size_bytes === null ? null : Number(row.size_bytes),
  mimeType: row.mime_type,
  docVersion: Number(row.doc_version ?? 1),
  uploadedBy: row.uploader_name ?? null,
  retentionUntil: row.retention_until,
  createdAt: row.created_at,
});

/** Document registry: every stored attachment with integrity + retention controls. */
export const documentsRoutes = () => {
  const router = Router();

  const baseQuery = (db) => db('publication_attachments')
    .leftJoin('publication', 'publication.id', 'publication_attachments.publication_id')
    .leftJoin('users as uploader', 'uploader.id', 'publication_attachments.uploaded_by')
    .select('publication_attachments.*', 'publication.title as publication_title', 'publication.slug as publication_slug', 'uploader.name as uploader_name');

  router.get(
    '/documents',
    asyncHandler(async (req, res) => {
      const term = String(req.query.term ?? '').trim();
      const query = baseQuery(req.db).orderBy('publication_attachments.id', 'desc').limit(500);
      if (term) {
        query.where((builder) => builder
          .whereILike('publication_attachments.original_filename', likePattern(term))
          .orWhereILike('publication_attachments.description', likePattern(term))
          .orWhereILike('publication.title', likePattern(term)));
      }
      const rows = await query;
      res.json({ documents: rows.map((row) => serialize(req.site, row)) });
    }),
  );

  router.put(
    '/documents/:id',
    requirePermission('admin.documents.manage'),
    validate(updateSchema),
    asyncHandler(async (req, res) => {
      const updates = { updated_at: now() };
      if ('description' in req.body) updates.description = req.body.description;
      if ('retention_until' in req.body) updates.retention_until = req.body.retention_until;
      const changed = await req.db('publication_attachments').where({ id: Number(req.params.id) }).update(updates);
      if (!changed) throw new HttpError(404, 'Document not found.');
      res.json({ ok: true });
    }),
  );

  router.post(
    '/documents/:id/verify',
    requirePermission('admin.documents.manage'),
    asyncHandler(async (req, res) => {
      const row = await req.db('publication_attachments').where({ id: Number(req.params.id) }).first();
      if (!row) throw new HttpError(404, 'Document not found.');
      res.json(await new DocumentsService(req.site).verify(row));
    }),
  );

  router.delete(
    '/documents/:id',
    requirePermission('admin.documents.manage'),
    asyncHandler(async (req, res) => {
      const row = await req.db('publication_attachments').where({ id: Number(req.params.id) }).first();
      if (!row) throw new HttpError(404, 'Document not found.');
      await new DocumentsService(req.site).dispose(row);
      res.status(204).end();
    }),
  );

  // Run the retention/backfill pass on demand.
  router.post(
    '/documents/sweep',
    requirePermission('admin.documents.manage'),
    asyncHandler(async (req, res) => {
      res.json(await new DocumentsService(req.site).sweep());
    }),
  );

  // CSV export of the registry.
  router.get(
    '/documents/export',
    asyncHandler(async (req, res) => {
      const rows = await baseQuery(req.db).orderBy('publication_attachments.id', 'desc');
      const cell = (value) => {
        const text = String(value ?? '');
        return /[",\n]/.test(text) ? `"${text.replace(/"/g, '""')}"` : text;
      };
      const header = ['ID', 'Name', 'Publication', 'Size (bytes)', 'MIME type', 'SHA-256', 'Version', 'Uploaded by', 'Delete after'];
      const lines = rows.map((row) => {
        const item = serialize(req.site, row);
        return [item.id, item.name, item.publication?.title ?? '', item.sizeBytes ?? '', item.mimeType ?? '',
          item.checksum ?? '', item.docVersion, item.uploadedBy ?? '',
          item.retentionUntil ? new Date(item.retentionUntil).toISOString().slice(0, 10) : ''].map(cell).join(',');
      });
      res.setHeader('Content-Type', 'text/csv; charset=utf-8');
      res.setHeader('Content-Disposition', `attachment; filename="documents-${new Date().toISOString().slice(0, 10)}.csv"`);
      res.send(['﻿' + header.join(','), ...lines].join('\n'));
    }),
  );

  return router;
};
