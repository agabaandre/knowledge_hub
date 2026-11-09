import { PERMISSION_GROUPS } from '@khub/shared/permissions';
import { createIfMissing, addColumnIfMissing } from '../src/core/db/schema-guard.js';

/** Federated hubs registry + provenance columns for shared content + new admin permissions. */
export const up = async (knex) => {
  await createIfMissing(knex, 'federated_hubs', (table) => {
    table.increments('id');
    table.string('name', 150).notNullable();
    table.string('base_url', 255).notNullable();
    table.string('api_token', 255).notNullable();
    table.string('direction', 10).notNullable().defaultTo('pull'); // pull | push | both
    table.boolean('is_active').notNullable().defaultTo(true);
    table.string('last_status', 500).nullable();
    table.timestamp('last_synced_at').nullable();
    table.timestamp('created_at').nullable();
    table.timestamp('updated_at').nullable();
  });
  await addColumnIfMissing(knex, 'publication', 'federated_source', (t) => t.string('federated_source', 255).nullable());
  await addColumnIfMissing(knex, 'publication', 'federated_remote_id', (t) => t.string('federated_remote_id', 100).nullable());

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
