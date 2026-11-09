import { createIfMissing } from "../src/core/db/schema-guard.js";
/**
 * Taxonomy and reference tables: themes, tags, categories, file types, geography, authors, licences.
 */
const timestamps = (table) => {
  table.timestamp('created_at').nullable();
  table.timestamp('updated_at').nullable();
};

export const up = async (knex) => {
  await createIfMissing(knex, 'thematic_area', (table) => {
    table.increments('id');
    table.string('description', 191).notNullable();
    table.text('detailed_description').nullable();
    table.string('icon', 191).notNullable().defaultTo('fa-circle');
    table.integer('display_order').unsigned().notNullable().defaultTo(0);
    table.integer('display_index').notNullable().defaultTo(1);
    timestamps(table);
  });

  await createIfMissing(knex, 'sub_thematic_area', (table) => {
    table.increments('id');
    table.string('description', 191).notNullable();
    table.text('detailed_description').nullable();
    table.string('icon', 191).notNullable().defaultTo('fa-circle');
    table.integer('thematic_area_id').unsigned().notNullable().index();
    timestamps(table);
  });

  await createIfMissing(knex, 'tags', (table) => {
    table.increments('id');
    table.string('tag_text', 100).notNullable();
    table.string('slug', 191).nullable().unique();
    table.boolean('is_health_topic').notNullable().defaultTo(true);
    table.boolean('is_health_emergency').notNullable().defaultTo(false);
    table.text('overview').nullable();
  });

  await createIfMissing(knex, 'data_categories', (table) => {
    table.increments('id');
    table.string('category_name', 255).notNullable();
    table.string('url_path', 255).nullable();
    table.string('slug', 255).nullable();
    table.boolean('show_on_menu').notNullable().defaultTo(false);
    table.string('required_permission', 255).nullable();
    table.boolean('is_special').notNullable().defaultTo(false);
    table.boolean('is_dashboard').notNullable().defaultTo(false);
    timestamps(table);
  });

  await createIfMissing(knex, 'publication_categories', (table) => {
    table.increments('id');
    table.integer('parent_id').unsigned().nullable();
    table.string('category_name', 255).notNullable();
    table.string('category_desc', 255).nullable();
    timestamps(table);
  });

  await createIfMissing(knex, 'publication_sub_categories', (table) => {
    table.increments('id');
    table.integer('publication_category_id').unsigned().notNullable().index();
    table.string('category_name', 191).notNullable();
    table.string('category_desc', 191).nullable();
    timestamps(table);
  });

  await createIfMissing(knex, 'file_type', (table) => {
    table.increments('id');
    table.string('name', 200).notNullable();
    table.string('icon', 100).notNullable().defaultTo('fa-file');
    table.integer('is_downloadable').notNullable().defaultTo(0);
    table.string('mime_types', 191).nullable();
  });

  await createIfMissing(knex, 'licenses', (table) => {
    table.increments('id');
    table.string('name', 255).notNullable();
    table.string('short_name', 100).nullable();
    table.text('description').nullable();
    table.string('url', 500).nullable();
    table.boolean('is_active').notNullable().defaultTo(true);
    table.integer('sort_order').notNullable().defaultTo(0);
    timestamps(table);
  });

  await createIfMissing(knex, 'regions', (table) => {
    table.increments('id');
    table.string('region_name', 100).notNullable();
  });

  await createIfMissing(knex, 'country', (table) => {
    table.increments('id');
    table.string('national', 100).notNullable().defaultTo('');
    table.string('name', 100).notNullable();
    table.string('slug', 191).nullable().unique();
    table.string('iso_code', 2).nullable();
    table.string('iso3_code', 3).nullable();
    table.integer('numcode').nullable();
    table.string('phonecode', 191).nullable();
    table.integer('region_id').notNullable().defaultTo(0);
    table.double('longitude').notNullable().defaultTo(0);
    table.double('latitude').notNullable().defaultTo(0);
    table.integer('population').notNullable().defaultTo(0);
    table.string('color', 100).notNullable().defaultTo('');
    table.text('svg_path').nullable();
    table.string('flag', 255).nullable();
    table.string('base_url', 191).nullable();
    table.boolean('is_app_supported').notNullable().defaultTo(false);
  });

  await createIfMissing(knex, 'geographical_scope', (table) => {
    table.increments('id');
    table.string('name', 191).notNullable();
    table.text('description').nullable();
    timestamps(table);
  });

  await createIfMissing(knex, 'author', (table) => {
    table.increments('id');
    table.string('name', 100).notNullable();
    table.string('icon', 30).notNullable().defaultTo('fa fa-archive');
    table.string('is_organsiation', 10).notNullable().defaultTo('Yes');
    table.string('address', 200).notNullable().defaultTo('');
    table.string('telephone', 13).notNullable().defaultTo('');
    table.string('email', 100).notNullable().defaultTo('');
    table.string('orcid', 19).nullable();
    table.string('logo', 100).notNullable().defaultTo('author.png');
    timestamps(table);
  });
};

export const down = async (knex) => {
  for (const table of ['author', 'geographical_scope', 'country', 'regions', 'licenses', 'file_type', 'publication_sub_categories', 'publication_categories', 'data_categories', 'tags', 'sub_thematic_area', 'thematic_area']) {
    await knex.schema.dropTableIfExists(table);
  }
};
