#!/usr/bin/env node
/**
 * Demo data for testing role segregation. Idempotent; refuses nothing, only
 * fills gaps. Creates (when missing):
 *   - 5 AU regions and a few countries bound to them
 *   - one user per access tier, password "Khub-Demo-2026!":
 *       rcc-east@khub.local       RCC Admin      scope: Eastern Africa region
 *       nigeria-admin@khub.local  MS Manager     scope: Nigeria
 *       moderator@khub.local      Reviewer       scope: Ethiopia
 *   - pending publications for Kenya, Nigeria, and one with no country,
 *     plus a pending forum by the Nigerian user
 *
 *   node scripts/seed-access-demo.js [--site=id]
 */
import { loadEnv } from '../src/core/config/env.js';
import { createLogger } from '../src/core/logger.js';
import { ConnectionRegistry } from '../src/core/db/connection.js';
import { SiteRegistry } from '../src/core/site/registry.js';
import { hashPassword } from '../src/core/auth/password.js';
import { now } from '../src/core/db/helpers.js';

const PASSWORD = 'Khub-Demo-2026!';
const REGIONS = ['Eastern Africa', 'Western Africa', 'Northern Africa', 'Southern Africa', 'Central Africa'];
const COUNTRIES = [
  { name: 'Kenya', iso: 'ke', region: 'Eastern Africa' },
  { name: 'Ethiopia', iso: 'et', region: 'Eastern Africa' },
  { name: 'Nigeria', iso: 'ng', region: 'Western Africa' },
  { name: 'Ghana', iso: 'gh', region: 'Western Africa' },
  { name: 'Egypt', iso: 'eg', region: 'Northern Africa' },
];
const USERS = [
  { email: 'rcc-east@khub.local', name: 'Rcc East', role: 'RCC Admin', country: 'Kenya' },
  { email: 'nigeria-admin@khub.local', name: 'Nigeria Admin', role: 'MS Manager', country: 'Nigeria' },
  { email: 'moderator@khub.local', name: 'Content Moderator', role: 'Reviewer', country: 'Ethiopia' },
];

const args = Object.fromEntries(process.argv.slice(2).map((arg) => arg.replace(/^--/, '').split('=')).map(([k, v]) => [k, v ?? true]));
const env = loadEnv();
const logger = createLogger(env);
const connections = new ConnectionRegistry();
const sites = new SiteRegistry({ env, connections, logger });
await sites.load();
const db = sites.get(args.site ?? 'default').db;

const insertId = async (table, row) => {
  const [inserted] = await db(table).insert(row).returning('id');
  return typeof inserted === 'object' ? inserted.id : inserted;
};

const ensure = async (table, where, row) => {
  const existing = await db(table).where(where).first();
  if (existing) return existing.id;
  const id = await insertId(table, row);
  logger.info(`created ${table}: ${JSON.stringify(where)}`);
  return id;
};

