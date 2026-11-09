/**
 * Tables copied from the Laravel database, in foreign-key order. Each entry
 * may rename/transform columns; anything not listed copies as-is when the
 * target table has a column of the same name.
 *
 * `skipColumns` drops Laravel-only columns; `transform` adjusts a row.
 */
const dropTelescopeNoise = ['deleted_at'];

export const LARAVEL_TABLES = [
  { name: 'users', skipColumns: ['fcm_token', 'is_changed', 'pwd_changed', ...dropTelescopeNoise] },
  { name: 'roles' },
  { name: 'permissions' },
  { name: 'model_has_roles' },
  { name: 'role_has_permissions' },
  // Unmodelled setting columns go into setting.legacy_settings (JSON) instead of being dropped.
  { name: 'setting', skipColumns: ['default_password', 'installer_locked', 'installer_completed_at'], overflowColumn: 'legacy_settings' },
  { name: 'site_languages' },
  { name: 'static_links' },
  { name: 'hub_storage_settings', skipColumns: ['migration_status', 'migration_files_total', 'migration_files_done', 'migration_message'] },
  { name: 'thematic_area' },
  { name: 'sub_thematic_area' },
  { name: 'tags' },
  { name: 'data_categories' },
  { name: 'publication_categories' },
  { name: 'publication_sub_categories' },
  { name: 'file_type' },
  { name: 'licenses' },
  { name: 'regions' },
  { name: 'country' },
  { name: 'geographical_scope' },
  { name: 'author' },
  { name: 'publication', skipColumns: ['appealed_at'] },
  { name: 'publication_tags' },
  { name: 'publication_countries' },
  { name: 'publication_community_of_practices' },
  { name: 'publication_attachments' },
  { name: 'publication_summaries' },
  { name: 'publication_comments' },
  { name: 'publication_views' },
  { name: 'favourites' },
  { name: 'forums' },
  { name: 'forum_comments' },
  { name: 'forum_likes' },
  { name: 'forum_comment_likes' },
  { name: 'forum_tags' },
  { name: 'community_of_practices' },
  { name: 'community_of_practice_members' },
  { name: 'community_of_practice_tags' },
  { name: 'events', skipColumns: ['updated_by', 'orcid'] },
  { name: 'faqs' },
  { name: 'quotes' },
  { name: 'search_logs' },
  { name: 'subscribes' },
  { name: 'badge_types' },
  { name: 'user_lifetime_badges' },
];

/** Columns the target stores as booleans; MySQL sources deliver 0/1 which PostgreSQL rejects. */
export const coerceBooleans = (row, booleanColumns) => {
  for (const column of booleanColumns) {
    if (column in row && row[column] !== null) row[column] = row[column] === true || Number(row[column]) === 1;
  }
  return row;
};
