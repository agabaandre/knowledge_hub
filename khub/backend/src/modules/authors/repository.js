import { countOf, likePattern, toBool } from '../../core/db/helpers.js';
import { mediaUrl } from '../../core/storage/hub-storage.js';
import { userPhotoUrl } from '../publications/serializer.js';
import { badgeTiers, badgeFor } from './badges.js';

/**
 * Contributors & Authors. An entry is an `author` row (person or organisation)
 * merged with its linked user account (users.author_id) for photo, job title,
 * organisation and country — the shape the production /browse/authors page shows.
 * Contributions = approved publications + forum threads + forum comments.
 */
const LIST_COLUMNS = [
  'author.id', 'author.name', 'author.is_organsiation', 'author.logo', 'author.orcid',
  'users.id as user_id', 'users.photo', 'users.is_photo_external', 'users.job_title', 'users.organization_name',
  'country.name as country_name',
];

const photoOf = (site, row) => {
  if (row.user_id && row.photo && row.photo !== 'avatar.jpg') return userPhotoUrl(site, row);
  if (row.logo && row.logo !== 'author.png') return /^https?:/i.test(row.logo) ? row.logo : mediaUrl(site, `uploads/authors/${row.logo}`);
  return '';
};

export class ContributorsRepository {
  constructor(site) {
    this.site = site;
    this.db = site.db;
  }

  #base() {
    return this.db('author')
      .leftJoin('users', 'users.author_id', 'author.id')
      .leftJoin('country', 'country.id', 'users.country_id');
  }

  #withCounts(query) {
    return query
      .select(LIST_COLUMNS)
      .select(this.db('publication').count('*').whereRaw('publication.author_id = author.id').where({ is_approved: true, is_active: 'Active', is_version: false }).as('publication_count'))
      .select(this.db('forums').count('*').whereRaw('forums.created_by = users.id').where('forums.is_approved', true).as('forum_count'))
      .select(this.db('forum_comments').count('*').whereRaw('forum_comments.created_by = users.id').where('forum_comments.status', 'approved').as('comment_count'));
  }

  async #serialize(row) {
    const tiers = await badgeTiers(this.site);
    const contributions = Number(row.publication_count ?? 0) + Number(row.forum_count ?? 0) + Number(row.comment_count ?? 0);
    const badge = badgeFor(tiers, contributions);
    return {
      id: row.id,
      userId: row.user_id ?? null,
      name: row.name,
      isOrganisation: String(row.is_organsiation).toLowerCase() === 'yes' && !row.user_id,
      jobTitle: row.job_title ?? '',
      organization: row.organization_name ?? '',
      country: row.country_name ?? '',
      orcid: row.orcid ?? '',
      photo: photoOf(this.site, row),
      contributions,
      publicationCount: Number(row.publication_count ?? 0),
      forumCount: Number(row.forum_count ?? 0),
      commentCount: Number(row.comment_count ?? 0),
      badge: badge ? { name: badge.name, slug: badge.slug, color: badge.color } : null,
      path: `/authors/publications/${row.id}`,
    };
  }

  async list({ term, page, per_page: perPage }) {
    const query = this.#base();
    if (term) query.where((builder) => builder.whereILike('author.name', likePattern(term)).orWhereILike('users.organization_name', likePattern(term)));
    const total = await countOf(this.db('author').modify((builder) => term && builder.whereILike('name', likePattern(term))));
    // Bare aliases only: PostgreSQL rejects alias arithmetic in ORDER BY.
    const rows = await this.#withCounts(query)
      .orderBy([{ column: 'publication_count', order: 'desc' }, { column: 'forum_count', order: 'desc' }, { column: 'comment_count', order: 'desc' }])
      .orderBy('author.name')
      .limit(perPage)
      .offset((page - 1) * perPage);
    return { rows: await Promise.all(rows.map((row) => this.#serialize(row))), total };
  }

  async find(id) {
    const row = await this.#withCounts(this.#base().where('author.id', id)).first();
    if (!row) return null;
    const contributor = await this.#serialize(row);

    // Stored lifetime badge (for the acquired date), when the Laravel data has it.
    let awarded = null;
    if (row.user_id && (await this.db.schema.hasTable('user_lifetime_badges'))) {
      awarded = await this.db('user_lifetime_badges')
        .leftJoin('badge_types', 'badge_types.id', 'user_lifetime_badges.badge_type_id')
        .where('user_lifetime_badges.user_id', row.user_id)
        .first('badge_types.name', 'badge_types.slug', 'badge_types.badge_color as color', 'user_lifetime_badges.lifetime_contributions', 'user_lifetime_badges.last_upgraded_at');
    }
    contributor.badgeDetail = awarded?.name
      ? { name: awarded.name, slug: awarded.slug, color: awarded.color, lifetimeContributions: Number(awarded.lifetime_contributions), acquiredAt: awarded.last_upgraded_at }
      : contributor.badge
        ? { ...contributor.badge, lifetimeContributions: contributor.contributions, acquiredAt: null }
        : null;

    contributor.communities = row.user_id
      ? await this.db('community_of_practice_members as membership')
          .join('community_of_practices', 'community_of_practices.id', 'membership.community_of_practice_id')
          .where({ 'membership.user_id': row.user_id, 'membership.is_approved': true })
          .select('community_of_practices.id', 'community_of_practices.slug', 'community_of_practices.community_name as name')
          .select(this.db('community_of_practice_members').count('*').whereRaw('community_of_practice_members.community_of_practice_id = community_of_practices.id').where('is_approved', true).as('member_count'))
          .limit(10)
      : [];
    contributor.communities = contributor.communities.map((community) => ({ ...community, memberCount: Number(community.member_count) }));

    contributor.forumThreads = row.user_id
      ? await this.db('forums').where({ created_by: row.user_id, is_approved: true }).select('id', 'slug', 'forum_title as title', 'created_at', 'views').orderBy('created_at', 'desc').limit(10)
      : [];
    return contributor;
  }
}

export const serializeBadgeTier = (row) => ({
  id: row.id,
  name: row.name,
  slug: row.slug,
  description: row.description ?? '',
  threshold: Number(row.contribution_threshold),
  color: row.badge_color,
  sortOrder: Number(row.sort_order),
  isActive: toBool(row.is_active),
});
