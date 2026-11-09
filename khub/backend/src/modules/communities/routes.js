import { Router } from 'express';
import { z } from 'zod';
import { communityPath, parseRecordKey, truncate } from '@khub/shared';
import { asyncHandler, notFound } from '../../core/http/errors.js';
import { validate } from '../../core/http/validate.js';
import { paginationSchema, paginate } from '../../core/http/pagination.js';
import { countOf, likePattern, toBool, now } from '../../core/db/helpers.js';
import { requireAuth } from '../../core/auth/middleware.js';
import { slugify } from '@khub/shared';
import { userPhotoUrl, publicationCard } from '../publications/serializer.js';

const listSchema = paginationSchema.extend({ term: z.string().trim().max(200).optional() });

const COLUMNS = ['community_of_practices.id', 'community_of_practices.slug', 'community_of_practices.community_name', 'community_of_practices.description', 'community_of_practices.organisation', 'community_of_practices.is_public', 'community_of_practices.created_at', 'country.name as country_name'];

const publicQuery = (db) =>
  db('community_of_practices')
    .leftJoin('country', 'country.id', 'community_of_practices.country_id')
    .where('community_of_practices.is_active', true)
    .whereNull('community_of_practices.deleted_at');

const createSchema = z.object({
  name: z.string().trim().min(3).max(150),
  description: z.string().trim().min(10).max(5000),
  organisation: z.string().trim().max(150).optional().default(''),
  is_public: z.boolean().optional().default(true),
});

/** The viewer's membership row for a community, if any. */
const membershipOf = async (db, communityId, userId) =>
  (userId ? db('community_of_practice_members').where({ community_of_practice_id: communityId, user_id: userId }).first() : null);

const membershipStatus = (row) => (!row ? 'none' : !toBool(row.is_approved) ? 'pending' : toBool(row.is_admin) ? 'admin' : 'member');

const withMemberCount = (db, query) =>
  query.select(db('community_of_practice_members').count('*').whereRaw('community_of_practice_members.community_of_practice_id = community_of_practices.id').where('is_approved', true).as('member_count'));

const card = (row) => ({
  id: row.id,
  slug: row.slug,
  name: row.community_name,
  excerpt: truncate(row.description, 200),
  organisation: row.organisation,
  country: row.country_name,
  isPublic: toBool(row.is_public),
  memberCount: Number(row.member_count ?? 0),
  createdAt: row.created_at,
  path: communityPath(row),
});

