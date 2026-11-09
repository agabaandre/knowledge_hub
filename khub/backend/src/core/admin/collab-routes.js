import { Router } from 'express';
import { z } from 'zod';
import { asyncHandler, HttpError } from '../http/errors.js';
import { validate } from '../http/validate.js';
import { requirePermission } from '../auth/middleware.js';
import { countOf, likePattern, toBool, now } from '../db/helpers.js';
import { slugify } from '@khub/shared';
import { userPhotoUrl } from '../../modules/publications/serializer.js';

const pageSchema = z.object({
  term: z.string().trim().max(200).optional().default(''),
  page: z.coerce.number().int().min(1).optional().default(1),
  per_page: z.coerce.number().int().min(1).max(50).optional().default(12),
});
const meta = (page, perPage, total) => ({ page, perPage, total, lastPage: Math.max(1, Math.ceil(total / perPage)) });

/** Admin: Communities of Practice (Manage COPs + COP Participants Directory). */
export const communitiesAdminRoutes = () => {
  const router = Router();
  const listSchema = pageSchema.extend({ view: z.enum(['all', 'active', 'inactive', 'trash']).optional().default('all') });
  const memberSchema = pageSchema.extend({ status: z.enum(['all', 'pending', 'approved']).optional().default('all') });

  router.get(
    '/communities/stats',
    asyncHandler(async (req, res) => {
      const db = req.db;
      const [total, active, isPublic, approvedMemberships, pendingMemberships] = await Promise.all([
        countOf(db('community_of_practices').whereNull('deleted_at')),
        countOf(db('community_of_practices').whereNull('deleted_at').where('is_active', true)),
        countOf(db('community_of_practices').whereNull('deleted_at').where('is_public', true)),
        countOf(db('community_of_practice_members').where('is_approved', true)),
        countOf(db('community_of_practice_members').where('is_approved', false)),
      ]);
      res.json({ total, active, public: isPublic, approvedMemberships, pendingMemberships });
    }),
  );

  router.get(
    '/communities',
    asyncHandler(async (req, res) => {
      const { term, view, page, per_page: perPage } = listSchema.parse(req.query);
      const db = req.db;
      const query = db('community_of_practices')
        .leftJoin('users as creator', 'creator.id', 'community_of_practices.created_by')
        .leftJoin('country', 'country.id', 'community_of_practices.country_id')
        .modify((builder) => (view === 'trash' ? builder.whereNotNull('community_of_practices.deleted_at') : builder.whereNull('community_of_practices.deleted_at')));
      if (view === 'active') query.where('community_of_practices.is_active', true);
      if (view === 'inactive') query.where('community_of_practices.is_active', false);
      if (term) {
        query.where((builder) => builder
          .whereILike('community_of_practices.community_name', likePattern(term))
          .orWhereILike('community_of_practices.description', likePattern(term))
          .orWhereILike('creator.email', likePattern(term)));
      }
      const total = await countOf(query);
      const rows = await query
        .select('community_of_practices.*', 'creator.name as creator_name', 'creator.email as creator_email', 'country.name as country_name')
        .select(db('community_of_practice_members').count('*').whereRaw('community_of_practice_members.community_of_practice_id = community_of_practices.id').where('is_approved', true).as('member_count'))
        .select(db('community_of_practice_members').count('*').whereRaw('community_of_practice_members.community_of_practice_id = community_of_practices.id').where('is_approved', false).as('pending_count'))
        .select(db('publication_community_of_practices').count('*').whereRaw('publication_community_of_practices.community_of_practice_id = community_of_practices.id').as('publication_count'))
        .orderBy('community_of_practices.created_at', 'desc')
        .limit(perPage).offset((page - 1) * perPage);
      res.json({
        data: rows.map((row) => ({
          id: row.id,
          name: row.community_name,
          slug: row.slug,
          creator: row.creator_name ? { name: row.creator_name, email: row.creator_email } : null,
          country: row.country_name,
          isPublic: toBool(row.is_public),
          isActive: toBool(row.is_active),
          members: Number(row.member_count ?? 0),
          pendingMembers: Number(row.pending_count ?? 0),
          publications: Number(row.publication_count ?? 0),
          trashedAt: row.deleted_at,
          createdAt: row.created_at,
        })),
        meta: meta(page, perPage, total),
      });
    }),
  );

  // Create a community (the Laravel admin form) — the creator joins as its first admin member.
  router.post(
    '/communities',
    requirePermission('admin.communities.manage'),
    validate(z.object({
      name: z.string().trim().min(3).max(150),
      description: z.string().trim().min(10).max(5000),
      organisation: z.string().trim().max(150).optional().default(''),
      country_id: z.coerce.number().int().positive().nullable().optional(),
      is_public: z.boolean().optional().default(true),
      is_active: z.boolean().optional().default(true),
    })),
    asyncHandler(async (req, res) => {
      const baseSlug = slugify(req.body.name);
      const clash = await req.db('community_of_practices').where('slug', baseSlug).first('id');
      const [inserted] = await req.db('community_of_practices').insert({
        community_name: req.body.name,
        slug: clash ? `${baseSlug}-${Date.now().toString(36)}` : baseSlug,
        description: req.body.description,
        organisation: req.body.organisation || null,
        country_id: req.body.country_id ?? null,
        is_public: req.body.is_public,
        is_active: req.body.is_active,
        created_by: req.user.id,
        created_at: now(),
        updated_at: now(),
      }).returning('id');
      const communityId = typeof inserted === 'object' ? inserted.id : inserted;
      await req.db('community_of_practice_members').insert({
        community_of_practice_id: communityId, user_id: req.user.id,
        is_approved: true, is_active: true, is_admin: true, created_at: now(), updated_at: now(),
      });
      res.status(201).json({ id: communityId });
    }),
  );

  router.put(
    '/communities/:id',
    requirePermission('admin.communities.manage'),
    validate(z.object({ is_active: z.boolean().optional(), is_public: z.boolean().optional() }).refine((v) => Object.keys(v).length > 0, 'Nothing to update')),
    asyncHandler(async (req, res) => {
      const changed = await req.db('community_of_practices').where({ id: Number(req.params.id) }).update({ ...req.body, updated_at: now() });
      if (!changed) throw new HttpError(404, 'Community not found.');
      res.json({ ok: true });
    }),
  );

  router.delete('/communities/:id', requirePermission('admin.communities.manage'), asyncHandler(async (req, res) => {
    const changed = await req.db('community_of_practices').where({ id: Number(req.params.id) }).whereNull('deleted_at').update({ deleted_at: now(), updated_at: now() });
    if (!changed) throw new HttpError(404, 'Community not found (or already in the trash).');
    res.json({ ok: true, trashed: true });
  }));
  router.post('/communities/:id/restore', requirePermission('admin.communities.manage'), asyncHandler(async (req, res) => {
    const changed = await req.db('community_of_practices').where({ id: Number(req.params.id) }).whereNotNull('deleted_at').update({ deleted_at: null, updated_at: now() });
    if (!changed) throw new HttpError(404, 'Nothing to restore.');
    res.json({ ok: true });
  }));
  router.delete('/communities/:id/destroy', requirePermission('admin.communities.manage'), asyncHandler(async (req, res) => {
    const id = Number(req.params.id);
    await req.db('community_of_practice_members').where({ community_of_practice_id: id }).del();
    await req.db('publication_community_of_practices').where({ community_of_practice_id: id }).del();
    const changed = await req.db('community_of_practices').where({ id }).del();
    if (!changed) throw new HttpError(404, 'Community not found.');
    res.status(204).end();
  }));

  // COP Participants Directory.
  router.get(
    '/communities/participants',
    asyncHandler(async (req, res) => {
      const { term, status, page, per_page: perPage } = memberSchema.parse(req.query);
      const db = req.db;
      const query = db('community_of_practice_members')
        .join('users', 'users.id', 'community_of_practice_members.user_id')
        .join('community_of_practices', 'community_of_practices.id', 'community_of_practice_members.community_of_practice_id')
        .whereNull('community_of_practices.deleted_at');
      if (status === 'pending') query.where('community_of_practice_members.is_approved', false);
      if (status === 'approved') query.where('community_of_practice_members.is_approved', true);
      if (term) {
        query.where((builder) => builder
          .whereILike('users.name', likePattern(term))
          .orWhereILike('users.email', likePattern(term))
          .orWhereILike('community_of_practices.community_name', likePattern(term)));
      }
      const total = await countOf(query);
      const rows = await query
        .select('community_of_practice_members.id', 'community_of_practice_members.is_approved', 'community_of_practice_members.is_admin', 'community_of_practice_members.created_at',
          'users.id as user_id', 'users.name', 'users.email', 'users.photo', 'users.is_photo_external', 'users.organization_name',
          'community_of_practices.id as community_id', 'community_of_practices.community_name')
        .orderBy([{ column: 'community_of_practice_members.is_approved', order: 'asc' }, { column: 'community_of_practice_members.created_at', order: 'desc' }])
        .limit(perPage).offset((page - 1) * perPage);
      res.json({
        data: rows.map((row) => ({
          id: row.id,
          user: { id: row.user_id, name: row.name, email: row.email, organisation: row.organization_name, photo: userPhotoUrl(req.site, row) },
          community: { id: row.community_id, name: row.community_name },
          approved: toBool(row.is_approved),
          isAdmin: toBool(row.is_admin),
          joinedAt: row.created_at,
        })),
        meta: meta(page, perPage, total),
      });
    }),
  );

  router.put(
    '/communities/participants/:id',
    requirePermission('admin.communities.manage'),
    validate(z.object({ is_approved: z.boolean().optional(), is_admin: z.boolean().optional() }).refine((v) => Object.keys(v).length > 0, 'Nothing to update')),
    asyncHandler(async (req, res) => {
      const changed = await req.db('community_of_practice_members').where({ id: Number(req.params.id) }).update({ ...req.body, updated_at: now() });
      if (!changed) throw new HttpError(404, 'Membership not found.');
      res.json({ ok: true });
    }),
  );
  router.delete('/communities/participants/:id', requirePermission('admin.communities.manage'), asyncHandler(async (req, res) => {
    await req.db('community_of_practice_members').where({ id: Number(req.params.id) }).del();
    res.status(204).end();
  }));

  return router;
};

