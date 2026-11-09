import { Router } from 'express';
import { z } from 'zod';
import { forumPath, parseRecordKey, truncate } from '@khub/shared';
import { asyncHandler, notFound } from '../../core/http/errors.js';
import { validate } from '../../core/http/validate.js';
import { paginationSchema, paginate } from '../../core/http/pagination.js';
import { requireAuth } from '../../core/auth/middleware.js';
import { mediaUrl } from '../../core/storage/hub-storage.js';
import { userPhotoUrl } from '../publications/serializer.js';
import { ForumsRepository } from './repository.js';

const listSchema = paginationSchema.extend({ term: z.string().trim().max(200).optional() });
const commentSchema = z.object({ comment: z.string().trim().min(2).max(5000), parent_id: z.coerce.number().int().positive().optional() });

const forumImage = (site, image) => (image && image !== 'forum.jpg' ? mediaUrl(site, `uploads/forums/${image}`) : '');

const author = (site, row) => ({ id: row.created_by, name: row.author_name, photo: userPhotoUrl(site, row) });

const forumCard = (site, row) => ({
  id: row.id,
  slug: row.slug,
  title: row.forum_title,
  excerpt: truncate(row.forum_description, 240),
  image: forumImage(site, row.forum_image),
  createdAt: row.created_at,
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
      const [comments, tags] = await Promise.all([repo.commentsOf(row.id), repo.tagsOf(row.id)]);
      await repo.incrementViews(row.id);
      res.json({ data: { ...forumCard(req.site, row), description: row.forum_description, tags, comments: commentTree(req.site, comments) } });
    }),
  );

  router.post(
    '/:id/comments',
    requireAuth,
    validate(commentSchema),
    asyncHandler(async (req, res) => {
      const siteSettings = await settings.forSite(req.site);
      await new ForumsRepository(req.db).addComment(Number(req.params.id), req.user.id, req.body.comment, req.body.parent_id, siteSettings.auto_approve_comments);
      res.status(201).json({ status: siteSettings.auto_approve_comments ? 'approved' : 'pending' });
    }),
  );

  router.post(
    '/:id/like',
    requireAuth,
    asyncHandler(async (req, res) => res.json(await new ForumsRepository(req.db).toggleLike(Number(req.params.id), req.user.id))),
  );

  return router;
};
