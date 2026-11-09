import { Router } from 'express';
import path from 'node:path';
import { z } from 'zod';
import multer from 'multer';
import { asyncHandler, HttpError } from '../http/errors.js';
import { validate } from '../http/validate.js';
import { requireAuth } from './middleware.js';
import { UsersRepository } from './users-repository.js';
import { hashPassword, verifyPassword } from './password.js';
import { HubStorage } from '../storage/hub-storage.js';
import { userPhotoUrl } from '../../modules/publications/serializer.js';
import { now } from '../db/helpers.js';

/** Same field set as the Laravel account page (Personal information card). */
const profileSchema = z.object({
  first_name: z.string().trim().min(1).max(50),
  last_name: z.string().trim().min(1).max(100),
  organization: z.string().trim().max(150).optional().default(''),
  phone_number: z.string().trim().max(30).optional().default(''),
  job_title: z.string().trim().max(191).optional().default(''),
  orcid: z.string().trim().regex(/^$|^\d{4}-\d{4}-\d{4}-(\d{3}[\dX])$/, 'ORCID format is 0000-0000-0000-0000').optional().default(''),
  gender: z.enum(['', 'female', 'male', 'other', 'prefer_not_to_say']).optional().default(''),
  country_id: z.coerce.number().int().positive().nullable().optional(),
  language: z.string().trim().max(10).optional().default(''),
  theme_preference: z.enum(['light', 'dark']).optional().default('light'),
  is_subscribed: z.boolean().optional().default(false),
  interests: z.array(z.coerce.number().int().positive()).max(50).optional().default([]),
});

const passwordSchema = z.object({
  current_password: z.string().min(1),
  password: z.string().min(8).max(200),
  password_confirmation: z.string(),
}).refine((value) => value.password === value.password_confirmation, { message: 'The new passwords do not match.', path: ['password_confirmation'] });

/** JPG, PNG, GIF or WebP, max 5 MB — the limits the original portal states. */
const PHOTO_TYPES = { '.jpg': 'image/jpeg', '.jpeg': 'image/jpeg', '.png': 'image/png', '.gif': 'image/gif', '.webp': 'image/webp' };
const upload = multer({ storage: multer.memoryStorage(), limits: { fileSize: 5 * 1024 * 1024 } });

const profilePayload = async (site, db, user) => ({
  id: user.id,
  firstName: user.first_name ?? '',
  lastName: user.last_name ?? '',
  name: user.name,
  email: user.email,
  organization: user.organization_name ?? '',
  phone: user.phone_number ?? '',
  jobTitle: user.job_title ?? '',
  orcid: user.orcid ?? '',
  gender: user.gender ?? '',
  countryId: user.country_id ?? null,
  language: user.langauge ?? '',
  themePreference: user.theme_preference ?? 'light',
  subscribed: Boolean(user.is_subscribed),
  interests: (await db('user_preferences').where('user_id', user.id).pluck('subtheme_id')).map(Number),
  photo: userPhotoUrl(site, user),
  roles: user.roles,
  access: user.access,
});

const fullUser = async (users, db, id) => {
  const session = await users.findForSession(id);
  const extra = await db('users').select('phone_number', 'job_title', 'orcid', 'gender', 'langauge', 'is_subscribed', 'organization_name').where({ id }).first();
  return { ...session, ...extra };
};

/**
 * The account area, mirroring the original portal's profile page:
 *   GET  /api/account/profile    details + job-title options
 *   PUT  /api/account/profile    personal information, preferences, interests
 *   POST /api/account/password   security card (current + new password)
 *   POST /api/account/photo      profile photo upload
 */
export const accountRouter = () => {
  const router = Router();
  router.use(requireAuth);

  router.get(
    '/profile',
    asyncHandler(async (req, res) => {
      const users = new UsersRepository(req.db);
      const jobTitles = (await req.db.schema.hasTable('job_titles'))
        ? await req.db('job_titles').whereNotNull('name').orderBy('name').pluck('name')
        : [];
      res.json({ profile: await profilePayload(req.site, req.db, await fullUser(users, req.db, req.user.id)), jobTitles });
    }),
  );

  router.put(
    '/profile',
    validate(profileSchema),
    asyncHandler(async (req, res) => {
      const body = req.body;
      if (body.country_id) {
        const country = await req.db('country').where('id', body.country_id).first('id');
        if (!country) throw new HttpError(422, 'Unknown country.');
      }
      await req.db('users').where({ id: req.user.id }).update({
        name: `${body.first_name} ${body.last_name}`.trim(),
        first_name: body.first_name,
        last_name: body.last_name,
        organization_name: body.organization || null,
        phone_number: body.phone_number || null,
        job_title: body.job_title || null,
        orcid: body.orcid || null,
        gender: body.gender || null,
        country_id: body.country_id ?? null,
        langauge: body.language || null,
        theme_preference: body.theme_preference,
        is_subscribed: body.is_subscribed,
        updated_at: now(),
      });
      await req.db('user_preferences').where('user_id', req.user.id).del();
      if (body.interests.length) {
        await req.db('user_preferences').insert(body.interests.map((subthemeId) => ({ user_id: req.user.id, subtheme_id: subthemeId, created_at: now(), updated_at: now() })));
      }
      const users = new UsersRepository(req.db);
      res.json({ profile: await profilePayload(req.site, req.db, await fullUser(users, req.db, req.user.id)) });
    }),
  );

  router.post(
    '/password',
    validate(passwordSchema),
    asyncHandler(async (req, res) => {
      const record = await req.db('users').where({ id: req.user.id }).first('password');
      if (!(await verifyPassword(req.body.current_password, record?.password))) {
        throw new HttpError(422, 'The current password is incorrect.');
      }
      await req.db('users').where({ id: req.user.id }).update({ password: await hashPassword(req.body.password), updated_at: now() });
      res.json({ ok: true });
    }),
  );

  router.post(
    '/photo',
    upload.single('photo'),
    asyncHandler(async (req, res) => {
      if (!req.file) throw new HttpError(422, 'Attach an image as the "photo" field.');
      const extension = path.extname(req.file.originalname ?? '').toLowerCase();
      if (!(extension in PHOTO_TYPES) || req.file.mimetype !== PHOTO_TYPES[extension]) {
        throw new HttpError(422, 'Photos must be a JPG, PNG, GIF or WebP image.');
      }
      const filename = `u${req.user.id}-${Date.now()}${extension}`;
      await new HubStorage(req.site).write(`uploads/users/${filename}`, req.file.buffer);
      const users = new UsersRepository(req.db);
      await users.updatePhoto(req.user.id, filename);
      res.status(201).json({ profile: await profilePayload(req.site, req.db, await fullUser(users, req.db, req.user.id)) });
    }),
  );

  return router;
};
