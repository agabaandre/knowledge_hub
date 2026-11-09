import { Router } from 'express';
import { z } from 'zod';
import { publicationPath } from '@khub/shared';
import { PublicationsRepository } from '../../modules/publications/repository.js';
import { publicationDetail, userPhotoUrl } from '../../modules/publications/serializer.js';
import { DocumentsService } from '../documents/service.js';
import { asyncHandler, HttpError } from '../http/errors.js';
import { validate } from '../http/validate.js';
import { requirePermission } from '../auth/middleware.js';
import { paginationSchema, paginate } from '../http/pagination.js';
import { countOf, likePattern, toBool, now } from '../db/helpers.js';

const listSchema = paginationSchema.extend({
  term: z.string().trim().max(200).optional(),
  status: z.enum(['all', 'approved', 'pending', 'rejected', 'inactive', 'trashed']).optional().default('all'),
});
const updateSchema = z.object({
  is_featured: z.boolean().optional(),
  is_active: z.boolean().optional(),
}).refine((value) => Object.keys(value).length > 0, 'Nothing to update');

const statusOf = (row) => (!['active', 'Active'].includes(String(row.is_active)) ? 'inactive' : toBool(row.is_approved) ? 'approved' : toBool(row.is_rejected) ? 'rejected' : 'pending');

