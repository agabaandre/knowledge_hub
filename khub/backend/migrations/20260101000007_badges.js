import { createIfMissing } from '../src/core/db/schema-guard.js';

/** Contributor badge tiers + awards, matching the Laravel tables and seed. */
const TIERS = [
  { name: 'Silver Contributor', slug: 'silver', contribution_threshold: 5, badge_color: '#C0C0C0', sort_order: 1 },
  { name: 'Gold Contributor', slug: 'gold', contribution_threshold: 10, badge_color: '#FFD700', sort_order: 2 },
  { name: 'Platinum Contributor', slug: 'platinum', contribution_threshold: 20, badge_color: '#E5E4E2', sort_order: 3 },
  { name: 'Diamond Contributor', slug: 'diamond', contribution_threshold: 40, badge_color: '#B9F2FF', sort_order: 4 },
];

export const up = async (knex) => {
  await createIfMissing(knex, 'badge_types', (table) => {
    table.increments('id');
    table.string('name', 191).notNullable();
    table.string('slug', 191).notNullable().unique();
    table.text('description').nullable();
    table.integer('contribution_threshold').notNullable();
    table.string('badge_color', 50).notNullable().defaultTo('#C0C0C0');
    table.string('image_path', 191).nullable();
    table.integer('sort_order').notNullable().defaultTo(0);
    table.boolean('is_active').notNullable().defaultTo(true);
    table.timestamp('created_at').nullable();
    table.timestamp('updated_at').nullable();
  });
  await createIfMissing(knex, 'user_lifetime_badges', (table) => {
    table.increments('id');
    table.integer('user_id').unsigned().notNullable().unique();
    table.integer('badge_type_id').unsigned().nullable();
    table.integer('lifetime_contributions').unsigned().notNullable().defaultTo(0);
    table.timestamp('last_upgraded_at').nullable();
    table.timestamp('created_at').nullable();
    table.timestamp('updated_at').nullable();
  });
  const existing = Number((await knex('badge_types').count({ total: '*' }).first()).total);
  if (existing === 0) await knex('badge_types').insert(TIERS.map((tier) => ({ ...tier, is_active: true })));
};

export const down = async () => {
  // Shared with Laravel data; never drop on rollback.
};
