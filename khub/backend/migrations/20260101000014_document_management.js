import { PERMISSION_GROUPS } from '@khub/shared/permissions';
import { addColumnIfMissing } from '../src/core/db/schema-guard.js';

/**
 * Document management metadata on publication attachments, industry-standard:
 * SHA-256 integrity checksum, size/type, uploader audit, document version,
 * and a retention date after which the file is disposed of automatically.
 * Also syncs newly catalogued permissions (documents.*) to the Admin role.
 */
export const up = async (knex) => {
  const columns = [
    ['checksum_sha256', (t) => t.string('checksum_sha256', 64).nullable()],
    ['size_bytes', (t) => t.bigInteger('size_bytes').nullable()],
    ['mime_type', (t) => t.string('mime_type', 150).nullable()],
    ['uploaded_by', (t) => t.integer('uploaded_by').nullable()],
    ['doc_version', (t) => t.integer('doc_version').notNullable().defaultTo(1)],
    ['retention_until', (t) => t.date('retention_until').nullable()],
    ['created_at', (t) => t.timestamp('created_at').nullable()],
    ['updated_at', (t) => t.timestamp('updated_at').nullable()],
  ];
  for (const [column, build] of columns) {
    await addColumnIfMissing(knex, 'publication_attachments', column, (table) => build(table));
  }

  const catalogue = PERMISSION_GROUPS.flatMap((group) => group.permissions.map((permission) => ({ ...permission, group: group.group })));
  const added = [];
  for (const permission of catalogue) {
    const existing = await knex('permissions').where({ name: permission.name, guard_name: 'web' }).first('id');
    if (!existing) {
      const [inserted] = await knex('permissions').insert({ name: permission.name, guard_name: 'web', description: `${permission.group}: ${permission.label}`, created_at: new Date(), updated_at: new Date() }).returning('id');
      added.push(typeof inserted === 'object' ? inserted.id : inserted);
    }
  }
  if (added.length === 0) return;
  const admin = await knex('roles').where({ name: 'Admin' }).first('id');
  if (admin) await knex('role_has_permissions').insert(added.map((permissionId) => ({ permission_id: permissionId, role_id: admin.id })));
};

export const down = async () => {};