/** Admin: forum management (moderation happens via the approvals endpoint; this adds pin/lock/trash). */
export const forumsAdminRoutes = () => {
  const router = Router();
  const listSchema = pageSchema.extend({ status: z.enum(['all', 'approved', 'pending', 'rejected', 'trashed']).optional().default('all') });

  router.get(
    '/forums',
    asyncHandler(async (req, res) => {
      const { term, status, page, per_page: perPage } = listSchema.parse(req.query);
      const db = req.db;
      const query = db('forums').leftJoin('users', 'users.id', 'forums.created_by');
      if (status === 'trashed') query.whereNotNull('forums.deleted_at');
      else query.whereNull('forums.deleted_at');
      if (status === 'approved') query.where('forums.is_approved', true);
      if (status === 'pending') query.where({ 'forums.is_approved': false, 'forums.is_rejected': false });
      if (status === 'rejected') query.where('forums.is_rejected', true);
      if (term) {
        query.where((builder) => builder
          .whereILike('forums.forum_title', likePattern(term))
          .orWhereILike('forums.forum_description', likePattern(term))
          .orWhereILike('users.name', likePattern(term)));
      }
      const total = await countOf(query);
      const rows = await query
        .select('forums.*', 'users.name as author_name')
        .select(db('forum_comments').count('*').whereRaw('forum_comments.forum_id = forums.id').as('comment_count'))
        .orderBy([{ column: 'forums.is_pinned', order: 'desc' }, { column: 'forums.created_at', order: 'desc' }])
        .limit(perPage).offset((page - 1) * perPage);
      res.json({
        data: rows.map((row) => ({
          id: row.id,
          slug: row.slug,
          title: row.forum_title,
          author: row.author_name,
          comments: Number(row.comment_count ?? 0),
          views: Number(row.views ?? 0),
          status: toBool(row.is_approved) ? 'approved' : toBool(row.is_rejected) ? 'rejected' : 'pending',
          rejectedReason: row.rejected_reason,
          pinned: toBool(row.is_pinned),
          locked: toBool(row.is_locked),
          trashedAt: row.deleted_at,
          createdAt: row.created_at,
        })),
        meta: meta(page, perPage, total),
      });
    }),
  );

  router.put(
    '/forums/:id',
    requirePermission('admin.forums.manage'),
    validate(z.object({ is_pinned: z.boolean().optional(), is_locked: z.boolean().optional() }).refine((v) => Object.keys(v).length > 0, 'Nothing to update')),
    asyncHandler(async (req, res) => {
      const changed = await req.db('forums').where({ id: Number(req.params.id) }).update(req.body);
      if (!changed) throw new HttpError(404, 'Forum not found.');
      res.json({ ok: true });
    }),
  );

  router.delete('/forums/:id', requirePermission('admin.forums.manage'), asyncHandler(async (req, res) => {
    const changed = await req.db('forums').where({ id: Number(req.params.id) }).whereNull('deleted_at').update({ deleted_at: now() });
    if (!changed) throw new HttpError(404, 'Forum not found (or already in the trash).');
    res.json({ ok: true, trashed: true });
  }));
  router.post('/forums/:id/restore', requirePermission('admin.forums.manage'), asyncHandler(async (req, res) => {
    const changed = await req.db('forums').where({ id: Number(req.params.id) }).whereNotNull('deleted_at').update({ deleted_at: null });
    if (!changed) throw new HttpError(404, 'Nothing to restore.');
    res.json({ ok: true });
  }));
  router.delete('/forums/:id/destroy', requirePermission('admin.forums.manage'), asyncHandler(async (req, res) => {
    const id = Number(req.params.id);
    await req.db('forum_comments').where({ forum_id: id }).del();
    await req.db('forum_tags').where({ forum_id: id }).del();
    await req.db('forum_likes').where({ forum_id: id }).del().catch(() => {});
    const changed = await req.db('forums').where({ id }).del();
    if (!changed) throw new HttpError(404, 'Forum not found.');
    res.status(204).end();
  }));

  return router;
};
