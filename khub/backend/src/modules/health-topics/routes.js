import { Router } from 'express';
import { healthTopicPath } from '@khub/shared';
import { asyncHandler, notFound } from '../../core/http/errors.js';
import { toBool } from '../../core/db/helpers.js';
import { PublicationsRepository } from '../publications/repository.js';
import { LookupsRepository } from '../publications/lookups-repository.js';
import { publicationCard } from '../publications/serializer.js';

const TOPIC_RESOURCES = 12;

const topic = (row) => ({
  id: row.id,
  slug: row.slug,
  name: row.name ?? row.tag_text,
  overview: row.overview,
  isHealthEmergency: toBool(row.is_health_emergency),
  resourceCount: Number(row.resource_count ?? 0),
  path: healthTopicPath(row),
});

/**
 * Health topics are tags flagged `is_health_topic`; emergencies are flagged
 * `is_health_emergency`. Each topic page lists the latest resources tagged with it.
 */
export const healthTopicsRouter = () => {
  const router = Router();

  router.get(
    '/',
    asyncHandler(async (req, res) => {
      const rows = await req.db('tags')
        .where('is_health_topic', true)
        .select('tags.id', 'tags.slug', 'tags.tag_text as name', 'tags.overview', 'tags.is_health_emergency')
        .select(req.db('publication_tags').count('*').whereRaw('publication_tags.tag_id = tags.id').as('resource_count'))
        .orderBy('tags.tag_text');
      const topics = rows.map(topic);
      res.json({ topics: topics.filter((item) => !item.isHealthEmergency), emergencies: topics.filter((item) => item.isHealthEmergency) });
    }),
  );

  router.get(
    '/:key',
    asyncHandler(async (req, res) => {
      const tag = await new LookupsRepository(req.site).tagByKey(req.params.key);
      if (!tag || !toBool(tag.is_health_topic)) throw notFound('Health topic not found');
      const { rows, total } = await new PublicationsRepository(req.db).search({ tag: String(tag.id) }, { page: 1, per_page: TOPIC_RESOURCES }, req.user);
      res.json({ data: { ...topic(tag), resourceCount: total }, resources: rows.map((row) => publicationCard(req.site, row)) });
    }),
  );

  return router;
};
