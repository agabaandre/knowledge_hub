import jwt from 'jsonwebtoken';

export const SESSION_COOKIE = 'khub_session';
export const SESSION_DAYS = 7;
export const REMEMBER_DAYS = 30;

export const signSession = (secret, user, siteId, days = SESSION_DAYS) =>
  jwt.sign({ sub: String(user.id), site: siteId, role: user.role ?? null }, secret, { expiresIn: `${days}d` });

export const verifySession = (secret, token) => {
  try {
    return jwt.verify(token, secret);
  } catch {
    return null;
  }
};
