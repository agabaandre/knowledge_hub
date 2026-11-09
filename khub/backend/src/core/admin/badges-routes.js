import { Router } from 'express';
import { z } from 'zod';
import { asyncHandler, HttpError } from '../http/errors.js';
import { validate } from '../http/validate.js';
import { now } from '../db/helpers.js';
import { forgetBadgeTiers } from '../../modules/authors/badges.js';
import { serializeBadgeTier } from '../../modules/authors/repository.js';

const tierSchema = z.object({
  id: z.coerce.number().int().positive().optional(),
  name: z.string().trim().min(2).max(191),
  slug: z.string().trim().regex(/^[a-z0-9-]{2,50}$/),
  description: z.string().trim().max(1000).optional().default(''),
  threshold: z.coerce.number().int().min(1).max(100000),
  color: z.string().trim().regex(/^#[0-9a-fA-F]{3,8}$/),
  isActive: z.boolean(),
});
const updateSchema = z.object({ tiers: z.array(tierSchema).min(1).max(12) });

/** GET/PUT /api/admin/badges — configure the contributor badge tiers (badge_types). */
export const badgesRoutes = () => {
  const router = Router();

  router.get(
    '/badges',
    asyncHandler(async (req, res) => {
      const rows = await req.db('badge_types').orderBy('contribution_threshold');
      res.json({ tiers: rows.map(serializeBadgeTier) });
    }),
  );

  router.put(
    '/badges',
    validate(updateSchema),
    asyncHandler(async (req, res) => {
      const slugs = req.body.tiers.map((tier) => tier.slug);
      if (new Set(slugs).size !== slugs.length) throw new HttpError(422, 'Badge slugs must be unique.');

      const keepIds = [];
      for (const [index, tier] of req.body.tiers.entries()) {
        const payload = {
          name: tier.name, slug: tier.slug, description: tier.description || null,
          contribution_threshold: tier.threshold, badge_color: tier.color,
          sort_order: index + 1, is_active: tier.isActive, updated_at: now(),
        };
        if (tier.id) {
          await req.db('badge_types').where({ id: tier.id }).update(payload);
          keepIds.push(tier.id);
        } else {
          const [inserted] = await req.db('badge_types').insert({ ...payload, created_at: now() }).returning('id');
          keepIds.push(typeof inserted === 'object' ? inserted.id : inserted);
        }
      }
      await req.db('badge_types').whereNotIn('id', keepIds).update({ is_active: false, updated_at: now() });
      forgetBadgeTiers(req.site);
      const rows = await req.db('badge_types').orderBy('contribution_threshold');
      res.json({ tiers: rows.map(serializeBadgeTier) });
    }),
  );

  return router;
};
