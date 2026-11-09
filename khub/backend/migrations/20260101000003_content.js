import { createIfMissing, indexName } from "../src/core/db/schema-guard.js";
/**
 * Content tables: publications and their relations, forums, communities, events, faqs, quotes, logs.
 */
const timestamps = (table) => {
  table.timestamp('created_at').nullable();
  table.timestamp('updated_at').nullable();
};

const pivot = (knex, name, left, right) =>
  createIfMissing(knex, name, (table) => {
    table.increments('id');
    table.integer(left).unsigned().notNullable().index(indexName(name, left));
    table.integer(right).unsigned().notNullable().index(indexName(name, right));
    timestamps(table);
  });

export const up = async (knex) => {
  await createIfMissing(knex, 'publication', (table) => {
    table.increments('id');
    table.integer('author_id').unsigned().notNullable().index();
    table.integer('sub_thematic_area_id').unsigned().notNullable().index();
    table.text('publication').nullable();
    table.integer('year_published').nullable().index();
    table.string('title', 200).nullable();
    table.string('slug', 191).nullable().unique();
    table.text('description').nullable();
    table.integer('publication_catgory_id').unsigned().notNullable().defaultTo(0);
    table.integer('publication_sub_category_id').unsigned().nullable();
    table.integer('geographical_coverage_id').unsigned().notNullable().defaultTo(0);
    table.text('cover').nullable();
    table.string('is_active', 10).notNullable().defaultTo('Active');
    table.integer('visits').notNullable().defaultTo(0);
    table.date('date_created').nullable();
    table.boolean('is_featured').notNullable().defaultTo(false);
    table.boolean('is_rss').nullable();
    table.integer('rss_id').unsigned().nullable();
    table.boolean('is_video').notNullable().defaultTo(false);
    table.boolean('is_version').notNullable().defaultTo(false);
    table.string('version_no', 255).nullable();
    table.integer('parent_id').unsigned().nullable();
    table.integer('file_type_id').unsigned().nullable();
    table.string('citation_link', 500).nullable();
    table.string('citation_authors', 100).nullable();
    table.string('doi', 255).nullable();
    table.string('issn', 50).nullable();
    table.string('isbn', 50).nullable();
    table.string('publisher', 500).nullable();
    table.integer('license_id').unsigned().nullable();
    table.text('copyright_info').nullable();
    table.string('funder', 500).nullable();
    table.string('journal_name', 500).nullable();
    table.string('journal_volume', 50).nullable();
    table.string('journal_issue', 50).nullable();
    table.string('journal_pages', 50).nullable();
    table.integer('user_id').unsigned().nullable();
    table.boolean('is_approved').notNullable().defaultTo(false);
    table.integer('approved_by').unsigned().nullable();
    table.integer('rejected_by').unsigned().nullable();
    table.boolean('is_rejected').notNullable().defaultTo(false);
    table.text('rejected_reason').nullable();
    table.timestamp('rejected_at').nullable();
    table.string('associated_authors', 200).nullable();
    table.string('author_affiliation', 500).nullable();
    table.boolean('cover_is_exteranl').notNullable().defaultTo(false);
    table.integer('geographical_scope_id').unsigned().nullable();
    table.integer('data_category_id').unsigned().nullable().index();
    table.boolean('is_embedded').notNullable().defaultTo(false);
    table.boolean('is_default_in_category').notNullable().defaultTo(false);
    table.boolean('is_admin_only_access').notNullable().defaultTo(false);
    table.boolean('show_disclaimer').notNullable().defaultTo(true);
    table.boolean('also_public_on_hub').notNullable().defaultTo(false);
    timestamps(table);
  });

  await pivot(knex, 'publication_tags', 'tag_id', 'publication_id');
  await pivot(knex, 'publication_countries', 'publication_id', 'country_id');
  await pivot(knex, 'publication_community_of_practices', 'publication_id', 'community_of_practice_id');

  await createIfMissing(knex, 'publication_attachments', (table) => {
    table.increments('id');
    table.integer('publication_id').unsigned().notNullable().index();
    table.string('file', 100).notNullable();
    table.string('description', 200).nullable();
    table.string('original_filename', 255).nullable();
  });

  await createIfMissing(knex, 'publication_summaries', (table) => {
    table.increments('id');
    table.integer('resource_id').unsigned().notNullable().index();
    table.string('file_path', 255).nullable();
    table.string('title', 255).notNullable();
    table.text('description').nullable();
    table.boolean('is_link').notNullable().defaultTo(false);
    table.boolean('is_approved').notNullable().defaultTo(false);
    table.boolean('is_rejected').notNullable().defaultTo(false);
    table.integer('author_id').unsigned().notNullable().defaultTo(0);
    table.integer('user_id').unsigned().nullable();
    timestamps(table);
  });

  await createIfMissing(knex, 'publication_comments', (table) => {
    table.increments('id');
    table.integer('user_id').unsigned().notNullable().defaultTo(0);
    table.integer('publication_id').unsigned().notNullable().index();
    table.text('comment').notNullable();
    table.string('status', 15).notNullable().defaultTo('pending');
    table.timestamp('created_at').nullable();
  });

  await createIfMissing(knex, 'publication_views', (table) => {
    table.increments('id');
    table.integer('publication_id').unsigned().notNullable().index();
    table.integer('year').notNullable();
    table.integer('month').unsigned().notNullable();
    table.integer('views').unsigned().notNullable().defaultTo(0);
    timestamps(table);
  });

  await createIfMissing(knex, 'favourites', (table) => {
    table.increments('id');
    table.integer('publication_id').unsigned().notNullable().index();
    table.integer('user_id').unsigned().notNullable().index();
  });

  await createIfMissing(knex, 'forums', (table) => {
    table.increments('id');
    table.text('forum_title').notNullable();
    table.string('slug', 191).nullable().unique();
    table.string('forum_image', 200).notNullable().defaultTo('forum.jpg');
    table.text('forum_description').nullable();
    table.integer('created_by').unsigned().notNullable().index();
    table.string('status', 11).notNullable().defaultTo('pending');
    table.integer('views').unsigned().notNullable().defaultTo(0);
    table.boolean('is_approved').notNullable().defaultTo(false);
    table.boolean('is_rejected').notNullable().defaultTo(false);
    table.integer('approved_by').unsigned().nullable();
    table.integer('rejected_by').unsigned().nullable();
    table.text('rejected_reason').nullable();
    table.boolean('is_resubmission_pending').notNullable().defaultTo(false);
    table.boolean('also_public_on_hub').notNullable().defaultTo(false);
    table.timestamp('created_at').nullable();
  });

  await createIfMissing(knex, 'forum_comments', (table) => {
    table.increments('id');
    table.integer('forum_id').unsigned().notNullable().index();
    table.text('comment').notNullable();
    table.integer('created_by').unsigned().notNullable();
    table.integer('parent_id').unsigned().nullable();
    table.string('status', 15).notNullable().defaultTo('pending');
    timestamps(table);
  });

  await pivot(knex, 'forum_likes', 'forum_id', 'user_id');
  await pivot(knex, 'forum_comment_likes', 'forum_comment_id', 'user_id');

  await createIfMissing(knex, 'forum_tags', (table) => {
    table.increments('id');
    table.integer('forum_id').unsigned().notNullable().index();
    table.string('tag', 50).notNullable();
  });

  await createIfMissing(knex, 'community_of_practices', (table) => {
    table.increments('id');
    table.string('community_name', 255).notNullable();
    table.string('slug', 191).nullable().unique();
    table.string('description', 1000).nullable();
    table.integer('region_id').unsigned().nullable();
    table.integer('country_id').unsigned().nullable();
    table.string('organisation', 191).nullable();
    table.string('department', 191).nullable();
    table.boolean('is_public').notNullable().defaultTo(true);
    table.integer('created_by').unsigned().notNullable();
    table.boolean('is_active').notNullable().defaultTo(false);
    timestamps(table);
  });

  await createIfMissing(knex, 'community_of_practice_members', (table) => {
    table.increments('id');
    table.integer('community_of_practice_id').unsigned().notNullable().index();
    table.integer('user_id').unsigned().notNullable().index();
    table.boolean('is_approved').notNullable().defaultTo(false);
    table.boolean('is_admin').notNullable().defaultTo(false);
    table.boolean('is_active').notNullable().defaultTo(true);
    timestamps(table);
  });

  await pivot(knex, 'community_of_practice_tags', 'community_of_practice_id', 'tag_id');

  await createIfMissing(knex, 'events', (table) => {
    table.increments('id');
    table.string('title', 191).notNullable();
    table.text('description').notNullable();
    table.string('venue', 191).nullable();
    table.timestamp('startdate').nullable();
    table.timestamp('enddate').nullable();
    table.string('organized_by', 191).nullable();
    table.decimal('fee', 8, 2).nullable();
    table.string('status', 191).notNullable().defaultTo('pending');
    table.string('event_link', 191).nullable();
    table.string('registration_link', 191).nullable();
    table.boolean('is_online').notNullable().defaultTo(false);
    table.string('contact_person', 191).nullable();
    table.string('banner_image', 191).nullable();
    table.string('created_by', 191).nullable();
    table.integer('country_id').unsigned().nullable();
    table.integer('community_of_practice_id').unsigned().nullable();
    table.string('event_category', 100).nullable();
    timestamps(table);
  });

  await createIfMissing(knex, 'faqs', (table) => {
    table.increments('id');
    table.string('question', 200).notNullable();
    table.text('answer').notNullable();
    timestamps(table);
  });

  await createIfMissing(knex, 'quotes', (table) => {
    table.increments('id');
    table.text('quote').notNullable();
    table.string('image', 500).nullable();
    table.string('link_url', 1000).nullable();
    table.integer('is_active').notNullable().defaultTo(1);
  });

  await createIfMissing(knex, 'search_logs', (table) => {
    table.increments('id');
    table.integer('user_id').unsigned().nullable();
    table.string('term', 255).notNullable();
    table.integer('results_count').unsigned().nullable();
    table.string('request_path', 191).nullable();
    table.string('ip_address', 45).nullable();
    table.text('user_agent').nullable();
    timestamps(table);
  });

  await createIfMissing(knex, 'subscribes', (table) => {
    table.increments('id');
    table.string('email', 255).notNullable().unique();
    table.string('name', 255).nullable();
    table.string('status', 20).notNullable().defaultTo('subscribed');
    timestamps(table);
  });
};

export const down = async (knex) => {
  const tables = [
    'subscribes', 'search_logs', 'quotes', 'faqs', 'events', 'community_of_practice_tags', 'community_of_practice_members', 'community_of_practices',
    'forum_tags', 'forum_comment_likes', 'forum_likes', 'forum_comments', 'forums', 'favourites', 'publication_views', 'publication_comments',
    'publication_summaries', 'publication_attachments', 'publication_community_of_practices', 'publication_countries', 'publication_tags', 'publication',
  ];
  for (const table of tables) await knex.schema.dropTableIfExists(table);
};
