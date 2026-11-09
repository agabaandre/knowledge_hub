import { addColumnIfMissing } from '../src/core/db/schema-guard.js';

/**
 * Trash bin: deletes become recoverable (deleted_at) for publications and
 * documents; the retention sweep destroys trashed items permanently after
 * `setting.trash_retention_days` (default 30).
 */
export const up = async (knex) => {
  await addColumnIfMissing(knex, 'publication', 'deleted_at', (table) => table.timestamp('deleted_at').nullable().index());
  await addColumnIfMissing(knex, 'publication_attachments', 'deleted_at', (table) => table.timestamp('deleted_at').nullable());
  await addColumnIfMissing(knex, 'setting', 'trash_retention_days', (table) => table.string('trash_retention_days', 10).nullable());
};

export const down = async () => {};
