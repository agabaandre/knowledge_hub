import { TtlCache, HOUR } from '../cache.js';
import { toBool } from '../db/helpers.js';

/**
 * Reads the single-row `setting` table (same table the Laravel app uses) and
 * exposes it with branding fallbacks from site.json. Cached per site.
 */
const BOOLEAN_KEYS = [
  'use_seo_friendly_urls', 'menu_icons_enabled', 'show_featured', 'show_events', 'show_top_searches',
  'show_tags', 'show_quotes', 'show_health_themes', 'show_quiz', 'enable_ai_search',
  'header_logo_inverse', 'footer_logo_inverse', 'search_show_forums', 'search_show_communities',
  'show_publication_card_file_type_badge', 'enable_microsoft_login', 'enable_google_login',
  'enable_linkedin_login', 'auto_approve_comments',
];

export class SettingsService {
  #cache = new TtlCache();

  constructor(mediaUrl) {
    this.mediaUrl = mediaUrl;
  }

  async forSite(site) {
    return this.#cache.remember(`settings:${site.id}`, 24 * HOUR, () => this.#load(site));
  }

  forget(site) {
    this.#cache.forget(`settings:${site.id}`);
  }

  async #load(site) {
    const hasTable = await site.db.schema.hasTable('setting');
    const row = hasTable
      ? (await site.db('setting').where('status', 'active').first()) ?? (await site.db('setting').first())
      : null;

    const settings = { ...(row ?? {}) };
    for (const key of BOOLEAN_KEYS) {
      if (key in settings) settings[key] = toBool(settings[key]);
    }

    settings.site_name = settings.site_name || site.name;
    settings.title = settings.title || settings.site_name;
    settings.primary_color = settings.primary_color || site.branding.primaryColor;
    settings.secondary_color = settings.secondary_color || site.branding.secondaryColor;
    settings.logo = this.#brandingUrl(site, settings.logo || site.branding.logo);
    settings.favicon = this.#brandingUrl(site, settings.favicon || site.branding.favicon);
    settings.spotlight_banner = this.#brandingUrl(site, settings.spotlight_banner);
    settings.site_theme = settings.site_theme || 'theme1.';
    delete settings.default_password;
    return settings;
  }

  /** Laravel stored branding as a bare filename under uploads/config or a full URL. */
  #brandingUrl(site, value) {
    const text = String(value ?? '').trim();
    if (text === '') return '';
    if (/^(https?:)?\/\//i.test(text)) return text;
    const basename = text.split('/').pop();
    return this.mediaUrl(site, `uploads/config/${basename}`);
  }
}
