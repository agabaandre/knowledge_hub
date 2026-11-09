import { test } from 'node:test';
import assert from 'node:assert/strict';
import { siteIdFromUrl, sanitizeSiteId } from '../src/core/site/identifier.js';
import { hashPassword, verifyPassword } from '../src/core/auth/password.js';
import { signSession, verifySession } from '../src/core/auth/jwt.js';
import { cleanRelativePath } from '../src/core/storage/paths.js';
import { normalizeTerm, significantWords } from '../src/modules/publications/search-query.js';
import { loadEnv } from '../src/core/config/env.js';
import { coerceBooleans } from '../scripts/laravel-table-map.js';

test('site id derivation matches Laravel HubSiteIdentifier', () => {
  assert.equal(siteIdFromUrl('https://khub.com/andrew'), 'khub-com-andrew');
  assert.equal(siteIdFromUrl('https://www.hub.example.com'), 'hub-example-com');
  assert.equal(siteIdFromUrl('http://localhost:8080'), 'localhost-8080');
  assert.equal(siteIdFromUrl('http://localhost:8081', 'nigeria'), 'localhost-8081-nigeria');
  assert.equal(sanitizeSiteId('  Weird__Name!! '), 'weird-name');
});

test('laravel $2y$ bcrypt hashes verify', async () => {
  const hash = await hashPassword('secret-password');
  const laravelStyle = `$2y$${hash.slice(4)}`;
  assert.equal(await verifyPassword('secret-password', laravelStyle), true);
  assert.equal(await verifyPassword('wrong', laravelStyle), false);
  assert.equal(await verifyPassword('x', null), false);
});

test('sessions are bound to a site', () => {
  const token = signSession('secret', { id: 5, role: 'admin' }, 'default');
  assert.equal(verifySession('secret', token).site, 'default');
  assert.equal(verifySession('other', token), null);
});

test('storage paths reject traversal', () => {
  assert.equal(cleanRelativePath('../../etc/passwd'), 'etc/passwd');
  assert.equal(cleanRelativePath('uploads\\publications\\a.pdf'), 'uploads/publications/a.pdf');
});

test('search term tokenizer drops stop words but never returns empty', () => {
  assert.equal(normalizeTerm('  <b>Ebola</b>   in  Africa '), 'Ebola in Africa');
  assert.deepEqual(significantWords('the of in'), ['the', 'of', 'in']);
  assert.deepEqual(significantWords('Ebola in Africa, 2024'), ['Ebola', 'Africa', '2024']);
});

test('env loader validates the DB client and production secret', () => {
  assert.throws(() => loadEnv({ DB_CLIENT: 'sqlite' }), /Unsupported DB client/);
  assert.throws(() => loadEnv({ NODE_ENV: 'production', APP_SECRET: 'change-me' }), /APP_SECRET/);
  assert.equal(loadEnv({ DB_CLIENT: 'pgsql' }).database.client, 'pg');
});

test('migration coerces MySQL tinyints into booleans', () => {
  assert.deepEqual(coerceBooleans({ a: 1, b: 0, c: null, d: 'x' }, ['a', 'b', 'c']), { a: true, b: false, c: null, d: 'x' });
});

test('manifest settings never expose secrets', async () => {
  const { publicSettings } = await import('../src/core/site/public-settings.js');
  const exposed = publicSettings({ site_name: 'Hub', ai_custom_api_key: 'sk-secret', legacy_settings: '{"x":1}', analytics_script: '<script>', default_password: 'x', primary_color: '#000' });
  assert.deepEqual(exposed, { site_name: 'Hub', primary_color: '#000' });
});

test('access levels map roles and scopes correctly', async () => {
  const { resolveAccess, levelForRoles } = await import('../src/core/auth/access.js');
  assert.equal(levelForRoles(['Admin']), 'overall');
  assert.equal(levelForRoles(['Overall Administrator']), 'overall');
  assert.equal(levelForRoles(['RCC Admin']), 'rcc');
  assert.equal(levelForRoles(['MS Manager']), 'country');
  assert.equal(levelForRoles(['Country Moderator']), 'country');
  assert.equal(levelForRoles(['Reviewer']), 'moderator');
  assert.equal(levelForRoles([]), 'member');

  const rcc = resolveAccess(['RCC Admin'], { regionId: 2, regionName: 'Eastern Africa', countryId: 7, countryName: 'Kenya' });
  assert.equal(rcc.isAdmin, false);
  assert.equal(rcc.canModerate, true);
  assert.equal(rcc.scopeLabel, 'Eastern Africa region');

  const overall = resolveAccess(['Admin'], {});
  assert.equal(overall.isAdmin, true);
  assert.equal(overall.scopeLabel, 'All countries');

  const member = resolveAccess([], {});
  assert.equal(member.canModerate, false);
});