try {
  const stamp = { created_at: now(), updated_at: now() };

  const regionIds = {};
  for (const name of REGIONS) regionIds[name] = await ensure('regions', { region_name: name }, { region_name: name });

  const countryIds = {};
  for (const country of COUNTRIES) {
    countryIds[country.name] = await ensure('country', { name: country.name }, {
      name: country.name, national: '', slug: country.name.toLowerCase(), iso_code: country.iso,
      region_id: regionIds[country.region], longitude: 0, latitude: 0, population: 0, color: '#cccccc',
    });
  }

  const roleIds = {};
  for (const role of ['Admin', 'RCC Admin', 'MS Manager', 'Reviewer']) {
    roleIds[role] = await ensure('roles', { name: role }, { name: role, guard_name: 'web', ...stamp });
  }

  const passwordHash = await hashPassword(PASSWORD);
  const userIds = {};
  for (const user of USERS) {
    const [firstName, ...rest] = user.name.split(' ');
    userIds[user.email] = await ensure('users', { email: user.email }, {
      name: user.name, first_name: firstName, last_name: rest.join(' ') || firstName, email: user.email,
      password: passwordHash, status: true, is_approved: true, country_id: countryIds[user.country], ...stamp,
    });
    await db('users').where({ id: userIds[user.email] }).update({ country_id: countryIds[user.country], password: passwordHash, status: true, is_approved: true });
    const hasRole = await db('model_has_roles').where({ role_id: roleIds[user.role], model_id: userIds[user.email] }).first();
    if (!hasRole) await db('model_has_roles').insert({ role_id: roleIds[user.role], model_id: userIds[user.email], model_type: 'App\\Models\\User' });
  }

  // Minimal taxonomy so the publish wizard's required selects can be satisfied.
  const themes = { 'Emergency Preparedness & Response': ['Outbreak Response', 'Surveillance'], 'Health Systems': ['Health Workforce', 'Laboratory Systems'] };
  for (const [themeName, subs] of Object.entries(themes)) {
    const themeId = await ensure('thematic_area', { description: themeName }, { description: themeName, icon: 'fa-shield', ...stamp });
    for (const subName of subs) await ensure('sub_thematic_area', { description: subName }, { description: subName, thematic_area_id: themeId, ...stamp });
  }
  for (const categoryName of ['Publications', 'Data & Statistics']) {
    await ensure('data_categories', { category_name: categoryName }, { category_name: categoryName, slug: categoryName.toLowerCase().replace(/[^a-z0-9]+/g, '-'), show_on_menu: true, ...stamp });
  }
  await ensure('publication_categories', { category_name: 'Technical Reports' }, { category_name: 'Technical Reports', ...stamp });
  await ensure('tags', { slug: 'cholera' }, { tag_text: 'Cholera', slug: 'cholera', is_health_topic: true });
  await ensure('tags', { slug: 'ebola' }, { tag_text: 'Ebola', slug: 'ebola', is_health_topic: true });

  const authorId = (await db('author').orderBy('id').first())?.id ?? (await insertId('author', { name: 'Africa CDC', ...stamp }));
  const pendingPublication = async (title, countryName, submitterEmail) => {
    const slug = title.toLowerCase().replace(/[^a-z0-9]+/g, '-');
    const existing = await db('publication').where({ slug }).first();
    if (existing) return;
    const id = await insertId('publication', {
      author_id: authorId, sub_thematic_area_id: 0, title, slug, description: `${title} — pending review (access demo).`,
      publication_catgory_id: 0, geographical_coverage_id: 0, is_active: 'Active', is_approved: false, is_rejected: false,
      visits: 0, is_version: false, is_admin_only_access: false, year_published: 2026,
      user_id: submitterEmail ? userIds[submitterEmail] : null, ...stamp,
    });
    if (countryName) await db('publication_countries').insert({ publication_id: id, country_id: countryIds[countryName], ...stamp });
    logger.info(`created pending publication: ${title}`);
  };

  await pendingPublication('Kenya measles situation report', 'Kenya', 'rcc-east@khub.local');
  await pendingPublication('Nigeria Lassa fever brief', 'Nigeria', 'nigeria-admin@khub.local');
  await pendingPublication('Continental health strategy draft', null, null);

  const forumSlug = 'nigeria-surveillance-questions';
  if (!(await db('forums').where({ slug: forumSlug }).first())) {
    await db('forums').insert({
      forum_title: 'Nigeria surveillance data questions', slug: forumSlug, forum_description: 'Pending forum from a Nigerian member (access demo).',
      created_by: userIds['nigeria-admin@khub.local'], status: 'pending', is_approved: false, is_rejected: false, created_at: now(),
    });
    logger.info('created pending forum: Nigeria surveillance data questions');
  }

  logger.info(`done — demo moderators all use password "${PASSWORD}"`);
} finally {
  await connections.destroyAll();
}
