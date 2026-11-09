import { Router } from 'express';
import { z } from 'zod';
import { publicationPath } from '@khub/shared';
import { PublicationsRepository } from '../../modules/publications/repository.js';
import { publicationDetail, userPhotoUrl } from '../../modules/publications/serializer.js';
import { asyncHandler, HttpError } from '../http/errors.js';
import { validate } from '../http/validate.js';
import { requirePermission } from '../auth/middleware.js';
import { paginationSchema, paginate } from '../http/pagination.js';
import { countOf, likePattern, toBool, now } from '../db/helpers.js';

const listSchema = paginationSchema.extend({
  term: z.string().trim().max(200).optional(),
  status: z.enum(['all', 'approved', 'pending', 'rejected', 'inactive']).optional().default('all'),
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
      if (status === 'approved') base.where({ 'publication.is_approved': true }).where('publication.is_active', 'Active');
      if (status === 'pending') base.where({ 'publication.is_approved': false, 'publication.is_rejected': false });
      if (status === 'rejected') base.where({ 'publication.is_rejected': true });
      if (status === 'inactive') base.whereNot('publication.is_active', 'Active');

      const total = await countOf(base);
      const rows = await base
        .select('publication.id', 'publication.slug', 'publication.title', 'publication.created_at', 'publication.visits',
          'publication.is_approved', 'publication.is_rejected', 'publication.is_active', 'publication.is_featured',
          'publication.rejected_reason', 'author.name as author_name', 'users.name as submitted_by')
        .orderBy('publication.created_at', 'desc')
        .limit(perPage)
        .offset((page - 1) * perPage);

      res.json(paginate(rows.map((row) => ({
        id: row.id, title: row.title, author: row.author_name ?? '', submittedBy: row.submitted_by ?? '',
        createdAt: row.created_at, visits: Number(row.visits ?? 0), status: statusOf(row),
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

  router.delete(
    '/publications/:id',
    requirePermission('admin.publications.edit'),
    asyncHandler(async (req, res) => {
      const id = Number(req.params.id);
      const related = [
        ['publication_tags', 'publication_id'], ['publication_countries', 'publication_id'],
        ['publication_community_of_practices', 'publication_id'], ['publication_attachments', 'publication_id'],
        ['publication_comments', 'publication_id'], ['publication_views', 'publication_id'],
        ['publication_summaries', 'resource_id'], ['favourites', 'publication_id'],
      ];
      for (const [table, column] of related) await req.db(table).where({ [column]: id }).del();
      const changed = await req.db('publication').where({ id }).del();
      if (!changed) throw new HttpError(404, 'Publication not found.');
      res.status(204).end();
    }),
  );

  return router;
};
