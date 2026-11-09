/**
 * Seed the six African Union official languages into `site_languages` when the
 * table is empty (same rows the Laravel installer creates). Existing data wins.
 */
const AU_LANGUAGES = [
  { locale_code: 'en', name: 'English', google_translate_code: 'en', flag_emoji: '🇺🇸', sort_order: 1 },
  { locale_code: 'fr', name: 'Français', google_translate_code: 'fr', flag_emoji: '🇫🇷', sort_order: 2 },
  { locale_code: 'ar', name: 'العربية', google_translate_code: 'ar', flag_emoji: '🇸🇦', sort_order: 3 },
  { locale_code: 'es', name: 'Español', google_translate_code: 'es', flag_emoji: '🇪🇸', sort_order: 4 },
  { locale_code: 'pt', name: 'Português', google_translate_code: 'pt', flag_emoji: '🇵🇹', sort_order: 5 },
  { locale_code: 'sw', name: 'Kiswahili', google_translate_code: 'sw', flag_emoji: '🇰🇪', sort_order: 6 },
];

export const up = async (knex) => {
  if (!(await knex.schema.hasTable('site_languages'))) return;
  const existing = Number((await knex('site_languages').count({ total: '*' }).first()).total);
  if (existing > 0) return;
  await knex('site_languages').insert(AU_LANGUAGES.map((row) => ({ ...row, is_active: true })));
};

export const down = async () => {
  // Seed only; leave admin-managed rows alone on rollback.
};
