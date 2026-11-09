import { addColumnIfMissing } from '../src/core/db/schema-guard.js';

/**
 * Bring fresh (non-Laravel) databases to parity with the Laravel `setting`
 * schema for every key the Configure page can edit. All nullable, so adopted
 * Laravel databases are untouched (addColumnIfMissing is a no-op there).
 */
const TEXT_COLUMNS = [
  'banner_text', 'footer_style', 'preloader_text', 'default_font_color', 'spotlight_animation',
  'spotlight_animation_speed', 'search_pagination_mode', 'researchgate', 'email_driver',
  'mail_from_name', 'mail_from_address', 'mail_host', 'mail_port', 'mail_username', 'mail_password',
  'mail_encryption', 'exchange_tenant_id', 'exchange_client_id', 'exchange_client_secret',
  'exchange_redirect_uri', 'exchange_scope', 'exchange_auth_method', 'google_client_id',
  'google_client_secret', 'microsoft_client_id', 'microsoft_client_secret', 'microsoft_tenant_id',
  'linkedin_client_id', 'linkedin_client_secret', 'publication_min_words', 'blocked_email_domains',
  'federation_api_token',
];
const BOOLEAN_COLUMNS = [
  'preloader_enabled', 'show_quiz', 'allow_email_password_accounts_social_login',
  'auto_approve_publications', 'enable_version_submission', 'block_disposable_email_registration',
];

export const up = async (knex) => {
  for (const column of TEXT_COLUMNS) {
    await addColumnIfMissing(knex, 'setting', column, (table) => table.text(column).nullable());
  }
  for (const column of BOOLEAN_COLUMNS) {
    await addColumnIfMissing(knex, 'setting', column, (table) => table.boolean(column).nullable());
  }
};

export const down = async () => {};
