import { addColumnIfMissing } from '../src/core/db/schema-guard.js';

/** Threaded discussions: a comment may reply to another comment on the same publication. */
export const up = async (knex) => {
  await addColumnIfMissing(knex, 'publication_comments', 'parent_id', (table) => {
    table.integer('parent_id').nullable().index();
  });
};

export const down = async () => {};
