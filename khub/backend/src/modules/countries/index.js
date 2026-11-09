import { Router } from 'express';
import { countryPath, parseRecordKey } from '@khub/shared';
import { asyncHandler, notFound } from '../../core/http/errors.js';
import { validate } from '../../core/http/validate.js';
import { paginationSchema, paginate } from '../../core/http/pagination.js';
import { PublicationsRepository } from '../publications/repository.js';
import { publicationCard } from '../publications/serializer.js';

const serialize = (row) => ({
  id: row.id,
  slug: row.slug,
  name: row.name,
  isoCode: row.iso_code,
  iso3Code: row.iso3_code,
  region: row.region_name,
  flag: row.flag,
  latitude: row.latitude,
  longitude: row.longitude,
  publicationCount: Number(row.publication_count ?? 0),
  path: countryPath(row),
});

const withCount = (db) =>
  db('country')
    .leftJoin('regions', 'regions.id', 'country.region_id')
    .select('country.*', 'regions.region_name')
    .select(db('publication_countries').count('*').whereRaw('publication_countries.country_id = country.id').as('publication_count'));

export default {
  name: 'countries',
  router: () => {
    const router = Router();

    router.get(
      '/',
      asyncHandler(async (req, res) => {
        const rows = await withCount(req.db).orderBy('country.name');
        res.json({ data: rows.map(serialize) });
      }),
    );

    router.get(
      '/:key',
      validate(paginationSchema, 'query'),
      asyncHandler(async (req, res) => {
        const { id, slug } = parseRecordKey(req.params.key);
        const row = await (id ? withCount(req.db).where('country.id', id) : withCount(req.db).where('country.slug', slug)).first();
        if (!row) throw notFound('Country not found');
        const { rows, total } = await new PublicationsRepository(req.db).search({ country: row.id }, req.validatedQuery, req.user);
        res.json({ data: serialize(row), publications: paginate(rows.map((item) => publicationCard(req.site, item)), total, req.validatedQuery) });
      }),
    );

    return router;
  },
};
