import { Router } from 'express';
import { z } from 'zod';
import { TtlCache } from '../cache.js';
import { asyncHandler, HttpError } from '../http/errors.js';
import { validate } from '../http/validate.js';
import { requirePermission } from '../auth/middleware.js';
import { now } from '../db/helpers.js';

const cache = new TtlCache({ name: 'blocked-ips' });
const CACHE_MS = 30 * 1000;
const cacheKey = (site) => `blocked:${site.id}`;

/** Normalise IPv4-mapped IPv6 (::ffff:1.2.3.4) and loopback aliases so comparisons match. */
export const normalizeIp = (ip) => {
  const clean = String(ip ?? '').trim().toLowerCase().replace(/^::ffff:/, '');
  return clean === '::1' ? '127.0.0.1' : clean;
};

const blockedSet = (db, site) => cache.remember(cacheKey(site), CACHE_MS, async () => {
  if (!(await db.schema.hasTable('blocked_ips'))) return new Set();
  const rows = await db('blocked_ips').select('ip');
  return new Set(rows.map((row) => normalizeIp(row.ip)));
});

/**
 * Deny requests from blocked IPs (suspicious activity found in the access
 * logs). Overall administrators are never blocked, so an admin cannot lock
 * themselves out of the panel that manages the list.
 */
export const ipBlockMiddleware = () => async (req, res, next) => {
  try {
    if (!req.db || !req.site) return next();
    if (req.user?.access?.level === 'overall') return next();
    const blocked = await blockedSet(req.db, req.site);
    if (blocked.has(normalizeIp(req.ip))) {
      return res.status(403).json({ error: 'Access from your network has been blocked. Contact the hub administrators.' });
    }
    return next();
  } catch {
    return next(); // never let the blocklist take the site down
  }
};

const blockSchema = z.object({
  ip: z.string().trim().regex(/^[0-9a-fA-F:.]{3,45}$/, 'Enter a valid IPv4 or IPv6 address.'),
  reason: z.string().trim().max(500).optional().default(''),
});

/** Admin endpoints: list, block, and unblock IPs. */
export const securityRoutes = () => {
  const router = Router();

  router.get(
    '/security/blocked',
    asyncHandler(async (req, res) => {
      const rows = await req.db('blocked_ips')
        .leftJoin('users', 'users.id', 'blocked_ips.created_by')
        .orderBy('blocked_ips.created_at', 'desc')
        .select('blocked_ips.id', 'blocked_ips.ip', 'blocked_ips.reason', 'blocked_ips.created_at', 'users.name as blocked_by');
      res.json({ blocked: rows });
    }),
  );

  router.post(
    '/security/blocked',
    requirePermission('admin.security.manage'),
    validate(blockSchema),
    asyncHandler(async (req, res) => {
      const ip = normalizeIp(req.body.ip);
      const { reason } = req.body;
      if (ip === normalizeIp(req.ip)) throw new HttpError(422, 'That is your current IP address — blocking it would lock this session out.');
      const existing = await req.db('blocked_ips').where({ ip }).first('id');
      if (existing) throw new HttpError(422, `${ip} is already blocked.`);
      const [inserted] = await req.db('blocked_ips').insert({ ip, reason: reason || null, created_by: req.user.id, created_at: now() }).returning('id');
      cache.forget(cacheKey(req.site));
      res.status(201).json({ id: typeof inserted === 'object' ? inserted.id : inserted });
    }),
  );

  router.delete(
    '/security/blocked/:id',
    requirePermission('admin.security.manage'),
    asyncHandler(async (req, res) => {
      await req.db('blocked_ips').where({ id: Number(req.params.id) }).del();
      cache.forget(cacheKey(req.site));
      res.status(204).end();
    }),
  );

  return router;
};
