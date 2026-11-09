import { Router } from 'express';
import { z } from 'zod';
import { asyncHandler, HttpError } from '../http/errors.js';
import { validate } from '../http/validate.js';
import { now, toBool } from '../db/helpers.js';

const languageSchema = z.object({
  id: z.coerce.number().int().positive().optional(),
  locale_code: z.string().trim().regex(/^[a-z]{2}(-[a-z]{2})?$/, 'Use a two-letter locale code, e.g. "fr"'),
  name: z.string().trim().min(2).max(120),
  flag_emoji: z.string().trim().max(16).optional().default(''),
  is_active: z.boolean(),
});
const updateSchema = z.object({ languages: z.array(languageSchema).min(1).max(30) });

/** Site languages (the language switcher's source): list, add, reorder, toggle. */
export const languagesRoutes = ({ i18n }) => {
  const router = Router();

  router.get(
    '/languages',
    asyncHandler(async (req, res) => {
      const rows = await req.db('site_languages').orderBy('sort_order').select('id', 'locale_code', 'name', 'flag_emoji', 'is_active', 'sort_order');
      res.json({ languages: rows.map((row) => ({ id: row.id, locale_code: row.locale_code, name: row.name, flag_emoji: row.flag_emoji ?? '', is_active: toBool(row.is_active) })) });
    }),
  );

  router.put(
    '/languages',
    validate(updateSchema),
    asyncHandler(async (req, res) => {
      const codes = req.body.languages.map((language) => language.locale_code);
      if (new Set(codes).size !== codes.length) throw new HttpError(422, 'Locale codes must be unique.');
      if (!req.body.languages.some((language) => language.is_active)) throw new HttpError(422, 'At least one language must stay active.');

      const keepIds = [];
      for (const [index, language] of req.body.languages.entries()) {
        const payload = { locale_code: language.locale_code, name: language.name, flag_emoji: language.flag_emoji || null, is_active: language.is_active, sort_order: index + 1, updated_at: now() };
        if (language.id) {
          await req.db('site_languages').where({ id: language.id }).update(payload);
          keepIds.push(language.id);
        } else {
          const [inserted] = await req.db('site_languages').insert({ ...payload, created_at: now() }).returning('id');
          keepIds.push(typeof inserted === 'object' ? inserted.id : inserted);
        }
      }
      await req.db('site_languages').whereNotIn('id', keepIds).del();
      i18n.forgetLanguages(req.site);
      const rows = await req.db('site_languages').orderBy('sort_order').select('id', 'locale_code', 'name', 'flag_emoji', 'is_active');
      res.json({ languages: rows.map((row) => ({ id: row.id, locale_code: row.locale_code, name: row.name, flag_emoji: row.flag_emoji ?? '', is_active: toBool(row.is_active) })) });
    }),
  );

  return router;
};
