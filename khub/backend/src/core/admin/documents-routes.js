import { Router } from 'express';
import { z } from 'zod';
import { asyncHandler, HttpError } from '../http/errors.js';
import { validate } from '../http/validate.js';
import { requirePermission } from '../auth/middleware.js';
import { likePattern, now } from '../db/helpers.js';
import { DocumentsService, attachmentRelativePath } from '../documents/service.js';
import { publicationFileUrl } from '../../modules/publications/serializer.js';

const listSchema = z.object({
  term: z.string().trim().max(200).optional().default(''),
  kind: z.enum(['all', 'primary', 'attachment']).optional().default('all'),
  view: z.enum(['active', 'trash']).optional().default('active'),
  page: z.coerce.number().int().min(1).optional().default(1),
  per_page: z.coerce.number().int().min(1).max(50).optional().default(12),
});

const updateSchema = z.object({
  description: z.string().trim().max(500).optional(),
  retention_until: z.string().trim().regex(/^\d{4}-\d{2}-\d{2}$/).nullable().optional(), // "delete after" date; null clears it
}).refine((value) => Object.keys(value).length > 0, 'Nothing to update');

const serializeAttachment = (site, row) => ({
  id: row.id,
  kind: 'attachment',
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
  trashedAt: row.deleted_at ?? null,
  createdAt: row.created_at,
});

const serializePrimary = (site, row) => ({
  id: row.id,
  kind: 'primary',
  name: row.file,
  description: 'Primary resource file',
  file: row.file,
  url: publicationFileUrl(site, row.file),
  external: attachmentRelativePath(row.file) === null,
  publication: { id: row.id, title: row.title, slug: row.slug },
  checksum: row.file_checksum_sha256,
  sizeBytes: row.file_size_bytes === null ? null : Number(row.file_size_bytes),
  mimeType: row.file_mime_type,
  docVersion: Number(row.version_no ?? 1) || 1,
  uploadedBy: row.uploader_name ?? null,
  retentionUntil: null,
  trashedAt: row.deleted_at ?? null,
  createdAt: row.created_at,
});

const fetchRegistry = async (req, { term, kind, view }) => {
  const trashed = view === 'trash';
  const documents = [];

  if (kind !== 'primary') {
    const query = req.db('publication_attachments')
      .leftJoin('publication', 'publication.id', 'publication_attachments.publication_id')
      .leftJoin('users as uploader', 'uploader.id', 'publication_attachments.uploaded_by')
      .modify((builder) => (trashed ? builder.whereNotNull('publication_attachments.deleted_at') : builder.whereNull('publication_attachments.deleted_at')))
      .orderBy('publication_attachments.id', 'desc').limit(1000)
      .select('publication_attachments.*', 'publication.title as publication_title', 'publication.slug as publication_slug', 'uploader.name as uploader_name');
    if (term) {
      query.where((builder) => builder
        .whereILike('publication_attachments.original_filename', likePattern(term))
        .orWhereILike('publication_attachments.description', likePattern(term))
        .orWhereILike('publication.title', likePattern(term)));
    }
    documents.push(...(await query).map((row) => serializeAttachment(req.site, row)));
  }

  if (kind !== 'attachment') {
    const query = req.db('publication')
      .leftJoin('users as uploader', 'uploader.id', 'publication.user_id')
      .whereNotNull('publication.publication').whereNot('publication.publication', '')
      .modify((builder) => (trashed ? builder.whereNotNull('publication.deleted_at') : builder.whereNull('publication.deleted_at')))
      .orderBy('publication.id', 'desc').limit(1000)
      .select('publication.id', 'publication.title', 'publication.slug', 'publication.publication as file', 'publication.deleted_at',
        'publication.file_checksum_sha256', 'publication.file_size_bytes', 'publication.file_mime_type',
        'publication.version_no', 'publication.created_at', 'uploader.name as uploader_name');
    if (term) query.where((builder) => builder.whereILike('publication.title', likePattern(term)).orWhereILike('publication.publication', likePattern(term)));
    documents.push(...(await query).map((row) => serializePrimary(req.site, row)));
  }

  documents.sort((a, b) => new Date(b.createdAt ?? 0) - new Date(a.createdAt ?? 0));
  return documents;
};

