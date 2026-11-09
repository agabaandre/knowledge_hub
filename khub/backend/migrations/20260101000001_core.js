import { createIfMissing } from "../src/core/db/schema-guard.js";
/**
 * Core tables: users, roles, settings, site languages, module toggles, storage settings.
 * Column names match the Laravel schema so data migrates 1:1.
 */
const timestamps = (table) => {
  table.timestamp('created_at').nullable();
  table.timestamp('updated_at').nullable();
};

export const up = async (knex) => {
  await createIfMissing(knex, 'users', (table) => {
    table.increments('id');
    table.string('name', 255).notNullable();
    table.string('email', 255).notNullable().unique();
    table.timestamp('email_verified_at').nullable();
    table.string('password', 191).nullable();
    table.string('remember_token', 100).nullable();
    table.timestamp('last_login_at').nullable();
    table.string('theme_preference', 20).notNullable().defaultTo('light');
    table.integer('author_id').unsigned().nullable();
    table.string('photo', 100).notNullable().defaultTo('avatar.jpg');
    table.boolean('is_photo_external').notNullable().defaultTo(false);
    table.string('first_name', 50).nullable();
    table.string('last_name', 255).notNullable().defaultTo('');
    table.string('organization_name', 150).nullable();
    table.boolean('is_approved').notNullable().defaultTo(false);
    table.boolean('is_verified').notNullable().defaultTo(false);
    table.boolean('status').notNullable().defaultTo(false);
    table.string('verification_token', 255).nullable();
    table.string('job_title', 255).nullable();
    table.string('phone_number', 255).nullable();
    table.string('orcid', 19).nullable();
    table.integer('country_id').unsigned().nullable();
    table.boolean('is_subscribed').notNullable().defaultTo(false);
    table.integer('access_level_id').unsigned().nullable().defaultTo(1);
    table.integer('administrative_unit_id').unsigned().nullable();
    table.string('langauge', 200).nullable();
    table.boolean('is_social_login').notNullable().defaultTo(false);
    table.string('social_provider', 191).nullable();
    timestamps(table);
  });

  await createIfMissing(knex, 'roles', (table) => {
    table.increments('id');
    table.string('name', 255).notNullable();
    table.string('guard_name', 255).notNullable().defaultTo('web');
    timestamps(table);
    table.unique(['name', 'guard_name']);
  });

  await createIfMissing(knex, 'permissions', (table) => {
    table.increments('id');
    table.string('name', 255).notNullable();
    table.string('guard_name', 255).notNullable().defaultTo('web');
    table.string('description', 255).nullable();
    timestamps(table);
    // No unique key: Laravel production data contains duplicate permission names.
  });

  await createIfMissing(knex, 'model_has_roles', (table) => {
    table.integer('role_id').unsigned().notNullable();
    table.string('model_type', 255).notNullable().defaultTo('App\\Models\\User');
    table.integer('model_id').unsigned().notNullable();
    table.primary(['role_id', 'model_id', 'model_type']);
  });

  await createIfMissing(knex, 'role_has_permissions', (table) => {
    table.integer('permission_id').unsigned().notNullable();
    table.integer('role_id').unsigned().notNullable();
    table.primary(['permission_id', 'role_id']);
  });

  await createIfMissing(knex, 'setting', (table) => {
    table.increments('id');
    table.string('config_name', 100).nullable();
    table.string('status', 10).notNullable().defaultTo('active');
    table.string('title', 255).nullable();
    table.string('site_name', 100).notNullable();
    table.text('seo_keywords').nullable();
    table.boolean('use_seo_friendly_urls').notNullable().defaultTo(true);
    table.text('site_description').nullable();
    table.text('address').nullable();
    table.string('email', 50).nullable();
    table.string('phone', 20).nullable();
    table.string('language', 100).nullable();
    table.string('timezone', 150).notNullable().defaultTo('Africa/Addis_Ababa');
    table.string('primary_color', 20).nullable();
    table.string('secondary_color', 20).nullable();
    table.string('logo', 400).nullable();
    table.string('favicon', 400).nullable();
    table.text('analytics_script').nullable();
    table.string('icon_font_color', 255).nullable();
    table.text('primary_text_color').nullable();
    table.text('links_active_color').nullable();
    table.text('spotlight_banner').nullable();
    table.string('spotlight_overlay_color', 20).nullable();
    table.integer('spotlight_overlay_opacity').unsigned().notNullable().defaultTo(35);
    table.string('gradient_start_color', 20).nullable();
    table.string('gradient_end_color', 20).nullable();
    table.boolean('auto_approve_comments').notNullable().defaultTo(true);
    table.boolean('enable_ai_search').notNullable().defaultTo(true);
    table.boolean('menu_icons_enabled').notNullable().defaultTo(false);
    table.string('slogan', 100).nullable();
    table.string('facebook', 191).nullable();
    table.string('twitter', 191).nullable();
    table.string('linkedin', 191).nullable();
    table.string('site_theme', 191).notNullable().defaultTo('theme1.');
    table.integer('logo_scale').unsigned().notNullable().defaultTo(80);
    table.string('primary_font', 100).nullable();
    table.boolean('header_logo_inverse').notNullable().defaultTo(false);
    table.boolean('footer_logo_inverse').notNullable().defaultTo(false);
    table.string('nav_style', 20).notNullable().defaultTo('colored');
    table.string('nav_link_color', 20).nullable();
    table.string('nav_link_hover_color', 20).nullable();
    table.string('nav_link_active_color', 20).nullable();
    table.string('nav_font_weight', 10).nullable().defaultTo('500');
    table.string('content_disclaimer', 1000).nullable();
    table.boolean('show_featured').notNullable().defaultTo(false);
    table.boolean('show_events').notNullable().defaultTo(false);
    table.boolean('show_top_searches').notNullable().defaultTo(false);
    table.boolean('show_tags').notNullable().defaultTo(false);
    table.boolean('show_quotes').notNullable().defaultTo(false);
    table.boolean('show_health_themes').notNullable().defaultTo(true);
    table.string('section_title_health_themes', 255).nullable();
    table.string('section_title_top_searches', 255).nullable();
    table.string('section_title_recommended', 255).nullable();
    table.string('section_title_flagship_initiatives', 255).nullable();
    table.string('theme_card_opacity', 10).nullable();
    table.integer('theme_cards_per_row').unsigned().notNullable().defaultTo(4);
    table.boolean('search_show_forums').notNullable().defaultTo(true);
    table.boolean('search_show_communities').notNullable().defaultTo(true);
    table.boolean('show_publication_card_file_type_badge').notNullable().defaultTo(true);
    table.boolean('enable_microsoft_login').notNullable().defaultTo(false);
    table.boolean('enable_google_login').notNullable().defaultTo(false);
    table.boolean('enable_linkedin_login').notNullable().defaultTo(false);
    table.json('partner_logos').nullable();
  });

  await createIfMissing(knex, 'site_languages', (table) => {
    table.increments('id');
    table.string('locale_code', 32).notNullable();
    table.string('name', 120).notNullable();
    table.string('google_translate_code', 32).nullable();
    table.string('flag_emoji', 16).nullable();
    table.integer('sort_order').unsigned().notNullable().defaultTo(0);
    table.boolean('is_active').notNullable().defaultTo(true);
    timestamps(table);
  });

  await createIfMissing(knex, 'static_links', (table) => {
    table.increments('id');
    table.string('title', 191).notNullable();
    table.integer('order').notNullable().defaultTo(0);
    table.string('link', 191).notNullable();
    table.boolean('open_in_new_tab').notNullable().defaultTo(false);
    timestamps(table);
  });

  await createIfMissing(knex, 'module_settings', (table) => {
    table.increments('id');
    table.string('module', 64).notNullable().unique();
    table.boolean('enabled').notNullable().defaultTo(true);
    table.integer('updated_by').unsigned().nullable();
    timestamps(table);
  });

  await createIfMissing(knex, 'hub_storage_settings', (table) => {
    table.increments('id');
    table.string('site_storage_id', 191).nullable();
    table.string('files_driver', 32).notNullable().defaultTo('internal');
    table.string('local_files_root', 512).nullable();
    table.string('sql_backup_root', 512).nullable();
    table.json('cloud_config').nullable();
    table.boolean('auto_sql_backup').notNullable().defaultTo(true);
    table.integer('sql_backup_retention_days').unsigned().notNullable().defaultTo(30);
    table.timestamp('last_sql_backup_at').nullable();
    table.string('last_sql_backup_path', 512).nullable();
    timestamps(table);
  });
};

export const down = async (knex) => {
  for (const table of ['hub_storage_settings', 'module_settings', 'static_links', 'site_languages', 'setting', 'role_has_permissions', 'model_has_roles', 'permissions', 'roles', 'users']) {
    await knex.schema.dropTableIfExists(table);
  }
};
