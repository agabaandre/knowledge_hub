import { toBool, now } from '../db/helpers.js';
import { resolveAccess } from './access.js';

const SESSION_COLUMNS = ['id', 'name', 'first_name', 'last_name', 'email', 'photo', 'is_photo_external', 'status', 'is_approved', 'theme_preference', 'author_id', 'country_id'];

/**
 * Users + Spatie-style roles (`model_has_roles` / `roles`) as stored by the Laravel app.
 */
export class UsersRepository {
  constructor(db) {
    this.db = db;
  }

  findByEmail(email) {
    return this.db('users').whereRaw('lower(email) = ?', [String(email).toLowerCase()]).first();
  }

  async findForSession(id) {
    const user = await this.db('users')
      .leftJoin('country', 'country.id', 'users.country_id')
      .leftJoin('regions', 'regions.id', 'country.region_id')
      .select(SESSION_COLUMNS.map((column) => `users.${column}`))
      .select('users.organization_name', 'country.name as country_name', 'country.region_id', 'regions.region_name')
      .where('users.id', id)
      .first();
    if (!user) return null;
    const [roles, permissions] = await Promise.all([this.rolesOf(id), this.permissionsOf(id)]);
    const access = resolveAccess(roles, {
      countryId: user.country_id,
      countryName: user.country_name,
      regionId: user.region_id,
      regionName: user.region_name,
    });
    return {
      ...user,
      isApproved: toBool(user.is_approved),
      roles,
      permissions,
      access,
      isAdmin: access.isAdmin,
    };
  }

  async updatePhoto(id, filename) {
    await this.db('users').where({ id }).update({ photo: filename, is_photo_external: false, updated_at: now() });
  }

  async updateProfile(id, { firstName, lastName, organization }) {
    await this.db('users').where({ id }).update({
      name: `${firstName} ${lastName}`.trim(),
      first_name: firstName,
      last_name: lastName,
      organization_name: organization ?? null,
      updated_at: now(),
    });
  }

  async permissionsOf(userId) {
    const rows = await this.db('model_has_roles as mhr')
      .join('role_has_permissions as rhp', 'rhp.role_id', 'mhr.role_id')
      .join('permissions', 'permissions.id', 'rhp.permission_id')
      .where('mhr.model_id', userId)
      .distinct('permissions.name');
    return rows.map((row) => row.name).filter((name) => name.startsWith('admin.'));
  }

  async rolesOf(userId) {
    const rows = await this.db('model_has_roles as mhr')
      .join('roles', 'roles.id', 'mhr.role_id')
      .where('mhr.model_id', userId)
      .select('roles.name');
    return rows.map((row) => row.name);
  }

  async create({ firstName, lastName, email, passwordHash, organization }) {
    const payload = {
      name: `${firstName} ${lastName}`.trim(),
      first_name: firstName,
      last_name: lastName,
      email,
      password: passwordHash,
      organization_name: organization ?? null,
      status: 1,
      is_approved: 1,
      created_at: now(),
      updated_at: now(),
    };
    const [inserted] = await this.db('users').insert(payload).returning('id');
    return typeof inserted === 'object' ? inserted.id : inserted;
  }

  touchLogin(id) {
    return this.db('users').where({ id }).update({ last_login_at: now() });
  }
}
