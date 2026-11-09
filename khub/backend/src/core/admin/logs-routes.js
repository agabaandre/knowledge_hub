import { Router } from 'express';
import { asyncHandler } from '../http/errors.js';
import { requirePermission } from '../auth/middleware.js';

/** User Logs + Site Access Logs: sign-ins, registrations, and search activity. */
export const logsRoutes = () => {
  const router = Router();

  router.get(
    '/logs',
    asyncHandler(async (req, res) => {
      const [logins, registrations, searches] = await Promise.all([
        req.db('users').whereNotNull('last_login_at').orderBy('last_login_at', 'desc').limit(100).select('id', 'name', 'email', 'last_login_at', 'photo', 'is_photo_external', 'status'),
        req.db('users').orderBy('created_at', 'desc').limit(100).select('id', 'name', 'email', 'created_at', 'photo', 'is_photo_external', 'status'),
        req.db('search_logs')
          .leftJoin('users', 'users.id', 'search_logs.user_id')
          .orderBy('search_logs.created_at', 'desc')
          .limit(300)
          .select('search_logs.id', 'search_logs.term', 'search_logs.results_count', 'search_logs.ip_address',
            'search_logs.request_path', 'search_logs.user_agent', 'search_logs.created_at', 'search_logs.user_id',
            'users.name as user_name'),
      ]);
      res.json({ logins, registrations, searches });
    }),
  );

  return router;
};

/** Mailing list: newsletter subscribers (the `subscribes` table + opted-in users). */
export const mailingRoutes = () => {
  const router = Router();

  router.get(
    '/mailing',
    asyncHandler(async (req, res) => {
      const [subscribers, optedInUsers] = await Promise.all([
        req.db('subscribes').orderBy('created_at', 'desc').limit(500).select('id', 'email', 'name', 'status', 'created_at'),
        req.db('users').where('is_subscribed', true).orderBy('created_at', 'desc').limit(500).select('id', 'name', 'email', 'created_at', 'photo', 'is_photo_external'),
      ]);
      res.json({ subscribers, optedInUsers });
    }),
  );

  router.delete(
    '/mailing/:id',
    requirePermission('admin.mailing.edit'),
    asyncHandler(async (req, res) => {
      await req.db('subscribes').where({ id: Number(req.params.id) }).del();
      res.status(204).end();
    }),
  );

  return router;
};
