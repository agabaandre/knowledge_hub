import { Router } from 'express';
import { asyncHandler, HttpError } from '../http/errors.js';
import { directionOf } from './service.js';

/**
 * GET  /api/i18n/:locale   dictionary for one locale (English fallback merged in)
 * POST /api/i18n/locale    persist the visitor's choice in a cookie
 */
export const i18nRouter = ({ i18n, env }) => {
  const router = Router();

  router.get(
    '/api/i18n/:locale',
    asyncHandler(async (req, res) => {
      const locale = String(req.params.locale).toLowerCase();
      const available = (await i18n.languages(req.site)).map((lang) => lang.code);
      if (!available.includes(locale)) throw new HttpError(404, `Language "${locale}" is not enabled on this site.`);
      res.setHeader('Cache-Control', 'public, max-age=600');
      res.json({ locale, dir: directionOf(locale), messages: await i18n.messages(locale, req.site) });
    }),
  );

  router.post(
    '/api/i18n/locale',
    asyncHandler(async (req, res) => {
      const locale = String(req.body?.locale ?? '').toLowerCase();
      const available = (await i18n.languages(req.site)).map((lang) => lang.code);
      if (!available.includes(locale)) throw new HttpError(422, `Language "${locale}" is not enabled on this site.`);
      res.cookie('khub_locale', locale, { sameSite: 'lax', secure: env.appUrl.startsWith('https://'), maxAge: 365 * 24 * 3600 * 1000, path: '/' });
      res.json({ locale });
    }),
  );

  return router;
};
