import { Router } from 'express';
import { z } from 'zod';
import rateLimit from 'express-rate-limit';
import { validate } from '../http/validate.js';
import { asyncHandler, HttpError } from '../http/errors.js';
import { requireAuth } from './middleware.js';
import { hashPassword, verifyPassword } from './password.js';
import { signSession, SESSION_COOKIE, SESSION_DAYS, REMEMBER_DAYS } from './jwt.js';
import crypto from 'node:crypto';
import { UsersRepository } from './users-repository.js';
import { Mailer } from '../mail/mailer.js';
import { now } from '../db/helpers.js';

const loginSchema = z.object({ email: z.email(), password: z.string().min(1), remember: z.boolean().optional().default(false) });

const registerSchema = z.object({
  first_name: z.string().trim().min(1).max(50),
  last_name: z.string().trim().min(1).max(100),
  email: z.email(),
  password: z.string().min(8).max(200),
  organization: z.string().trim().max(150).optional(),
});

const loginLimiter = rateLimit({ windowMs: 15 * 60 * 1000, limit: 20, standardHeaders: true, legacyHeaders: false });

const publicUser = (user) => ({
  id: user.id,
  name: user.name,
  email: user.email,
  photo: user.photo,
  roles: user.roles,
  isAdmin: user.isAdmin,
  themePreference: user.theme_preference,
});

/**
 * POST /api/auth/login, /register, /logout — GET /api/auth/me
 * Session = signed JWT in an httpOnly cookie (web) and returned in the body (apps).
 */
export const authRouter = ({ env, settings }) => {
  const router = Router();

  const issueSession = (req, res, user, remember = false) => {
    const days = remember ? REMEMBER_DAYS : SESSION_DAYS;
    const token = signSession(env.secret, user, req.site.id, days);
    // Secure follows the public URL, not NODE_ENV: a production container served
    // over plain http (local Docker, internal VM) would otherwise drop the cookie.
    res.cookie(SESSION_COOKIE, token, { httpOnly: true, sameSite: 'lax', secure: env.appUrl.startsWith('https://'), maxAge: days * 24 * 3600 * 1000, path: '/' });
    return { token, user: publicUser(user) };
  };

  router.post(
    '/login',
    loginLimiter,
    validate(loginSchema),
    asyncHandler(async (req, res) => {
      const users = new UsersRepository(req.db);
      const record = await users.findByEmail(req.body.email);
      if (!record || !(await verifyPassword(req.body.password, record.password))) {
        throw new HttpError(401, 'Invalid email or password');
      }
      if (Number(record.status) === 0) throw new HttpError(403, 'This account is inactive');
      if (record.verification_token && !record.email_verified_at) {
        throw new HttpError(403, 'Please verify your email first — we sent you a verification link.');
      }

      await users.touchLogin(record.id);
      const user = await users.findForSession(record.id);
      res.json(issueSession(req, res, user, req.body.remember));
    }),
  );

  router.post(
    '/register',
    loginLimiter,
    validate(registerSchema),
    asyncHandler(async (req, res) => {
      const users = new UsersRepository(req.db);
      if (await users.findByEmail(req.body.email)) throw new HttpError(409, 'An account with this email already exists');

      const id = await users.create({
        firstName: req.body.first_name,
        lastName: req.body.last_name,
        email: req.body.email,
        organization: req.body.organization,
        passwordHash: await hashPassword(req.body.password),
      });
      // Verification applies only when a mail service is configured AND answering;
      // otherwise the account is auto-verified (the Laravel app's current behaviour).
      const mailer = new Mailer(req.site, settings);
      if (await mailer.isWorking()) {
        const token = crypto.randomBytes(24).toString('hex');
        await req.db('users').where({ id }).update({ verification_token: token, is_verified: 0 });
        const link = `${req.site.publicUrl}/api/auth/verify-email?token=${token}`;
        await mailer.send({
          to: req.body.email,
          subject: 'Verify your Knowledge Hub account',
          html: `<p>Welcome to the Knowledge Hub, ${req.body.first_name}!</p><p>Please confirm your email address:</p><p><a href="${link}">${link}</a></p>`,
        });
        return res.status(201).json({ verificationRequired: true, message: 'Account created — check your email for the verification link before signing in.' });
      }
      await req.db('users').where({ id }).update({ email_verified_at: now(), is_verified: 1, verification_token: null });

      const user = await users.findForSession(id);
      res.status(201).json(issueSession(req, res, user));
    }),
  );

  // The link from the verification email.
  router.get(
    '/verify-email',
    asyncHandler(async (req, res) => {
      const token = String(req.query.token ?? '').trim();
      if (!token) throw new HttpError(422, 'Missing verification token.');
      const record = await req.db('users').where({ verification_token: token }).first('id');
      if (!record) throw new HttpError(404, 'This verification link is invalid or was already used.');
      await req.db('users').where({ id: record.id }).update({ email_verified_at: now(), is_verified: 1, verification_token: null, updated_at: now() });
      res.redirect(`${req.site.pathPrefix || ''}/login?verified=1`);
    }),
  );

  router.post('/logout', (req, res) => {
    res.clearCookie(SESSION_COOKIE, { path: '/' });
    res.status(204).end();
  });

  router.get('/me', requireAuth, (req, res) => res.json({ user: publicUser(req.user) }));

  return router;
};
