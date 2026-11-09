import { PERMISSION_GROUPS } from '@khub/shared/permissions';
import { createIfMissing } from '../src/core/db/schema-guard.js';

/** Blocked-IP table for suspicious activity + sync of newly catalogued permissions. */
export const up = async (knex) => {
  await createIfMissing(knex, 'blocked_ips', (table) => {
    table.increments('id');
    table.string('ip', 64).notNullable().index();
    table.string('reason', 500).nullable();
    table.integer('created_by').nullable();
    table.timestamp('created_at').nullable();
  });

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
