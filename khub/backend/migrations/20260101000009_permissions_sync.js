import { PERMISSION_GROUPS } from '@khub/shared/permissions';

/**
 * Sync newly catalogued permissions into the database and grant the NEW ones
 * to the Admin role (existing grants/revocations stay untouched). Re-create
 * this migration pattern whenever the catalogue grows.
 */
export const up = async (knex) => {
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
