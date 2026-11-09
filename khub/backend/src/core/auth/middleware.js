import { verifySession, SESSION_COOKIE } from './jwt.js';
import { HttpError } from '../http/errors.js';
import { UsersRepository } from './users-repository.js';

/**
 * Reads the session (cookie or bearer token) and attaches `req.user` when
 * valid. Never fails: anonymous requests continue with `req.user = null`.
 */
export const authenticate = (secret) => async (req, res, next) => {
  req.user = null;
  const bearer = req.get('authorization');
  const token = bearer?.startsWith('Bearer ') ? bearer.slice(7) : req.cookies?.[SESSION_COOKIE];
  if (!token) return next();

  const payload = verifySession(secret, token);
  if (!payload || payload.site !== req.site.id) return next();

  try {
    req.user = await new UsersRepository(req.db).findForSession(Number(payload.sub));
  } catch (error) {
    return next(error);
  }
  return next();
};

export const requireAuth = (req, res, next) => {
  if (!req.user) return next(new HttpError(401, 'Authentication required'));
  return next();
};

export const requireAdmin = (req, res, next) => {
  if (!req.user) return next(new HttpError(401, 'Authentication required'));
  if (!req.user.access?.isAdmin) return next(new HttpError(403, 'Administrator access required'));
  return next();
};

/**
 * Gate one admin function behind a named permission (see @khub/shared/permissions).
 * Permissions come from the user's roles and any permission can be revoked
 * from any role — including Admin — on the Roles page.
 */
export const requirePermission = (name) => (req, res, next) => {
  if (!req.user) return next(new HttpError(401, 'Authentication required'));
  if (!req.user.permissions?.includes(name)) return next(new HttpError(403, `Missing permission: ${name}`));
  return next();
};

/** Moderators and above: Reviewer, Country, RCC, Overall. */
export const requireModerator = (req, res, next) => {
  if (!req.user) return next(new HttpError(401, 'Authentication required'));
  if (!req.user.access?.canModerate) return next(new HttpError(403, 'Moderator access required'));
  return next();
};
