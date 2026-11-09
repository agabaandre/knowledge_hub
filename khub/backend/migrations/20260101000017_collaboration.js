import { PERMISSION_GROUPS } from '@khub/shared/permissions';
import { addColumnIfMissing } from '../src/core/db/schema-guard.js';

/** Modern collaboration: pinned/locked threads, trash for forums & communities, admin permissions. */
export const up = async (knex) => {
  await addColumnIfMissing(knex, 'forums', 'is_pinned', (t) => t.boolean('is_pinned').notNullable().defaultTo(false));
  await addColumnIfMissing(knex, 'forums', 'is_locked', (t) => t.boolean('is_locked').notNullable().defaultTo(false));
  await addColumnIfMissing(knex, 'forums', 'deleted_at', (t) => t.timestamp('deleted_at').nullable());
  await addColumnIfMissing(knex, 'community_of_practices', 'deleted_at', (t) => t.timestamp('deleted_at').nullable());

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