/** Manage Resources: search/filter every publication, feature, activate, delete. */
export const publicationsAdminRoutes = () => {
  const router = Router();

  router.get(
    '/publications',
    asyncHandler(async (req, res) => {
      const { term, status, page, per_page: perPage } = listSchema.parse(req.query);
      const base = req.db('publication')
        .leftJoin('author', 'author.id', 'publication.author_id')
        .leftJoin('users', 'users.id', 'publication.user_id')
        .where('publication.is_version', false);
      if (term) base.where((builder) => builder.whereILike('publication.title', likePattern(term)).orWhereILike('author.name', likePattern(term)));
      if (status === 'trashed') base.whereNotNull('publication.deleted_at');
      else base.whereNull('publication.deleted_at');
      if (status === 'approved') base.where({ 'publication.is_approved': true }).where('publication.is_active', 'Active');
      if (status === 'pending') base.where({ 'publication.is_approved': false, 'publication.is_rejected': false });
      if (status === 'rejected') base.where({ 'publication.is_rejected': true });
      if (status === 'inactive') base.whereNot('publication.is_active', 'Active');

      const total = await countOf(base);
      const rows = await base
        .select('publication.id', 'publication.slug', 'publication.title', 'publication.created_at', 'publication.visits',
          'publication.is_approved', 'publication.is_rejected', 'publication.is_active', 'publication.is_featured',
          'publication.rejected_reason', 'publication.deleted_at', 'author.name as author_name', 'users.name as submitted_by')
        .orderBy('publication.created_at', 'desc')
        .limit(perPage)
        .offset((page - 1) * perPage);

      res.json(paginate(rows.map((row) => ({
        id: row.id, title: row.title, author: row.author_name ?? '', submittedBy: row.submitted_by ?? '',
        createdAt: row.created_at, visits: Number(row.visits ?? 0), status: statusOf(row),
        trashed: Boolean(row.deleted_at), trashedAt: row.deleted_at,
        featured: toBool(row.is_featured), active: String(row.is_active) === 'Active',
        rejectedReason: row.rejected_reason, path: publicationPath(row),
      })), total, { page, per_page: perPage }));
    }),
  );

  // CSV export of every resource matching the current filters (no pagination).
  router.get(
    '/publications/export',
    asyncHandler(async (req, res) => {
      const { term, status } = listSchema.pick({ term: true, status: true }).parse(req.query);
      const query = req.db('publication')
        .leftJoin('author', 'author.id', 'publication.author_id')
        .leftJoin('users', 'users.id', 'publication.user_id')
        .where('publication.is_version', false);
      if (term) query.where((builder) => builder.whereILike('publication.title', likePattern(term)).orWhereILike('author.name', likePattern(term)));
      if (status === 'trashed') query.whereNotNull('publication.deleted_at');
      else query.whereNull('publication.deleted_at');
      if (status === 'approved') query.where({ 'publication.is_approved': true }).where('publication.is_active', 'Active');
      if (status === 'pending') query.where({ 'publication.is_approved': false, 'publication.is_rejected': false });
      if (status === 'rejected') query.where({ 'publication.is_rejected': true });
      if (status === 'inactive') query.whereNot('publication.is_active', 'Active');
      const rows = await query
        .select('publication.id', 'publication.title', 'publication.year_published', 'publication.visits',
          'publication.is_approved', 'publication.is_rejected', 'publication.is_active', 'publication.is_featured',
          'publication.created_at', 'author.name as author_name', 'users.name as submitted_by')
        .orderBy('publication.created_at', 'desc');

      const cell = (value) => {
        const text = String(value ?? '');
        return /[",\n]/.test(text) ? `"${text.replace(/"/g, '""')}"` : text;
      };
      const header = ['ID', 'Title', 'Author', 'Submitted by', 'Year', 'Status', 'Views', 'Featured', 'Active', 'Submitted on'];
      const lines = rows.map((row) => [
        row.id, row.title, row.author_name ?? '', row.submitted_by ?? '', row.year_published ?? '',
        statusOf(row), Number(row.visits ?? 0), toBool(row.is_featured) ? 'yes' : 'no',
        String(row.is_active) === 'Active' ? 'yes' : 'no', row.created_at ? new Date(row.created_at).toISOString().slice(0, 10) : '',
      ].map(cell).join(','));
      const csv = ['\uFEFF' + header.join(','), ...lines].join('\n');
      res.setHeader('Content-Type', 'text/csv; charset=utf-8');
      res.setHeader('Content-Disposition', `attachment; filename="resources-${status}-${new Date().toISOString().slice(0, 10)}.csv"`);
      res.send(csv);
    }),
  );

  // Full record for the admin details page — any status (pending/rejected/inactive too).
  router.get(
    '/publications/:id',
    asyncHandler(async (req, res) => {
      const repo = new PublicationsRepository(req.db);
      const row = await repo.findForAdmin(Number(req.params.id));
      if (!row) throw new HttpError(404, 'Publication not found.');
      const [tags, attachments, comments, countries, versionChain] = await Promise.all([
        repo.tagsOf(row.id), repo.attachmentsOf(row.id), repo.allCommentsOf(row.id), repo.countriesOf(row.id),
        repo.versionChainForAdmin(row),
      ]);
      const detail = publicationDetail(req.site, row, { tags, attachments, comments, countries });
      res.json({
        data: {
          ...detail,
          comments: comments.map((comment, index) => ({ ...detail.comments[index], status: comment.status })),
          status: statusOf(row),
          featured: toBool(row.is_featured),
          active: String(row.is_active) === 'Active',
          adminOnly: toBool(row.is_admin_only_access),
          rejectedReason: row.rejected_reason,
          link: row.link,
          isVersion: toBool(row.is_version),
          versionNo: Number(row.version_no ?? 1) || 1,
          versions: versionChain.map((v) => ({
            id: v.id, title: v.title, versionNo: v.is_version ? Number(v.version_no ?? 2) : 1,
            isOriginal: !v.is_version, current: v.id === row.id,
            status: !['active', 'Active'].includes(String(v.is_active)) ? 'inactive' : toBool(v.is_approved) ? 'approved' : toBool(v.is_rejected) ? 'rejected' : 'pending',
          })),
          submittedBy: row.user_id ? {
            id: row.user_id,
            name: row.submitter_name,
            email: row.submitter_email,
            photo: userPhotoUrl(req.site, { photo: row.submitter_photo, is_photo_external: row.submitter_photo_external }),
          } : null,
        },
      });
    }),
  );

  router.put(
    '/publications/:id',
    requirePermission('admin.publications.edit'),
    validate(updateSchema),
    asyncHandler(async (req, res) => {
      const updates = { updated_at: now() };
      if (req.body.is_featured !== undefined) updates.is_featured = req.body.is_featured;
      if (req.body.is_active !== undefined) updates.is_active = req.body.is_active ? 'Active' : 'Inactive';
      const changed = await req.db('publication').where({ id: Number(req.params.id) }).update(updates);
      if (!changed) throw new HttpError(404, 'Publication not found.');
      res.json({ ok: true });
    }),
  );

  // Delete = move to trash (recoverable). The sweep destroys it after the retention policy.
  router.delete(
    '/publications/:id',
    requirePermission('admin.publications.edit'),
    asyncHandler(async (req, res) => {
      const changed = await req.db('publication').where({ id: Number(req.params.id) }).whereNull('deleted_at').update({ deleted_at: now(), updated_at: now() });
      if (!changed) throw new HttpError(404, 'Publication not found (or already in the trash).');
      res.json({ ok: true, trashed: true });
    }),
  );

  router.post(
    '/publications/:id/restore',
    requirePermission('admin.publications.edit'),
    asyncHandler(async (req, res) => {
      const changed = await req.db('publication').where({ id: Number(req.params.id) }).whereNotNull('deleted_at').update({ deleted_at: null, updated_at: now() });
      if (!changed) throw new HttpError(404, 'Nothing to restore.');
      res.json({ ok: true });
    }),
  );

  // Destroy permanently: stored files, related rows, the publication itself.
  router.delete(
    '/publications/:id/destroy',
    requirePermission('admin.publications.edit'),
    asyncHandler(async (req, res) => {
      const destroyed = await new DocumentsService(req.site).destroyPublication(Number(req.params.id));
      if (!destroyed) throw new HttpError(404, 'Publication not found.');
      res.status(204).end();
    }),
  );

  // Duplicate resolution: turn this publication into a version of another.
  router.post(
    '/publications/:id/make-version',
    requirePermission('admin.publications.edit'),
    validate(z.object({ parent_id: z.coerce.number().int().positive() })),
    asyncHandler(async (req, res) => {
      const id = Number(req.params.id);
      const target = await req.db('publication').where({ id: req.body.parent_id }).whereNull('deleted_at').first();
      if (!target) throw new HttpError(422, 'The target publication no longer exists.');
      if (target.id === id) throw new HttpError(422, 'A publication cannot be a version of itself.');
      const rootId = target.is_version && target.parent_id ? target.parent_id : target.id;
      const repo = new PublicationsRepository(req.db);
      const versionNo = await repo.nextVersionNo(rootId);
      const changed = await req.db('publication').where({ id }).whereNull('deleted_at')
        .update({ is_version: true, parent_id: rootId, version_no: versionNo, updated_at: now() });
      if (!changed) throw new HttpError(404, 'Publication not found.');
      res.json({ ok: true, parentId: rootId, versionNo });
    }),
  );

  // Duplicate resolution: move this publication's content into another, then trash it.
  router.post(
    '/publications/:id/merge-into',
    requirePermission('admin.publications.edit'),
    validate(z.object({ target_id: z.coerce.number().int().positive() })),
    asyncHandler(async (req, res) => {
      const id = Number(req.params.id);
      const targetId = Number(req.body.target_id);
      if (targetId === id) throw new HttpError(422, 'A publication cannot be merged into itself.');
      const [source, target] = await Promise.all([
        req.db('publication').where({ id }).whereNull('deleted_at').first('id', 'visits'),
        req.db('publication').where({ id: targetId }).whereNull('deleted_at').first('id'),
      ]);
      if (!source || !target) throw new HttpError(422, 'Both publications must exist and be out of the trash.');
      // Re-point user-generated content, combine view counts, then trash the duplicate.
      for (const [table, column] of [['publication_attachments', 'publication_id'], ['publication_comments', 'publication_id'], ['favourites', 'publication_id'], ['publication_views', 'publication_id']]) {
        await req.db(table).where({ [column]: id }).update({ [column]: targetId });
      }
      await req.db('publication').where({ id: targetId }).increment('visits', Number(source.visits ?? 0));
      await req.db('publication').where({ id }).update({ deleted_at: now(), updated_at: now() });
      res.json({ ok: true, mergedInto: targetId });
    }),
  );

    return router;
};
