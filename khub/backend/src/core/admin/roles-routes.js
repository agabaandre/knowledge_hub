import { Router } from 'express';
import { z } from 'zod';
import { PERMISSION_GROUPS, isKnownPermission } from '@khub/shared/permissions';
import { asyncHandler, HttpError } from '../http/errors.js';
import { validate } from '../http/validate.js';
import { requirePermission } from '../auth/middleware.js';
import { now } from '../db/helpers.js';

const roleSchema = z.object({
  name: z.string().trim().min(2).max(100),
  permissions: z.array(z.string().refine(isKnownPermission, 'Unknown permission')).max(100),
});

/**
 * Roles & permissions. Every admin function is a named permission; this page
 * grants and revokes them per role — including the Admin role itself (with a
 * guard so you cannot lock yourself out of role management).
 */
export const rolesRoutes = () => {
  const router = Router();

  router.get(
    '/roles',
    asyncHandler(async (req, res) => {
      const roles = await req.db('roles').orderBy('id').select('id', 'name');
      const grants = await req.db('role_has_permissions')
        .join('permissions', 'permissions.id', 'role_has_permissions.permission_id')
        .select('role_has_permissions.role_id', 'permissions.name');
      const counts = await req.db('model_has_roles').select('role_id').count({ total: '*' }).groupBy('role_id');
      const usersByRole = Object.fromEntries(counts.map((row) => [row.role_id, Number(row.total)]));
      res.json({
        catalogue: PERMISSION_GROUPS,
        roles: roles.map((role) => ({
          id: role.id,
          name: role.name,
          users: usersByRole[role.id] ?? 0,
          permissions: grants.filter((grant) => grant.role_id === role.id).map((grant) => grant.name).filter((name) => name.startsWith('admin.')),
        })),
      });
    }),
  );

  router.post(
    '/roles',
    requirePermission('admin.roles.edit'),
    validate(roleSchema),
    asyncHandler(async (req, res) => {
      if (await req.db('roles').whereILike('name', req.body.name).first()) throw new HttpError(422, 'A role with this name already exists.');
      const [inserted] = await req.db('roles').insert({ name: req.body.name, guard_name: 'web', created_at: now(), updated_at: now() }).returning('id');
      const roleId = typeof inserted === 'object' ? inserted.id : inserted;
      await applyGrants(req.db, roleId, req.body.permissions);
      res.status(201).json({ ok: true, id: roleId });
    }),
  );

  router.put(
    '/roles/:id',
    requirePermission('admin.roles.edit'),
    validate(roleSchema),
    asyncHandler(async (req, res) => {
      const roleId = Number(req.params.id);
      const role = await req.db('roles').where({ id: roleId }).first();
      if (!role) throw new HttpError(404, 'Role not found.');

      // Lock-out guard: the editor must keep roles.edit through some role they hold.
      if (!req.body.permissions.includes('admin.roles.edit')) {
        const myRoles = await req.db('model_has_roles').where({ model_id: req.user.id }).pluck('role_id');
        if (myRoles.includes(roleId) && myRoles.length === 1) {
          throw new HttpError(422, 'You cannot revoke "Edit roles" from the only role you hold — you would lock yourself out.');
        }
      }
      await req.db('roles').where({ id: roleId }).update({ name: req.body.name, updated_at: now() });
      await applyGrants(req.db, roleId, req.body.permissions);
      res.json({ ok: true });
    }),
  );

  router.delete(
    '/roles/:id',
    requirePermission('admin.roles.edit'),
    asyncHandler(async (req, res) => {
      const roleId = Number(req.params.id);
      const members = Number((await req.db('model_has_roles').where({ role_id: roleId }).count({ total: '*' }).first()).total);
      if (members > 0) throw new HttpError(422, `This role is assigned to ${members} user(s); reassign them first.`);
      await req.db('role_has_permissions').where({ role_id: roleId }).del();
      await req.db('roles').where({ id: roleId }).del();
      res.status(204).end();
    }),
  );

  return router;
};

/** Replace a role's admin.* grants with the given set (non-admin grants untouched). */
const applyGrants = async (db, roleId, names) => {
  const adminPermissionIds = await db('permissions').where('name', 'like', 'admin.%').pluck('id');
  await db('role_has_permissions').where({ role_id: roleId }).whereIn('permission_id', adminPermissionIds).del();
  if (names.length === 0) return;
  const rows = await db('permissions').whereIn('name', names).select('id');
  await db('role_has_permissions').insert(rows.map((row) => ({ permission_id: row.id, role_id: roleId })));
};
