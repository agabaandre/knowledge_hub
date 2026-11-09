import { TtlCache, MINUTE } from '../../core/cache.js';
import { toBool } from '../../core/db/helpers.js';

const cache = new TtlCache();

/** Active badge tiers, lowest threshold first (admin-configurable in badge_types). */
export const badgeTiers = (site) =>
  cache.remember(`badges:${site.id}`, 5 * MINUTE, async () => {
    if (!(await site.db.schema.hasTable('badge_types'))) return [];
    const rows = await site.db('badge_types').orderBy('contribution_threshold');
    return rows.filter((row) => toBool(row.is_active)).map((row) => ({
      id: row.id, name: row.name, slug: row.slug, threshold: Number(row.contribution_threshold), color: row.badge_color, description: row.description ?? '',
    }));
  });

export const forgetBadgeTiers = (site) => cache.forget(`badges:${site.id}`);

/** Highest tier the contribution count has reached, or null. */
export const badgeFor = (tiers, contributions) => {
  let earned = null;
  for (const tier of tiers) {
    if (contributions >= tier.threshold) earned = tier;
  }
  return earned;
};
