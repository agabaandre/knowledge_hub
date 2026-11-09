import { addColumnIfMissing } from '../src/core/db/schema-guard.js';

/** Integrity metadata for a publication's primary file (same standard as attachments). */
export const up = async (knex) => {
  const columns = [
    ['file_checksum_sha256', (t) => t.string('file_checksum_sha256', 64).nullable()],
    ['file_size_bytes', (t) => t.bigInteger('file_size_bytes').nullable()],
    ['file_mime_type', (t) => t.string('file_mime_type', 150).nullable()],
  ];
  for (const [column, build] of columns) {
    await addColumnIfMissing(knex, 'publication', column, (table) => build(table));
  }
};

export const down = async () => {};
