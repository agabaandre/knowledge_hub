import { Router } from 'express';
import { z } from 'zod';
import { asyncHandler, HttpError } from '../http/errors.js';
import { validate } from '../http/validate.js';
import { AiSettings } from '../ai/settings.js';
import { now } from '../db/helpers.js';

const LOCALE = z.string().trim().toLowerCase().regex(/^[a-z]{2}(-[a-z]{2})?$/);
const saveSchema = z.object({
  locale: LOCALE,
  key: z.string().trim().min(1).max(191),
  text: z.string().trim().max(5000), // empty text removes the override (reverts to the file value)
});
const aiFillSchema = z.object({
  locale: LOCALE,
  limit: z.coerce.number().int().min(1).max(200).optional().default(100),
});

const BATCH = 25;

/**
 * Language Management: every UI string, per locale, editable in place and
 * fillable in bulk via the provider-agnostic AI client. DB overrides win over
 * the shipped translation files; English is the source language.
 */
export const translationsRoutes = ({ i18n }) => {
  const router = Router();

  const upsert = async (db, locale, key, text) => {
    const existing = await db('language_lines').where({ locale, key }).first('id');
    if (existing) await db('language_lines').where({ id: existing.id }).update({ text, updated_at: now() });
    else await db('language_lines').insert({ locale, key, text, updated_at: now() });
  };

  router.get(
    '/translations',
    asyncHandler(async (req, res) => {
      const locale = LOCALE.parse(req.query.locale ?? 'fr');
      const [english, file, overrides] = await Promise.all([
        i18n.baselineOf('en'),
        i18n.baselineOf(locale),
        i18n.overrides(req.site, locale),
      ]);
      const rows = Object.entries(english).map(([key, source]) => ({
        key,
        group: key.split('.')[0],
        source,
        value: overrides[key] ?? file[key] ?? '',
        overridden: key in overrides,
      }));
      const missing = rows.filter((row) => !row.value).length;
      res.json({ locale, total: rows.length, missing, overridden: rows.filter((row) => row.overridden).length, rows });
    }),
  );

  router.put(
    '/translations',
    validate(saveSchema),
    asyncHandler(async (req, res) => {
      const { locale, key, text } = req.body;
      if (locale === 'en') throw new HttpError(422, 'English is the source language — edit the other locales.');
      if (text === '') await req.db('language_lines').where({ locale, key }).del();
      else await upsert(req.db, locale, key, text);
      i18n.forgetMessages(req.site);
      res.json({ ok: true });
    }),
  );

  router.post(
    '/translations/ai-fill',
    validate(aiFillSchema),
    asyncHandler(async (req, res) => {
      const { locale, limit } = req.body;
      if (locale === 'en') throw new HttpError(422, 'English is the source language.');
      const [english, file, overrides] = await Promise.all([
        i18n.baselineOf('en'),
        i18n.baselineOf(locale),
        i18n.overrides(req.site, locale),
      ]);
      const languageName = (await i18n.languages(req.site)).find((lang) => lang.code === locale)?.name ?? locale;
      const missing = Object.entries(english)
        .filter(([key]) => !(overrides[key] ?? file[key]))
        .slice(0, limit);
      if (missing.length === 0) return res.json({ ok: true, translated: 0, remaining: 0 });

      const ai = new AiSettings(req.site);
      let translated = 0;
      let provider = null;
      for (let start = 0; start < missing.length; start += BATCH) {
        const batch = missing.slice(start, start + BATCH);
        const payload = Object.fromEntries(batch);
        const result = await ai.completeForFeature('translate', {
          maxTokens: 3000,
          messages: [
            { role: 'system', content: `You translate user-interface strings for the Africa CDC Knowledge Hub from English to ${languageName} (${locale}). Keep placeholders like :name untouched, keep the tone short and formal, and reply with ONLY a JSON object mapping each input key to its translation — no prose, no code fences.` },
            { role: 'user', content: JSON.stringify(payload) },
          ],
        });
        if (!result.ok) {
          if (translated === 0) return res.status(503).json({ ok: false, error: result.error });
          break; // keep what we already saved
        }
        provider = result.provider;
        let parsed;
        try {
          parsed = JSON.parse(result.content.replace(/^```(?:json)?\s*|\s*```$/g, ''));
        } catch {
          continue; // malformed batch — skip it, the next run can retry
        }
        for (const [key, text] of Object.entries(parsed)) {
          if (typeof text !== 'string' || !(key in payload) || !text.trim()) continue;
          await upsert(req.db, locale, key, text.trim());
          translated += 1;
        }
      }
      i18n.forgetMessages(req.site);
      const remaining = Object.entries(english).filter(([key]) => !(overrides[key] ?? file[key])).length - translated;
      res.json({ ok: true, translated, remaining: Math.max(0, remaining), provider });
    }),
  );

  return router;
};
