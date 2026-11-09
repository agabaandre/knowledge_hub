import { PERMISSION_GROUPS, DEFAULT_ROLE_GRANTS } from '@khub/shared/permissions';

/**
 * Seed the admin permission catalogue into the Spatie tables and grant the
 * defaults to existing roles that have no admin permissions yet. Idempotent:
 * new permissions are added on upgrade; grants are never re-added once an
 * admin has revoked them (the role already has rows / had them).
 */
export const up = async (knex) => {
  const catalogue = PERMISSION_GROUPS.flatMap((group) => group.permissions.map((permission) => ({ ...permission, group: group.group })));
  for (const permission of catalogue) {
    const existing = await knex('permissions').where({ name: permission.name, guard_name: 'web' }).first();
    if (!existing) {
      await knex('permissions').insert({ name: permission.name, guard_name: 'web', description: `${permission.group}: ${permission.label}`, created_at: new Date(), updated_at: new Date() });
    }
  }

  const permissionRows = await knex('permissions').whereIn('name', catalogue.map((permission) => permission.name)).select('id', 'name');
  const idByName = Object.fromEntries(permissionRows.map((row) => [row.name, row.id]));

  for (const [roleName, grants] of Object.entries(DEFAULT_ROLE_GRANTS)) {
    const role = await knex('roles').where({ name: roleName }).first('id');
    if (!role) continue;
    const already = await knex('role_has_permissions').where('role_id', role.id).whereIn('permission_id', Object.values(idByName)).first();
    if (already) continue; // role was managed before; respect revocations
    const rows = grants.map((name) => idByName[name]).filter(Boolean).map((permissionId) => ({ permission_id: permissionId, role_id: role.id }));
    if (rows.length) await knex('role_has_permissions').insert(rows);
  }
};

export const down = async () => {
  // Permission data is shared with Laravel; never drop on rollback.
};