export const communitiesRouter = () => {
  const router = Router();

  router.get(
    '/',
    validate(listSchema, 'query'),
    asyncHandler(async (req, res) => {
      const { term, page, per_page: perPage } = req.validatedQuery;
      const query = publicQuery(req.db).where('community_of_practices.is_public', true);
      if (term) query.whereILike('community_of_practices.community_name', likePattern(term));
      const total = await countOf(query);
      const rows = await withMemberCount(req.db, query.select(COLUMNS)).orderBy('community_of_practices.created_at', 'desc').limit(perPage).offset((page - 1) * perPage);
      res.json(paginate(rows.map(card), total, req.validatedQuery));
    }),
  );

  router.get(
    '/:key',
    asyncHandler(async (req, res) => {
      const { id, slug } = parseRecordKey(req.params.key);
      const query = withMemberCount(req.db, publicQuery(req.db).select(COLUMNS));
      const row = await (id ? query.where('community_of_practices.id', id) : query.where('community_of_practices.slug', slug)).first();
      if (!row) throw notFound('Community not found');
      const viewerMembership = await membershipOf(req.db, row.id, req.user?.id);
      if (!toBool(row.is_public) && membershipStatus(viewerMembership) === 'none' && !req.user?.isAdmin) {
        throw notFound('Community not found');
      }

      const [members, publications] = await Promise.all([
        req.db('community_of_practice_members').join('users', 'users.id', 'community_of_practice_members.user_id').where({ community_of_practice_id: row.id, 'community_of_practice_members.is_approved': true }).select('users.id', 'users.name', 'users.photo', 'users.is_photo_external', 'users.organization_name', 'community_of_practice_members.is_admin').limit(60),
        req.db('publication_community_of_practices')
          .join('publication', 'publication.id', 'publication_community_of_practices.publication_id')
          .leftJoin('author', 'author.id', 'publication.author_id')
          .leftJoin('file_type', 'file_type.id', 'publication.file_type_id')
          .leftJoin('sub_thematic_area', 'sub_thematic_area.id', 'publication.sub_thematic_area_id')
          .leftJoin('thematic_area', 'thematic_area.id', 'sub_thematic_area.thematic_area_id')
          .where({ community_of_practice_id: row.id, 'publication.is_approved': true, 'publication.is_active': 'Active' })
          .select('publication.*', 'author.name as author_name', 'file_type.name as file_type_name', 'file_type.icon as file_type_icon', 'sub_thematic_area.description as sub_theme_name', 'thematic_area.id as thematic_area_id', 'thematic_area.description as theme_name')
          .orderBy('publication.created_at', 'desc')
          .limit(12),
      ]);

      res.json({
        data: {
          ...card(row),
          description: row.description,
          members: members.map((member) => ({ id: member.id, name: member.name, organisation: member.organization_name, isAdmin: toBool(member.is_admin), photo: userPhotoUrl(req.site, member) })),
          publications: publications.map((item) => publicationCard(req.site, item)),
          membership: membershipStatus(viewerMembership),
        },
      });
    }),
  );

  // Join (public communities auto-approve; private ones queue for approval).
  router.post(
    '/:id/join',
    requireAuth,
    asyncHandler(async (req, res) => {
      const community = await req.db('community_of_practices').where({ id: Number(req.params.id), is_active: true }).whereNull('deleted_at').first();
      if (!community) throw notFound('Community not found');
      const existing = await membershipOf(req.db, community.id, req.user.id);
      if (existing) return res.json({ status: membershipStatus(existing) });
      const approved = toBool(community.is_public);
      await req.db('community_of_practice_members').insert({
        community_of_practice_id: community.id, user_id: req.user.id,
        is_approved: approved, is_active: true, is_admin: false, created_at: now(), updated_at: now(),
      });
      res.status(201).json({ status: approved ? 'member' : 'pending' });
    }),
  );

  router.delete(
    '/:id/leave',
    requireAuth,
    asyncHandler(async (req, res) => {
      await req.db('community_of_practice_members').where({ community_of_practice_id: Number(req.params.id), user_id: req.user.id }).del();
      res.json({ status: 'none' });
    }),
  );

  // Start a community — the creator becomes its first (admin) member.
  router.post(
    '/',
    requireAuth,
    validate(createSchema),
    asyncHandler(async (req, res) => {
      const baseSlug = slugify(req.body.name);
      const clash = await req.db('community_of_practices').where('slug', baseSlug).first('id');
      const [inserted] = await req.db('community_of_practices').insert({
        community_name: req.body.name,
        slug: clash ? `${baseSlug}-${Date.now().toString(36)}` : baseSlug,
        description: req.body.description,
        organisation: req.body.organisation || null,
        is_public: req.body.is_public,
        is_active: true,
        created_by: req.user.id,
        country_id: req.user.countryId ?? null,
        created_at: now(),
        updated_at: now(),
      }).returning('id');
      const communityId = typeof inserted === 'object' ? inserted.id : inserted;
      await req.db('community_of_practice_members').insert({
        community_of_practice_id: communityId, user_id: req.user.id,
        is_approved: true, is_active: true, is_admin: true, created_at: now(), updated_at: now(),
      });
      res.status(201).json({ id: communityId, path: communityPath({ id: communityId, slug: clash ? undefined : baseSlug }) });
    }),
  );

  return router;
};
