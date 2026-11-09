import { Router } from 'express';
import { z } from 'zod';
import { forumPath, parseRecordKey, truncate, slugify } from '@khub/shared';
import { asyncHandler, notFound, HttpError } from '../../core/http/errors.js';
import { toBool } from '../../core/db/helpers.js';
import { sanitizeRichText } from '../../core/http/sanitize-html.js';
import { validate } from '../../core/http/validate.js';
import { paginationSchema, paginate } from '../../core/http/pagination.js';
import { requireAuth } from '../../core/auth/middleware.js';
import { mediaUrl } from '../../core/storage/hub-storage.js';
import { userPhotoUrl } from '../publications/serializer.js';
import { ForumsRepository } from './repository.js';

const listSchema = paginationSchema.extend({ term: z.string().trim().max(200).optional() });
const commentSchema = z.object({ comment: z.string().trim().min(2).max(5000), parent_id: z.coerce.number().int().positive().optional() });
const createSchema = z.object({
  title: z.string().trim().min(5).max(255),
  description: z.string().trim().min(20).max(20000),
  tags: z.array(z.string().trim().min(1).max(60)).max(10).optional().default([]),
});

const forumImage = (site, image) => (image && image !== 'forum.jpg' ? mediaUrl(site, `uploads/forums/${image}`) : '');

const author = (site, row) => ({ id: row.created_by, name: row.author_name, photo: userPhotoUrl(site, row) });

const forumCard = (site, row) => ({
  id: row.id,
  slug: row.slug,
  title: row.forum_title,
  excerpt: truncate(row.forum_description, 240),
  image: forumImage(site, row.forum_image),
  createdAt: row.created_at,
  isPinned: toBool(row.is_pinned),
  isLocked: toBool(row.is_locked),
  views: Number(row.views ?? 0),
  commentCount: Number(row.comment_count ?? 0),
  likeCount: Number(row.like_count ?? 0),
  author: author(site, row),
  path: forumPath(row),
});

/** Nest replies under their parent comment. */
const commentTree = (site, rows) => {
  const byId = new Map(rows.map((row) => [row.id, { id: row.id, comment: row.comment, createdAt: row.created_at, likeCount: Number(row.like_count ?? 0), author: author(site, row), replies: [] }]));
  const roots = [];
  for (const row of rows) {
    const node = byId.get(row.id);
    const parent = row.parent_id ? byId.get(row.parent_id) : null;
    if (parent) parent.replies.push(node);
    else roots.push(node);
  }
  return roots;
};

export const forumsRouter = ({ settings }) => {
  const router = Router();

  router.get(
    '/',
    validate(listSchema, 'query'),
    asyncHandler(async (req, res) => {
      const { rows, total } = await new ForumsRepository(req.db).list(req.validatedQuery);
      res.json(paginate(rows.map((row) => forumCard(req.site, row)), total, req.validatedQuery));
    }),
  );

  router.get(
    '/:key',
    asyncHandler(async (req, res) => {
      const repo = new ForumsRepository(req.db);
      const row = await repo.findByKey(parseRecordKey(req.params.key));
      if (!row) throw notFound('Discussion not found');
      const [comments, tags, isLiked] = await Promise.all([repo.commentsOf(row.id), repo.tagsOf(row.id), repo.isLikedBy(row.id, req.user?.id)]);
      await repo.incrementViews(row.id);
      res.json({ data: { ...forumCard(req.site, row), description: row.forum_description, tags, isLiked, comments: commentTree(req.site, comments) } });
    }),
  );

  router.post(
    '/:id/comments',
    requireAuth,
    validate(commentSchema),
    asyncHandler(async (req, res) => {
      const forum = await req.db('forums').where({ id: Number(req.params.id) }).first('id', 'is_locked');
      if (!forum) throw notFound('Discussion not found');
      if (toBool(forum.is_locked)) throw new HttpError(422, 'This discussion is locked — new replies are closed.');
      const siteSettings = await settings.forSite(req.site);
      await new ForumsRepository(req.db).addComment(Number(req.params.id), req.user.id, req.body.comment, req.body.parent_id, siteSettings.auto_approve_comments);
      res.status(201).json({ status: siteSettings.auto_approve_comments ? 'approved' : 'pending' });
    }),
  );

  // Start a discussion (pending moderator approval, like the Laravel portal).
  router.post(
    '/',
    requireAuth,
    validate(createSchema),
    asyncHandler(async (req, res) => {
      const baseSlug = slugify(req.body.title);
      const clash = await req.db('forums').where('slug', baseSlug).first('id');
      const id = await new ForumsRepository(req.db).create({
        title: req.body.title,
        description: sanitizeRichText(req.body.description),
        tags: req.body.tags,
        userId: req.user.id,
        slug: clash ? `${baseSlug}-${Date.now().toString(36)}` : baseSlug,
      });
      res.status(201).json({ id, status: 'pending', message: 'Thank you — your discussion was submitted and is awaiting review.' });
    }),
  );

  router.post(
    '/:id/like',
    requireAuth,
    asyncHandler(async (req, res) => res.json(await new ForumsRepository(req.db).toggleLike(Number(req.params.id), req.user.id))),
  );

  router.post(
    '/comments/:id/like',
    requireAuth,
    asyncHandler(async (req, res) => res.json(await new ForumsRepository(req.db).toggleCommentLike(Number(req.params.id), req.user.id))),
  );

  return router;
};
