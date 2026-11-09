import { Router } from 'express';
import { z } from 'zod';
import { asyncHandler, HttpError } from '../http/errors.js';
import { validate } from '../http/validate.js';
import { now } from '../db/helpers.js';
import { allowedCountryIds } from '../auth/access.js';

const reviewSchema = z.object({
  type: z.enum(['publication', 'forum', 'publication_comment', 'forum_comment']),
  id: z.coerce.number().int().positive(),
  action: z.enum(['approve', 'reject']),
  reason: z.string().trim().max(1000).optional(),
});

/**
 * Geography-scoped approvals.
 *
 * An item's country is its publication_countries rows, falling back to the
 * submitter's country. Overall admins see everything, RCC moderators the
 * countries of their region, country moderators their country. Items with no
 * resolvable country reach overall administrators only.
 */
const scopePublications = (db, query, allowed) => {
  if (allowed === null) return query;
  return query.where((outer) => {
    outer
      .whereIn('publication.id', db('publication_countries').select('publication_id').whereIn('country_id', allowed))
      .orWhere((fallback) => {
        fallback
          .whereNotIn('publication.id', db('publication_countries').select('publication_id'))
          .whereIn('users.country_id', allowed.length ? allowed : [-1]);
      });
  });
};

const scopeBySubmitter = (query, column, allowed) => (allowed === null ? query : query.whereIn(column, allowed.length ? allowed : [-1]));

export const approvalsRoutes = () => {
  const router = Router();

  router.get(
    '/approvals',
    asyncHandler(async (req, res) => {
      const db = req.db;
      const access = req.user.access;
      const allowed = await allowedCountryIds(db, access);

      const [publications, forums, publicationComments, forumComments] = await Promise.all([
        scopePublications(db, db('publication').leftJoin('users', 'users.id', 'publication.user_id')
          .where({ 'publication.is_approved': false, 'publication.is_rejected': false, 'publication.is_version': false }), allowed)
          .select('publication.id', 'publication.title', 'publication.slug', 'publication.created_at', 'users.name as submitted_by')
          .select(db('publication_countries').join('country', 'country.id', 'publication_countries.country_id')
            .whereRaw('publication_countries.publication_id = publication.id').min('country.name').as('country_name'))
          .orderBy('publication.created_at', 'desc').limit(100),
        scopeBySubmitter(db('forums').leftJoin('users', 'users.id', 'forums.created_by')
          .leftJoin('country', 'country.id', 'users.country_id')
          .where({ 'forums.is_approved': false, 'forums.is_rejected': false }), 'users.country_id', allowed)
          .select('forums.id', 'forums.forum_title as title', 'forums.slug', 'forums.created_at', 'users.name as submitted_by', 'country.name as country_name')
          .orderBy('forums.created_at', 'desc').limit(100),
        scopeBySubmitter(db('publication_comments').leftJoin('users', 'users.id', 'publication_comments.user_id')
          .leftJoin('country', 'country.id', 'users.country_id')
          .where('publication_comments.status', 'pending'), 'users.country_id', allowed)
          .select('publication_comments.id', 'publication_comments.comment', 'publication_comments.created_at', 'users.name as submitted_by', 'country.name as country_name')
          .orderBy('publication_comments.created_at', 'desc').limit(100),
        scopeBySubmitter(db('forum_comments').leftJoin('users', 'users.id', 'forum_comments.created_by')
          .leftJoin('country', 'country.id', 'users.country_id')
          .where('forum_comments.status', 'pending'), 'users.country_id', allowed)
          .select('forum_comments.id', 'forum_comments.comment', 'forum_comments.created_at', 'users.name as submitted_by', 'country.name as country_name')
          .orderBy('forum_comments.created_at', 'desc').limit(100),
      ]);

      res.json({
        scope: { level: access.level, label: access.label, scopeLabel: access.scopeLabel },
        publications,
        forums,
        comments: [
          ...publicationComments.map((row) => ({ ...row, type: 'publication_comment' })),
          ...forumComments.map((row) => ({ ...row, type: 'forum_comment' })),
        ],
      });
    }),
  );

  router.post(
    '/approvals/review',
    validate(reviewSchema),
    asyncHandler(async (req, res) => {
      const { type, id, action, reason } = req.body;
      const db = req.db;
      const access = req.user.access;
      const allowed = await allowedCountryIds(db, access);
      const approve = action === 'approve';

      // A moderator may only review items inside their scope.
      if (allowed !== null) {
        const inScope = {
          publication: async () => {
            const row = await scopePublications(db, db('publication').leftJoin('users', 'users.id', 'publication.user_id').where('publication.id', id), allowed).first('publication.id');
            return Boolean(row);
          },
          forum: async () => Boolean(await scopeBySubmitter(db('forums').leftJoin('users', 'users.id', 'forums.created_by').where('forums.id', id), 'users.country_id', allowed).first('forums.id')),
          publication_comment: async () => Boolean(await scopeBySubmitter(db('publication_comments').leftJoin('users', 'users.id', 'publication_comments.user_id').where('publication_comments.id', id), 'users.country_id', allowed).first('publication_comments.id')),
          forum_comment: async () => Boolean(await scopeBySubmitter(db('forum_comments').leftJoin('users', 'users.id', 'forum_comments.created_by').where('forum_comments.id', id), 'users.country_id', allowed).first('forum_comments.id')),
        };
        if (!(await inScope[type]())) throw new HttpError(403, `This ${type.replace('_', ' ')} is outside your ${access.scopeLabel} scope.`);
      }

      const updates = {
        publication: () => db('publication').where({ id }).update(
          approve
            ? { is_approved: true, is_rejected: false, approved_by: req.user.id, updated_at: now() }
            : { is_approved: false, is_rejected: true, rejected_by: req.user.id, rejected_reason: reason ?? null, rejected_at: now(), updated_at: now() },
        ),
        forum: () => db('forums').where({ id }).update(
          approve
            ? { is_approved: true, is_rejected: false, approved_by: req.user.id, status: 'approved' }
            : { is_approved: false, is_rejected: true, rejected_by: req.user.id, rejected_reason: reason ?? null, status: 'rejected' },
        ),
        publication_comment: () => db('publication_comments').where({ id }).update({ status: approve ? 'approved' : 'rejected' }),
        forum_comment: () => db('forum_comments').where({ id }).update({ status: approve ? 'approved' : 'rejected', updated_at: now() }),
      };
      const changed = await updates[type]();
      if (!changed) throw new HttpError(404, `No pending ${type.replace('_', ' ')} #${id}.`);
      res.json({ ok: true, type, id, action });
    }),
  );

  return router;
};
