import { addColumnIfMissing } from '../src/core/db/schema-guard.js';

/**
 * Columns found on live Laravel hubs (newer than the original schema dump).
 * Added with the same types so the Laravel → Node copy is lossless and an
 * adopted Laravel database (which already has them) is left untouched.
 */
const COLUMNS = [
  ['thematic_area', 'slug', (t) => t.string('slug', 191).nullable().unique()],
  ['sub_thematic_area', 'slug', (t) => t.string('slug', 191).nullable().unique()],
  ['author', 'slug', (t) => t.string('slug', 191).nullable().unique()],
  ['publication', 'content_updated_at', (t) => t.datetime('content_updated_at').nullable()],
  ['publication', 'last_visited_at', (t) => t.datetime('last_visited_at').nullable()],
  ['publication', 'public_availability', (t) => t.boolean('public_availability').notNullable().defaultTo(true)],
  ['publication', 'publication_language', (t) => t.string('publication_language', 32).nullable()],
  ['forums', 'public_availability', (t) => t.boolean('public_availability').notNullable().defaultTo(true)],
  ['data_categories', 'is_restricted', (t) => t.boolean('is_restricted').notNullable().defaultTo(false)],
  ['users', 'gender', (t) => t.string('gender', 16).nullable()],
  ['setting', 'front_body_font_size', (t) => t.string('front_body_font_size', 10).nullable()],
  ['setting', 'nav_font_size', (t) => t.string('nav_font_size', 10).nullable()],
  ['hub_storage_settings', 'offsite_backup_config', (t) => t.json('offsite_backup_config').nullable()],
  // Laravel's `setting` row has ~200 columns that change between releases; the
  // ones the Node app does not model are kept here as JSON instead of dropped.
  ['setting', 'legacy_settings', (t) => t.json('legacy_settings').nullable()],
];

export const up = async (knex) => {
  for (const [table, column, build] of COLUMNS) {
    await addColumnIfMissing(knex, table, column, build);
  }
};

export const down = async (knex) => {
  for (const [table, column] of [...COLUMNS].reverse()) {
    if (await knex.schema.hasColumn(table, column)) await knex.schema.alterTable(table, (t) => t.dropColumn(column));
  }
};
