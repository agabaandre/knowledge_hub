import { createIfMissing, indexName } from '../src/core/db/schema-guard.js';

/** Per-site translation overrides, edited in Language Management (AI-assisted). */
export const up = async (knex) => {
  await createIfMissing(knex, 'language_lines', (table) => {
    table.increments('id');
    table.string('locale', 10).notNullable();
    table.string('key', 191).notNullable();
    table.text('text').notNullable();
    table.timestamp('updated_at').nullable();
    table.unique(['locale', 'key'], { indexName: indexName('language_lines', ['locale', 'key'], 'uq') });
  });
};

export const down = async () => {};
