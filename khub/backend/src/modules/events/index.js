import { Router } from 'express';
import { eventPath } from '@khub/shared';
import { asyncHandler, notFound } from '../../core/http/errors.js';
import { validate } from '../../core/http/validate.js';
import { paginationSchema, paginate } from '../../core/http/pagination.js';
import { countOf, toBool } from '../../core/db/helpers.js';
import { mediaUrl } from '../../core/storage/hub-storage.js';

const serialize = (site, row) => ({
  id: row.id,
  title: row.title,
  description: row.description,
  venue: row.venue,
  startDate: row.startdate,
  endDate: row.enddate,
  organizedBy: row.organized_by,
  isOnline: toBool(row.is_online),
  eventLink: row.event_link,
  registrationLink: row.registration_link,
  category: row.event_category,
  banner: row.banner_image ? (/^https?:/i.test(row.banner_image) ? row.banner_image : mediaUrl(site, `uploads/events/${row.banner_image}`)) : '',
  path: eventPath(row),
});

/** Active events whose end date has not passed, soonest first (same rule as the Laravel home page). */
const upcomingQuery = (db) =>
  db('events')
    .where((builder) => builder.where('status', 'active').orWhereNull('status'))
    .where((builder) => builder.whereNull('enddate').orWhere('enddate', '>=', new Date().toISOString().slice(0, 10)))
    .orderBy('startdate', 'asc');

export default {
  name: 'events',
  router: () => {
    const router = Router();

    router.get(
      '/',
      validate(paginationSchema, 'query'),
      asyncHandler(async (req, res) => {
        const { page, per_page: perPage } = req.validatedQuery;
        const query = upcomingQuery(req.db);
        const total = await countOf(query);
        const rows = await query.limit(perPage).offset((page - 1) * perPage);
        res.json(paginate(rows.map((row) => serialize(req.site, row)), total, req.validatedQuery));
      }),
    );

    router.get(
      '/:id',
      asyncHandler(async (req, res) => {
        const row = await req.db('events').where('id', Number(req.params.id) || 0).first();
        if (!row) throw notFound('Event not found');
        res.json({ data: serialize(req.site, row) });
      }),
    );

    return router;
  },
};
