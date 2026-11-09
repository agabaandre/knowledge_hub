#!/usr/bin/env node
/**
 * Seed an empty database with an admin user and a little demo content so a
 * fresh install can be tried and smoke-tested. Same code for MySQL and PostgreSQL.
 * Refuses to run when publications already exist (never touches real data).
 *
 *   node scripts/seed-demo.js --email=admin@example.org --password='long-password' [--site=id]
 */
import { loadEnv } from '../src/core/config/env.js';
import { createLogger } from '../src/core/logger.js';
import { ConnectionRegistry } from '../src/core/db/connection.js';
import { SiteRegistry } from '../src/core/site/registry.js';
import { hashPassword } from '../src/core/auth/password.js';
import { now } from '../src/core/db/helpers.js';

const args = Object.fromEntries(process.argv.slice(2).map((arg) => arg.replace(/^--/, '').split('=')).map(([k, ...v]) => [k, v.join('=') || true]));
if (!args.email || !args.password || String(args.password).length < 10) {
  console.error('Usage: seed-demo.js --email=<admin email> --password=<at least 10 characters>');
  process.exit(1);
}

const env = loadEnv();
const logger = createLogger(env);
const connections = new ConnectionRegistry();
const sites = new SiteRegistry({ env, connections, logger });
await sites.load();
const db = sites.get(args.site ?? 'default').db;

const insertId = async (trx, table, row) => {
  const [inserted] = await trx(table).insert(row).returning('id');
  return typeof inserted === 'object' ? inserted.id : inserted;
};

try {
  const existing = Number((await db('publication').count({ total: '*' }).first()).total);
  if (existing > 0) {
    logger.warn(`publication table has ${existing} rows; demo seed skipped`);
  } else {
    await db.transaction(async (trx) => {
      const stamp = { created_at: now(), updated_at: now() };
      const roleId = await insertId(trx, 'roles', { name: 'Admin', guard_name: 'web', ...stamp });
      const userId = await insertId(trx, 'users', { name: 'Hub Admin', first_name: 'Hub', last_name: 'Admin', email: args.email, password: await hashPassword(args.password), status: true, is_approved: true, ...stamp });
      await trx('model_has_roles').insert({ role_id: roleId, model_id: userId, model_type: 'App\\Models\\User' });
      await trx('setting').insert({ site_name: 'Knowledge Hub (demo)', seo_keywords: 'public health, Africa', site_description: 'Demo content for a fresh install.', timezone: 'Africa/Addis_Ababa', status: 'active', show_featured: true, show_top_searches: true });
      const authorId = await insertId(trx, 'author', { name: 'Africa CDC', ...stamp });
      const themeId = await insertId(trx, 'thematic_area', { description: 'Emergency preparedness', icon: 'fa-shield', ...stamp });
      const subThemeId = await insertId(trx, 'sub_thematic_area', { description: 'Outbreak response', thematic_area_id: themeId, ...stamp });
      const fileTypeId = await insertId(trx, 'file_type', { name: 'Report', icon: 'fa-file-pdf' });
      const tagId = await insertId(trx, 'tags', { tag_text: 'Cholera', slug: 'cholera', is_health_topic: true, is_health_emergency: true, overview: 'Cholera outbreaks and response.' });
      const titles = ['Cholera preparedness guidelines', 'Regional health workforce report', 'Outbreak response field manual'];
      for (const [index, title] of titles.entries()) {
        const publicationId = await insertId(trx, 'publication', {
          author_id: authorId, sub_thematic_area_id: subThemeId, title, slug: title.toLowerCase().replace(/[^a-z0-9]+/g, '-'),
          description: `${title}: demo resource for a public health knowledge hub.`, year_published: 2024 + (index % 2), file_type_id: fileTypeId,
          is_active: 'Active', is_approved: true, is_featured: index === 0, visits: 10 * (index + 1), ...stamp,
        });
        await trx('publication_tags').insert({ tag_id: tagId, publication_id: publicationId, ...stamp });
      }
      await trx('faqs').insert({ question: 'How do I publish a resource?', answer: 'Sign in, open Create → Resource publication, and submit for review.', ...stamp });
      await trx('forums').insert({ forum_title: 'Cholera response coordination', slug: 'cholera-response-coordination', forum_description: 'Share field updates.', created_by: userId, status: 'approved', is_approved: true, created_at: now() });
    });
    logger.info(`demo content and admin ${args.email} created`);
  }
} finally {
  await connections.destroyAll();
}
