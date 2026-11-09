import { Router } from 'express';
import { z } from 'zod';
import { asyncHandler, HttpError } from '../http/errors.js';
import { validate } from '../http/validate.js';
import { requirePermission } from '../auth/middleware.js';
import { paginationSchema, paginate } from '../http/pagination.js';
import { countOf, likePattern, toBool, now } from '../db/helpers.js';
import { userPhotoUrl } from '../../modules/publications/serializer.js';

const listSchema = paginationSchema.extend({ term: z.string().trim().max(200).optional() });
const updateSchema = z.object({
  status: z.boolean().optional(),
  role_id: z.coerce.number().int().positive().nullable().optional(),
  country_id: z.coerce.number().int().positive().nullable().optional(),
}).refine((value) => Object.keys(value).length > 0, 'Nothing to update');

/** Manage Users: list/search, activate/deactivate, assign role and country. */
export const usersRoutes = () => {
  const router = Router();

  router.get(
    '/users',
    asyncHandler(async (req, res) => {
      const { term, page, per_page: perPage } = listSchema.parse(req.query);
      const query = req.db('users')
        .leftJoin('country', 'country.id', 'users.country_id')
        .leftJoin('model_has_roles as mhr', 'mhr.model_id', 'users.id')
        .leftJoin('roles', 'roles.id', 'mhr.role_id');
      if (term) query.where((builder) => builder.whereILike('users.name', likePattern(term)).orWhereILike('users.email', likePattern(term)));
      const total = await countOf(req.db('users').modify((builder) => term && builder.where((inner) => inner.whereILike('name', likePattern(term)).orWhereILike('email', likePattern(term)))));
      const rows = await query
        .select('users.id', 'users.name', 'users.email', 'users.photo', 'users.is_photo_external', 'users.status', 'users.last_login_at', 'users.created_at', 'users.country_id', 'country.name as country_name', 'roles.id as role_id', 'roles.name as role_name')
        .orderBy('users.created_at', 'desc')
        .limit(perPage)
        .offset((page - 1) * perPage);
      res.json(paginate(rows.map((row) => ({
        id: row.id, name: row.name, email: row.email, photo: userPhotoUrl(req.site, row),
        active: toBool(row.status), lastLoginAt: row.last_login_at, createdAt: row.created_at,
        countryId: row.country_id, country: row.country_name ?? '', roleId: row.role_id, role: row.role_name ?? '',
      })), total, { page, per_page: perPage }));
    }),
  );

  router.put(
    '/users/:id',
    requirePermission('admin.users.edit'),
    validate(updateSchema),
    asyncHandler(async (req, res) => {
      const id = Number(req.params.id);
      if (id === req.user.id && req.body.status === false) throw new HttpError(422, 'You cannot deactivate your own account.');
      const user = await req.db('users').where({ id }).first('id');
      if (!user) throw new HttpError(404, 'User not found.');

      const updates = { updated_at: now() };
      if (req.body.status !== undefined) updates.status = req.body.status;
      if (req.body.country_id !== undefined) updates.country_id = req.body.country_id;
      await req.db('users').where({ id }).update(updates);

      if (req.body.role_id !== undefined) {
        if (id === req.user.id) throw new HttpError(422, 'You cannot change your own role.');
        await req.db('model_has_roles').where({ model_id: id }).del();
        if (req.body.role_id) {
          const role = await req.db('roles').where({ id: req.body.role_id }).first('id');
          if (!role) throw new HttpError(422, 'Unknown role.');
          await req.db('model_has_roles').insert({ role_id: req.body.role_id, model_id: id, model_type: 'App\\Models\\User' });
        }
      }
      res.json({ ok: true });
    }),
  );

  return router;
};
