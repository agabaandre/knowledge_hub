import { Router } from 'express';
import { z } from 'zod';
import { asyncHandler } from '../http/errors.js';
import { validate } from '../http/validate.js';

const analyticsSchema = z.object({
  period: z.enum(['7d', '30d', '90d', 'ytd', 'all']).optional().default('30d'),
  from: z.string().regex(/^\d{4}-\d{2}-\d{2}$/).optional(),
  to: z.string().regex(/^\d{4}-\d{2}-\d{2}$/).optional(),
  country_id: z.coerce.number().int().positive().optional(),
});

const rangeOf = ({ period, from, to }) => {
  if (from || to) return { from: from ? `${from} 00:00:00` : null, to: to ? `${to} 23:59:59` : null };
  if (period === 'all') return { from: null, to: null };
  if (period === 'ytd') return { from: `${new Date().getFullYear()}-01-01 00:00:00`, to: null };
  const days = { '7d': 7, '30d': 30, '90d': 90 }[period];
  return { from: new Date(Date.now() - days * 24 * 3600 * 1000).toISOString().slice(0, 19).replace('T', ' '), to: null };
};

const applyRange = (query, column, range) => {
  if (range.from) query.where(column, '>=', range.from);
  if (range.to) query.where(column, '<=', range.to);
  return query;
};

const count = async (query) => Number((await query.count({ total: '*' }).first())?.total ?? 0);

/** GET /api/admin/dashboard — the stats overview the Laravel admin home shows. */
export const dashboardRoutes = () => {
  const router = Router();

  router.get(
    '/dashboard',
    asyncHandler(async (req, res) => {
      const db = req.db;
      const [publications, pendingPublications, users, forums, pendingForums, pendingComments, pendingForumComments, communities, searches30d, authors, memberStates] = await Promise.all([
        count(db('publication').where({ is_active: 'Active', is_approved: true, is_version: false })),
        count(db('publication').where({ is_approved: false, is_rejected: false, is_version: false })),
        count(db('users')),
        count(db('forums').where({ is_approved: true })),
        count(db('forums').where({ is_approved: false, is_rejected: false })),
        count(db('publication_comments').where({ status: 'pending' })),
        count(db('forum_comments').where({ status: 'pending' })),
        count(db('community_of_practices')),
        count(db('search_logs').where('created_at', '>=', new Date(Date.now() - 30 * 24 * 3600 * 1000).toISOString().slice(0, 19).replace('T', ' '))),
        count(db('author')),
        count(db('country')),
      ]);
      res.json({
        content: { publications, forums, communities, authors, memberStates },
        pending: { publications: pendingPublications, forums: pendingForums, comments: pendingComments + pendingForumComments },
        users: { total: users },
        activity: { searchesLast30Days: searches30d },
      });
    }),
  );

  router.get(
    '/analytics',
    validate(analyticsSchema, 'query'),
    asyncHandler(async (req, res) => {
      const db = req.db;
      const filters = req.validatedQuery;
      const range = rangeOf(filters);

      const signupsQuery = applyRange(db('users'), 'users.created_at', range);
      if (filters.country_id) signupsQuery.where('users.country_id', filters.country_id);

      const searchesQuery = applyRange(db('search_logs'), 'search_logs.created_at', range);

      const publicationsQuery = applyRange(db('publication').where({ is_approved: true, is_version: false }), 'publication.created_at', range);
      if (filters.country_id) {
        publicationsQuery.whereIn('publication.id', db('publication_countries').select('publication_id').where('country_id', filters.country_id));
      }

      const [signups, searches, newPublications, signupCountries, resourceViews] = await Promise.all([
        count(signupsQuery),
        count(searchesQuery),
        count(publicationsQuery),
        applyRange(db('users'), 'created_at', range).whereNotNull('country_id').countDistinct({ total: 'country_id' }).first().then((row) => Number(row?.total ?? 0)),
        db('publication').where({ is_approved: true }).sum({ total: 'visits' }).first().then((row) => Number(row?.total ?? 0)),
      ]);

      res.json({
        period: filters.period,
        range,
        visits: searches, // search/page activity recorded by the hub (no raw hit counter yet)
        signups,
        newPublications,
        signupCountries,
        resourceViewsAllTime: resourceViews,
      });
    }),
  );

  return router;
};
