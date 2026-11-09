import { Router } from 'express';
import { z } from 'zod';
import { asyncHandler, HttpError } from '../http/errors.js';
import { validate } from '../http/validate.js';
import { requirePermission } from '../auth/middleware.js';
import { paginationSchema, paginate } from '../http/pagination.js';
import { countOf, likePattern, toBool, now } from '../db/helpers.js';
import { userPhotoUrl } from '../../modules/publications/serializer.js';
import { hashPassword } from '../auth/password.js';

const listSchema = paginationSchema.extend({ term: z.string().trim().max(200).optional() });
const profileFields = {
  first_name: z.string().trim().min(1).max(50).optional(),
  last_name: z.string().trim().min(1).max(100).optional(),
  email: z.email().optional(),
  phone_number: z.string().trim().max(30).nullable().optional(),
  organization_name: z.string().trim().max(150).nullable().optional(),
  job_title: z.string().trim().max(150).nullable().optional(),
  gender: z.enum(['Male', 'Female', 'Other']).nullable().optional(),
};
const updateSchema = z.object({
  ...profileFields,
  status: z.boolean().optional(),
  role_id: z.coerce.number().int().positive().nullable().optional(),
  country_id: z.coerce.number().int().positive().nullable().optional(),
  password: z.string().min(8).max(200).optional(),
}).refine((value) => Object.keys(value).length > 0, 'Nothing to update');
const createSchema = z.object({
  first_name: z.string().trim().min(1).max(50),
  last_name: z.string().trim().min(1).max(100),
  email: z.email(),
  password: z.string().min(8).max(200),
  phone_number: z.string().trim().max(30).optional(),
  organization_name: z.string().trim().max(150).optional(),
  job_title: z.string().trim().max(150).optional(),
  role_id: z.coerce.number().int().positive().nullable().optional(),
  country_id: z.coerce.number().int().positive().nullable().optional(),
});

/** Manage Users: list/search, activate/deactivate, assign role and country. */
export const usersRoutes = () => {
  const router = Router();

  // Create a user — admin-created accounts are verified and active immediately.
  router.post(
    '/users',
    requirePermission('admin.users.edit'),
    validate(createSchema),
    asyncHandler(async (req, res) => {
      const clash = await req.db('users').whereRaw('lower(email) = ?', [req.body.email.toLowerCase()]).first('id');
      if (clash) throw new HttpError(409, 'An account with this email already exists.');
      const [inserted] = await req.db('users').insert({
        name: `${req.body.first_name} ${req.body.last_name}`.trim(),
        first_name: req.body.first_name,
        last_name: req.body.last_name,
        email: req.body.email,
        password: await hashPassword(req.body.password),
        phone_number: req.body.phone_number ?? null,
        organization_name: req.body.organization_name ?? null,
        job_title: req.body.job_title ?? null,
        country_id: req.body.country_id ?? null,
        status: 1,
        is_approved: 1,
        is_verified: 1,
        email_verified_at: now(),
        created_at: now(),
        updated_at: now(),
      }).returning('id');
      const id = typeof inserted === 'object' ? inserted.id : inserted;
      if (req.body.role_id) await req.db('model_has_roles').insert({ role_id: req.body.role_id, model_id: id, model_type: 'App\\Models\\User' });
      res.status(201).json({ id });
    }),
  );

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
        .select('users.id', 'users.name', 'users.first_name', 'users.last_name', 'users.email', 'users.photo', 'users.is_photo_external', 'users.status', 'users.last_login_at', 'users.created_at', 'users.country_id', 'users.phone_number', 'users.organization_name', 'users.job_title', 'users.gender', 'country.name as country_name', 'roles.id as role_id', 'roles.name as role_name')
        .orderBy('users.created_at', 'desc')
        .limit(perPage)
        .offset((page - 1) * perPage);
      res.json(paginate(rows.map((row) => ({
        id: row.id, name: row.name, email: row.email, photo: userPhotoUrl(req.site, row),
        active: toBool(row.status), lastLoginAt: row.last_login_at, createdAt: row.created_at,
        countryId: row.country_id, country: row.country_name ?? '', roleId: row.role_id, role: row.role_name ?? '',
        firstName: row.first_name ?? '', lastName: row.last_name ?? '', phone: row.phone_number ?? '',
        organisation: row.organization_name ?? '', jobTitle: row.job_title ?? '', gender: row.gender ?? '',
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
      for (const field of ['first_name', 'last_name', 'email', 'phone_number', 'organization_name', 'job_title', 'gender']) {
        if (req.body[field] !== undefined) updates[field] = req.body[field];
      }
      if (req.body.first_name !== undefined || req.body.last_name !== undefined) {
        const current = await req.db('users').where({ id }).first('first_name', 'last_name');
        updates.name = `${req.body.first_name ?? current.first_name ?? ''} ${req.body.last_name ?? current.last_name ?? ''}`.trim();
      }
      if (req.body.email !== undefined) {
        const clash = await req.db('users').whereRaw('lower(email) = ?', [req.body.email.toLowerCase()]).whereNot({ id }).first('id');
        if (clash) throw new HttpError(422, 'Another account already uses this email.');
      }
      if (req.body.password) updates.password = await hashPassword(req.body.password);
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
