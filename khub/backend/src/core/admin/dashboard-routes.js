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


  // Chart data: 12-month signups + publications, and themes distribution.
  router.get(
    '/analytics/series',
    asyncHandler(async (req, res) => {
      const db = req.db;
      const since = new Date();
      since.setMonth(since.getMonth() - 11);
      since.setDate(1);
      const monthOf = (value) => String(value ?? '').slice(0, 7);
      const months = Array.from({ length: 12 }, (_, index) => {
        const date = new Date(since.getFullYear(), since.getMonth() + index, 1);
        return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
      });
      const [signups, publications, themes] = await Promise.all([
        db('users').where('created_at', '>=', since).select('created_at'),
        db('publication').where('created_at', '>=', since).where('is_version', false).whereNull('deleted_at').select('created_at'),
        db('publication')
          .join('sub_thematic_area', 'sub_thematic_area.id', 'publication.sub_thematic_area_id')
          .join('thematic_area', 'thematic_area.id', 'sub_thematic_area.thematic_area_id')
          .where({ 'publication.is_approved': true, 'publication.is_version': false })
          .whereNull('publication.deleted_at')
          .groupBy('thematic_area.id', 'thematic_area.description')
          .select('thematic_area.description as theme')
          .count({ total: 'publication.id' }),
      ]);
      const bucket = (rows) => {
        const byMonth = Object.fromEntries(months.map((month) => [month, 0]));
        for (const row of rows) {
          const month = monthOf(row.created_at instanceof Date ? row.created_at.toISOString() : row.created_at);
          if (month in byMonth) byMonth[month] += 1;
        }
        return months.map((month) => byMonth[month]);
      };
      res.json({
        months,
        signups: bucket(signups),
        publications: bucket(publications),
        themes: themes.map((row) => ({ theme: row.theme, total: Number(row.total) })).sort((a, b) => b.total - a.total),
      });
    }),
  );

  // Map data: per member state publication + user counts (ISO codes for Highcharts).
  router.get(
    '/analytics/geo',
    asyncHandler(async (req, res) => {
      const db = req.db;
      const [publications, users, countries] = await Promise.all([
        db('publication_countries')
          .join('country', 'country.id', 'publication_countries.country_id')
          .join('publication', 'publication.id', 'publication_countries.publication_id')
          .where({ 'publication.is_approved': true, 'publication.is_version': false })
          .whereNull('publication.deleted_at')
          .groupBy('country.id', 'country.name', 'country.iso_code')
          .select('country.name', 'country.iso_code')
          .count({ total: 'publication.id' }),
        db('users')
          .join('country', 'country.id', 'users.country_id')
          .groupBy('country.id', 'country.name', 'country.iso_code')
          .select('country.name', 'country.iso_code')
          .count({ total: 'users.id' }),
        db('country').select('name', 'iso_code'),
      ]);
      const entry = (row) => ({ name: row.name, iso: String(row.iso_code ?? '').toLowerCase(), total: Number(row.total) });
      res.json({
        publications: publications.map(entry).filter((row) => row.iso),
        users: users.map(entry).filter((row) => row.iso),
        memberStates: countries.length,
      });
    }),
  );

  return router;
};
