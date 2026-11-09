import { Router } from 'express';
import { z } from 'zod';
import { parseRecordKey, publicationPath } from '@khub/shared';
import { asyncHandler, notFound, HttpError } from '../../core/http/errors.js';
import { validate } from '../../core/http/validate.js';
import { paginationSchema, paginate } from '../../core/http/pagination.js';
import { requireAuth } from '../../core/auth/middleware.js';
import { PublicationsRepository } from './repository.js';
import { LookupsRepository } from './lookups-repository.js';
import { publicationCard, publicationDetail } from './serializer.js';

const idList = z.preprocess((value) => (value === undefined ? undefined : [].concat(value)), z.array(z.coerce.number().int().positive()).optional());

const searchSchema = paginationSchema.extend({
  term: z.string().trim().max(200).optional(),
  tag: z.string().trim().max(191).optional(),
  theme: z.coerce.number().int().positive().optional(),
  sub_theme: z.coerce.number().int().positive().optional(),
  category: z.coerce.number().int().positive().optional(),
  author: z.coerce.number().int().positive().optional(),
  country: z.coerce.number().int().positive().optional(),
  year: z.coerce.number().int().min(1900).max(2100).optional(),
  file_type_id: idList,
  file_category_id: idList,
  data_category_id: idList,
  featured: z.coerce.boolean().optional(),
  sort: z.enum(['newest', 'oldest', 'popular', 'title']).optional(),
});

const commentSchema = z.object({
  comment: z.string().trim().min(3).max(2000),
  parent_id: z.coerce.number().int().positive().optional(),
});

const HOME_FEATURED = 6;
const HOME_TOP_SEARCHES = 10;
const HOME_INITIATIVES = 6;

