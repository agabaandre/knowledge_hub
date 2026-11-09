import fs from 'node:fs/promises';
import path from 'node:path';
import { TtlCache, HOUR, MINUTE } from '../cache.js';

const baselines = new TtlCache();
const languagesCache = new TtlCache();
const overridesCache = new TtlCache();

const RTL_LOCALES = new Set(['ar']);
export const DEFAULT_LOCALE = 'en';

/** The six African Union official languages — the fallback when the DB has none. */
export const AU_LANGUAGES = Object.freeze([
  { code: 'en', name: 'English', flag: '🇺🇸' },
  { code: 'fr', name: 'Français', flag: '🇫🇷' },
  { code: 'ar', name: 'العربية', flag: '🇸🇦' },
  { code: 'es', name: 'Español', flag: '🇪🇸' },
  { code: 'pt', name: 'Português', flag: '🇵🇹' },
  { code: 'sw', name: 'Kiswahili', flag: '🇰🇪' },
]);

export const directionOf = (locale) => (RTL_LOCALES.has(locale) ? 'rtl' : 'ltr');

/**
 * Translations: JSON baselines exported from the Laravel lang files
 * (backend/i18n/{locale}.json), with English as the fallback layer.
 * Languages: the `site_languages` table of the current database.
 */
export class I18nService {
  constructor(env) {
    this.dir = path.resolve(env.backendRoot, 'i18n');
  }

  async #baseline(locale) {
    return baselines.remember(`baseline:${locale}`, 12 * HOUR, async () => {
      try {
        return JSON.parse(await fs.readFile(path.join(this.dir, `${locale}.json`), 'utf8'));
      } catch {
        return null;
      }
    });
  }

  forgetLanguages(site) {
    languagesCache.forget(`languages:${site.id}`);
  }

  forgetMessages(site) {
    overridesCache.forgetPrefix(`overrides:${site.id}:`);
  }

  /** DB overrides from Language Management (language_lines), flat dotted keys. */
  async overrides(site, locale) {
    if (!site) return {};
    return overridesCache.remember(`overrides:${site.id}:${locale}`, 5 * MINUTE, async () => {
      if (!(await site.db.schema.hasTable('language_lines'))) return {};
      const rows = await site.db('language_lines').where({ locale }).select('key', 'text');
      return Object.fromEntries(rows.map((row) => [row.key, row.text]));
    });
  }

  /** Active languages for a site, ordered; DB rows win, AU list is the fallback. */
  async languages(site) {
    return languagesCache.remember(`languages:${site.id}`, 10 * MINUTE, async () => {
      let rows = [];
      if (await site.db.schema.hasTable('site_languages')) {
        rows = await site.db('site_languages')
          .where('is_active', true)
          .orderBy('sort_order')
          .select('locale_code', 'name', 'flag_emoji');
      }
      const fromDb = rows
        .map((row) => ({ code: String(row.locale_code).toLowerCase().slice(0, 5), name: row.name, flag: row.flag_emoji ?? '' }))
        .filter((lang) => /^[a-z]{2}(-[a-z]{2})?$/.test(lang.code));
      const list = fromDb.length > 0 ? fromDb : AU_LANGUAGES.filter((lang) => site.locales.includes(lang.code));
      return (list.length > 0 ? list : [...AU_LANGUAGES]).map((lang) => ({ ...lang, dir: directionOf(lang.code) }));
    });
  }

  /** Resolve the request locale: cookie → Accept-Language → site default. */
  async resolveLocale(req) {
    const available = (await this.languages(req.site)).map((lang) => lang.code);
    const cookie = String(req.cookies?.khub_locale ?? '').toLowerCase();
    if (available.includes(cookie)) return cookie;
    const header = String(req.get('accept-language') ?? '');
    for (const part of header.split(',')) {
      const code = part.trim().slice(0, 2).toLowerCase();
      if (available.includes(code)) return code;
    }
    return available.includes(DEFAULT_LOCALE) ? DEFAULT_LOCALE : (available[0] ?? DEFAULT_LOCALE);
  }

  /**
   * Full dictionary for a locale: English baseline, overlaid with the locale's
   * file strings, overlaid with the site's DB overrides (Language Management).
   */
  async messages(locale, site = null) {
    const [english, requested, overrides] = await Promise.all([
      this.#baseline(DEFAULT_LOCALE),
      this.#baseline(locale),
      this.overrides(site, locale),
    ]);
    return { ...(english ?? {}), ...(requested ?? {}), ...overrides };
  }

  /** The English baseline, used as the source column in Language Management. */
  async baselineOf(locale) {
    return (await this.#baseline(locale)) ?? {};
  }
}

/** `:name`-style replacements, matching Laravel's trans() parameters. */
export const interpolate = (text, params = {}) =>
  String(text).replace(/:(\w+)/g, (match, name) => (name in params ? String(params[name]) : match));
