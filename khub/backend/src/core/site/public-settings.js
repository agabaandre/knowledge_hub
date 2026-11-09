/**
 * The only `setting` fields sent to browsers. The table also holds API keys,
 * SMTP and SSO details (and, after migration, `legacy_settings`), so the
 * manifest must never return the raw row.
 */
const PUBLIC_SETTING_KEYS = [
  'site_name', 'title', 'slogan', 'site_description', 'seo_keywords', 'use_seo_friendly_urls',
  'logo', 'favicon', 'logo_scale', 'header_logo_inverse', 'footer_logo_inverse', 'spotlight_banner',
  'primary_color', 'secondary_color', 'primary_text_color', 'links_active_color', 'primary_font',
  'front_body_font_size', 'default_font_color', 'nav_font_size',
  'nav_style', 'nav_link_color', 'nav_link_hover_color', 'nav_link_active_color', 'nav_font_weight', 'menu_icons_enabled',
  'address', 'email', 'phone', 'facebook', 'twitter', 'linkedin',
  'show_featured', 'show_events', 'show_top_searches', 'show_tags', 'show_quotes',
  'enable_version_submission', 'show_health_themes',
  'section_title_health_themes', 'section_title_top_searches', 'section_title_recommended', 'section_title_flagship_initiatives',
  'theme_cards_per_row', 'theme_card_opacity', 'content_disclaimer',
  'enable_microsoft_login', 'enable_google_login', 'enable_linkedin_login',
];

export const publicSettings = (settings) => Object.fromEntries(PUBLIC_SETTING_KEYS.filter((key) => key in settings).map((key) => [key, settings[key]]));
