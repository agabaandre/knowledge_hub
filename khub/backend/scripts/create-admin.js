#!/usr/bin/env node
/**
 * Create an admin account, or promote/reset an existing user, on a site's database.
 *
 *   npm run admin:create -- --email=you@example.org --password='long password here'
 *   npm run admin:create -- --site=nigeria --email=... --password=... --name='Jane Doe'
 *
 * Idempotent: an existing user gets the new password, active status and the
 * Admin role; a missing Admin role is created.
 */
import { loadEnv } from '../src/core/config/env.js';
import { createLogger } from '../src/core/logger.js';
import { ConnectionRegistry } from '../src/core/db/connection.js';
import { SiteRegistry } from '../src/core/site/registry.js';
import { hashPassword } from '../src/core/auth/password.js';
import { now } from '../src/core/db/helpers.js';

const args = Object.fromEntries(process.argv.slice(2).map((arg) => arg.replace(/^--/, '').split('=')).map(([k, ...v]) => [k, v.join('=') || true]));
if (!args.email || !args.password || String(args.password).length < 10) {
  console.error('Usage: create-admin.js --email=<email> --password=<at least 10 characters> [--name="Full Name"] [--site=id]');
  process.exit(1);
}

const env = loadEnv();
const logger = createLogger(env);
const connections = new ConnectionRegistry();
const sites = new SiteRegistry({ env, connections, logger });
await sites.load();
const site = sites.get(args.site ?? 'default');
if (!site) {
  logger.error(`Unknown site "${args.site}"`);
  process.exit(1);
}
const db = site.db;

const insertId = async (table, row) => {
  const [inserted] = await db(table).insert(row).returning('id');
  return typeof inserted === 'object' ? inserted.id : inserted;
};

try {
  const email = String(args.email).toLowerCase();
  const name = args.name && args.name !== true ? String(args.name) : 'Hub Administrator';
  const [firstName, ...rest] = name.split(' ');
  const passwordHash = await hashPassword(String(args.password));
  const stamp = { created_at: now(), updated_at: now() };

  let role = await db('roles').whereILike('name', 'admin').orderBy('id').first();
  if (!role) {
    const id = await insertId('roles', { name: 'Admin', guard_name: 'web', ...stamp });
    role = { id };
    logger.info('created Admin role');
  }

  const existing = await db('users').whereRaw('lower(email) = ?', [email]).first();
  let userId;
  if (existing) {
    userId = existing.id;
    await db('users').where({ id: userId }).update({ password: passwordHash, status: true, is_approved: true, updated_at: now() });
    logger.info(`updated password and activated ${email} (user #${userId})`);
  } else {
    userId = await insertId('users', {
      name, first_name: firstName, last_name: rest.join(' ') || firstName, email,
      password: passwordHash, status: true, is_approved: true, ...stamp,
    });
    logger.info(`created ${email} (user #${userId})`);
  }

  const hasRole = await db('model_has_roles').where({ role_id: role.id, model_id: userId }).first();
  if (!hasRole) {
    await db('model_has_roles').insert({ role_id: role.id, model_id: userId, model_type: 'App\\Models\\User' });
    logger.info('assigned Admin role');
  } else {
    logger.info('already has the Admin role');
  }
  logger.info(`done — site "${site.id}" admin: ${email}`);
} finally {
  await connections.destroyAll();
}
