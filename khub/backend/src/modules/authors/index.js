import { Router } from 'express';
import { z } from 'zod';
import { forumPath } from '@khub/shared';
import { asyncHandler, notFound } from '../../core/http/errors.js';
import { validate } from '../../core/http/validate.js';
import { paginationSchema, paginate } from '../../core/http/pagination.js';
import { PublicationsRepository } from '../publications/repository.js';
import { publicationCard } from '../publications/serializer.js';
import { ContributorsRepository } from './repository.js';

const listSchema = paginationSchema.extend({ term: z.string().trim().max(200).optional() });

/**
 * Contributors & Authors (the production /browse/authors page and its
 * drill-down profile with stats, badge, resources, forums and communities).
 */
export default {
  name: 'authors',
  router: () => {
    const router = Router();

    router.get(
      '/',
      validate(listSchema, 'query'),
      asyncHandler(async (req, res) => {
        const { rows, total } = await new ContributorsRepository(req.site).list(req.validatedQuery);
        res.json(paginate(rows, total, req.validatedQuery));
      }),
    );

    router.get(
      '/:id',
      validate(paginationSchema, 'query'),
      asyncHandler(async (req, res) => {
        const contributor = await new ContributorsRepository(req.site).find(Number(req.params.id) || 0);
        if (!contributor) throw notFound('Contributor not found');
        const { rows, total } = await new PublicationsRepository(req.db).search({ author: contributor.id }, req.validatedQuery, req.user);
        res.json({
          data: {
            ...contributor,
            forumThreads: contributor.forumThreads.map((thread) => ({ id: thread.id, title: thread.title, createdAt: thread.created_at, views: Number(thread.views ?? 0), path: forumPath(thread) })),
          },
          publications: paginate(rows.map((item) => publicationCard(req.site, item)), total, req.validatedQuery),
        });
      }),
    );

    return router;
  },
};