export const publicationsRouter = ({ settings }) => {
  const router = Router();

  router.get(
    '/search',
    validate(searchSchema, 'query'),
    asyncHandler(async (req, res) => {
      const filters = req.validatedQuery;
      const { rows, total } = await new PublicationsRepository(req.db).search(filters, filters, req.user);
      res.json(paginate(rows.map((row) => publicationCard(req.site, row)), total, filters));
    }),
  );

  router.get(
    '/lookups',
    asyncHandler(async (req, res) => {
      const lookups = new LookupsRepository(req.site);
      const [themes, dataCategories, fileCategories, fileTypes, countries, years, popularTags, healthEmergencies, staticLinks] = await Promise.all([
        lookups.themes(), lookups.dataCategories(), lookups.fileCategories(), lookups.fileTypes(), lookups.countries(), lookups.years(), lookups.popularTags(), lookups.healthEmergencies(), lookups.staticLinks(),
      ]);
      res.json({ themes, dataCategories, fileCategories, fileTypes, countries, years, popularTags, healthEmergencies, staticLinks });
    }),
  );

  router.get(
    '/home',
    asyncHandler(async (req, res) => {
      const repo = new PublicationsRepository(req.db);
      const lookups = new LookupsRepository(req.site);
      const siteSettings = await settings.forSite(req.site);
      const [featured, topSearches, topSearchesTotal, initiatives, themes, healthEmergencies, quotes, sectionCounts] = await Promise.all([
        siteSettings.show_featured ? repo.featured(HOME_FEATURED) : [],
        siteSettings.show_top_searches ? repo.mostVisited(HOME_TOP_SEARCHES) : [],
        siteSettings.show_top_searches ? repo.countPublic() : 0,
        repo.byDataCategorySlug('initiatives', HOME_INITIATIVES),
        lookups.themes(),
        lookups.healthEmergencies(),
        siteSettings.show_quotes ? lookups.quotes() : [],
        lookups.sectionCounts(),
      ]);
      const card = (row) => publicationCard(req.site, row);
      res.json({ featured: featured.map(card), topSearches: topSearches.map(card), topSearchesTotal, initiatives: initiatives.map(card), themes, healthEmergencies, quotes, sectionCounts });
    }),
  );

  router.get(
    '/top-searches',
    validate(paginationSchema, 'query'),
    asyncHandler(async (req, res) => {
      const { page, per_page: perPage } = req.validatedQuery;
      const repo = new PublicationsRepository(req.db);
      const [rows, total] = await Promise.all([repo.mostVisited(perPage, (page - 1) * perPage), repo.countPublic()]);
      res.json(paginate(rows.map((row) => publicationCard(req.site, row)), total, req.validatedQuery));
    }),
  );

  router.get(
    '/autocomplete',
    asyncHandler(async (req, res) => {
      const term = String(req.query.term ?? '').trim();
      if (term.length < 2) return res.json({ data: [] });
      const rows = await new PublicationsRepository(req.db).autocomplete(term);
      return res.json({ data: rows.map((row) => ({ id: row.id, slug: row.slug, title: row.title })) });
    }),
  );

  router.get(
    '/taxonomy/:type/:key',
    asyncHandler(async (req, res) => {
      const lookups = new LookupsRepository(req.site);
      const finders = { tag: () => lookups.tagByKey(req.params.key), theme: () => lookups.themeByKey(req.params.key), 'sub-theme': () => lookups.subThemeByKey(req.params.key) };
      const finder = finders[req.params.type];
      const record = finder ? await finder() : null;
      if (!record) throw notFound();
      res.json({ data: record });
    }),
  );

  router.get(
    '/:key',
    asyncHandler(async (req, res) => {
      const repo = new PublicationsRepository(req.db);
      const row = await repo.findByKey(parseRecordKey(req.params.key), req.user);
      if (!row) throw notFound('Publication not found');

      const [tags, attachments, comments, countries] = await Promise.all([repo.tagsOf(row.id), repo.attachmentsOf(row.id), repo.approvedCommentsOf(row.id), repo.countriesOf(row.id)]);
      const [related, isFavourite, versionChain] = await Promise.all([
        repo.related(row, tags.map((tag) => tag.id)),
        req.user ? repo.isFavourite(row.id, req.user.id) : false,
        repo.versionChain(row),
      ]);
      await repo.incrementVisits(row.id);

      res.json({
        data: publicationDetail(req.site, row, { tags, attachments, comments, countries }),
        versions: versionChain.map((v) => ({ id: v.id, slug: v.slug, title: v.title, versionNo: v.is_version ? Number(v.version_no ?? 2) : 1, isOriginal: !v.is_version, current: v.id === row.id, path: publicationPath(v), createdAt: v.created_at })),
        related: related.map((item) => publicationCard(req.site, item)),
        isFavourite,
      });
    }),
  );

  router.post(
    '/:id/favourite',
    requireAuth,
    asyncHandler(async (req, res) => {
      const favourite = await new PublicationsRepository(req.db).toggleFavourite(Number(req.params.id), req.user.id, true);
      res.json({ isFavourite: favourite });
    }),
  );

  router.delete(
    '/:id/favourite',
    requireAuth,
    asyncHandler(async (req, res) => {
      const favourite = await new PublicationsRepository(req.db).toggleFavourite(Number(req.params.id), req.user.id, false);
      res.json({ isFavourite: favourite });
    }),
  );

  router.post(
    '/:id/comments',
    requireAuth,
    validate(commentSchema),
    asyncHandler(async (req, res) => {
      const siteSettings = await settings.forSite(req.site);
      const publicationId = Number(req.params.id);
      let parentId = null;
      if (req.body.parent_id) {
        const parent = await req.db('publication_comments').where({ id: req.body.parent_id, publication_id: publicationId }).first('id', 'parent_id');
        if (!parent) throw new HttpError(422, 'The comment you are replying to no longer exists.');
        // Keep threads one level deep: replying to a reply attaches to the top-level comment.
        parentId = parent.parent_id ?? parent.id;
      }
      await new PublicationsRepository(req.db).addComment(publicationId, req.user.id, req.body.comment, siteSettings.auto_approve_comments, parentId);
      res.status(201).json({ status: siteSettings.auto_approve_comments ? 'approved' : 'pending', parentId });
    }),
  );

  return router;
};