/** Document registry: searchable, paginated, trash-aware, with duplicate detection. */
export const documentsRoutes = () => {
  const router = Router();

  router.get(
    '/documents',
    asyncHandler(async (req, res) => {
      const filters = listSchema.parse(req.query);
      const all = await fetchRegistry(req, filters);
      const start = (filters.page - 1) * filters.per_page;
      res.json({
        data: all.slice(start, start + filters.per_page),
        meta: { page: filters.page, perPage: filters.per_page, total: all.length, lastPage: Math.max(1, Math.ceil(all.length / filters.per_page)) },
      });
    }),
  );

  // Duplicate detection: identical content (SHA-256) or identical publication titles.
  router.get(
    '/documents/duplicates',
    asyncHandler(async (req, res) => {
      const all = await fetchRegistry(req, { term: '', kind: 'all', view: 'active' });
      const byChecksum = new Map();
      for (const doc of all) {
        if (!doc.checksum) continue;
        if (!byChecksum.has(doc.checksum)) byChecksum.set(doc.checksum, []);
        byChecksum.get(doc.checksum).push(doc);
      }
      const checksumGroups = [...byChecksum.entries()]
        .filter(([, docs]) => docs.length > 1)
        .map(([checksum, docs]) => ({ type: 'checksum', key: checksum, documents: docs }));

      const titles = await req.db('publication')
        .whereNull('deleted_at').where('is_version', false)
        .select('id', 'title', 'slug', 'created_at')
        .orderBy('id');
      const byTitle = new Map();
      for (const pub of titles) {
        const key = String(pub.title ?? '').trim().toLowerCase();
        if (!key) continue;
        if (!byTitle.has(key)) byTitle.set(key, []);
        byTitle.get(key).push(pub);
      }
      const titleGroups = [...byTitle.values()]
        .filter((pubs) => pubs.length > 1)
        .map((pubs) => ({ type: 'title', key: pubs[0].title, publications: pubs }));

      res.json({ checksumGroups, titleGroups });
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

  // Integrity checks.
  router.post(
    '/documents/:id/verify',
    requirePermission('admin.documents.manage'),
    asyncHandler(async (req, res) => {
      const row = await req.db('publication_attachments').where({ id: Number(req.params.id) }).first();
      if (!row) throw new HttpError(404, 'Document not found.');
      res.json(await new DocumentsService(req.site).verify(row));
    }),
  );
  router.post(
    '/documents/primary/:id/verify',
    requirePermission('admin.documents.manage'),
    asyncHandler(async (req, res) => {
      const pub = await req.db('publication').where({ id: Number(req.params.id) }).first();
      if (!pub) throw new HttpError(404, 'Publication not found.');
      res.json(await new DocumentsService(req.site).verifyPrimary(pub));
    }),
  );

  // Delete = trash (recoverable); destroy = permanent; restore undoes a trash.
  router.delete(
    '/documents/:id',
    requirePermission('admin.documents.manage'),
    asyncHandler(async (req, res) => {
      const changed = await req.db('publication_attachments').where({ id: Number(req.params.id) }).whereNull('deleted_at').update({ deleted_at: now(), updated_at: now() });
      if (!changed) throw new HttpError(404, 'Document not found (or already in the trash).');
      res.json({ ok: true, trashed: true });
    }),
  );
  router.post(
    '/documents/:id/restore',
    requirePermission('admin.documents.manage'),
    asyncHandler(async (req, res) => {
      const changed = await req.db('publication_attachments').where({ id: Number(req.params.id) }).whereNotNull('deleted_at').update({ deleted_at: null, updated_at: now() });
      if (!changed) throw new HttpError(404, 'Nothing to restore.');
      res.json({ ok: true });
    }),
  );
  router.delete(
    '/documents/:id/destroy',
    requirePermission('admin.documents.manage'),
    asyncHandler(async (req, res) => {
      const row = await req.db('publication_attachments').where({ id: Number(req.params.id) }).first();
      if (!row) throw new HttpError(404, 'Document not found.');
      await new DocumentsService(req.site).dispose(row);
      res.status(204).end();
    }),
  );

  // Run the retention / trash auto-clean pass on demand.
  router.post(
    '/documents/sweep',
    requirePermission('admin.documents.manage'),
    asyncHandler(async (req, res) => {
      res.json(await new DocumentsService(req.site).sweep());
    }),
  );

  // CSV export of the registry (current filters apply).
  router.get(
    '/documents/export',
    asyncHandler(async (req, res) => {
      const filters = listSchema.parse(req.query);
      const rows = await fetchRegistry(req, filters);
      const cell = (value) => {
        const text = String(value ?? '');
        return /[",\n]/.test(text) ? `"${text.replace(/"/g, '""')}"` : text;
      };
      const header = ['ID', 'Kind', 'Name', 'Publication', 'Size (bytes)', 'MIME type', 'SHA-256', 'Version', 'Uploaded by', 'Delete after'];
      const lines = rows.map((item) => [item.id, item.kind, item.name, item.publication?.title ?? '', item.sizeBytes ?? '', item.mimeType ?? '',
        item.checksum ?? '', item.docVersion, item.uploadedBy ?? '',
        item.retentionUntil ? new Date(item.retentionUntil).toISOString().slice(0, 10) : ''].map(cell).join(','));
      res.setHeader('Content-Type', 'text/csv; charset=utf-8');
      res.setHeader('Content-Disposition', `attachment; filename="documents-${new Date().toISOString().slice(0, 10)}.csv"`);
      res.send(['﻿' + header.join(','), ...lines].join('\n'));
    }),
  );

  return router;
};
