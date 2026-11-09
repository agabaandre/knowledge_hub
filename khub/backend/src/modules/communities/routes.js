import { Router } from 'express';
import { z } from 'zod';
import { communityPath, parseRecordKey, truncate } from '@khub/shared';
import { asyncHandler, notFound } from '../../core/http/errors.js';
import { validate } from '../../core/http/validate.js';
import { paginationSchema, paginate } from '../../core/http/pagination.js';
import { countOf, likePattern, toBool } from '../../core/db/helpers.js';
import { userPhotoUrl, publicationCard } from '../publications/serializer.js';

const listSchema = paginationSchema.extend({ term: z.string().trim().max(200).optional() });

const COLUMNS = ['community_of_practices.id', 'community_of_practices.slug', 'community_of_practices.community_name', 'community_of_practices.description', 'community_of_practices.organisation', 'community_of_practices.is_public', 'community_of_practices.created_at', 'country.name as country_name'];

const publicQuery = (db) =>
  db('community_of_practices')
    .leftJoin('country', 'country.id', 'community_of_practices.country_id')
    .where('community_of_practices.is_active', true)
    .where('community_of_practices.is_public', true);

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
      const query = publicQuery(req.db);
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
        },
      });
    }),
  );

  return router;
};
