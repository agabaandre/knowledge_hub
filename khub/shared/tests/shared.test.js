import { test } from 'node:test';
import assert from 'node:assert/strict';
import { resolveModuleFlags, isCoreModule } from '../src/modules.js';
import { normalizeSiteConfig } from '../src/site-config.js';
import { slugify, parseRecordKey } from '../src/slug.js';
import { descriptionForPath, truncate } from '../src/seo.js';
import { publicationPath, recordsSearchPath, withSitePrefix } from '../src/urls.js';

test('core modules cannot be disabled', () => {
  const flags = resolveModuleFlags({ publications: false, forums: false });
  assert.equal(flags.publications, true);
  assert.equal(flags.forums, false);
  assert.equal(isCoreModule('publications'), true);
});

test('later layers override earlier ones and unknown names are ignored', () => {
  const flags = resolveModuleFlags({ forums: false }, { forums: true, bogus: true });
  assert.equal(flags.forums, true);
  assert.equal('bogus' in flags, false);
});

test('site config fills defaults and validates prefix', () => {
  const config = normalizeSiteConfig({ name: 'Nigeria', pathPrefix: '/nigeria/' }, 'nigeria');
  assert.equal(config.id, 'nigeria');
  assert.equal(config.pathPrefix, 'nigeria');
  assert.equal(config.features.seoFriendlyUrls, true);
  assert.throws(() => normalizeSiteConfig({ pathPrefix: 'Bad Prefix' }, 'x'), /pathPrefix/);
  assert.throws(() => normalizeSiteConfig([], 'x'), /JSON object/);
});

test('slugify matches Laravel style slugs', () => {
  assert.equal(slugify('Ebola Virus Disease: Outbreak Réponse 2024'), 'ebola-virus-disease-outbreak-reponse-2024');
  assert.deepEqual(parseRecordKey('42'), { id: 42, slug: null });
  assert.deepEqual(parseRecordKey('ebola-2024'), { id: null, slug: 'ebola-2024' });
});

test('seo helpers produce stable descriptions', () => {
  assert.match(descriptionForPath('/forums/abc', 'Hub'), /discussion forums on Hub/);
  assert.match(descriptionForPath('/', 'Hub'), /Discover publications/);
  assert.equal(truncate('<p>Hello   world</p>', 8), 'Hello w…');
});

test('url builders keep Laravel paths', () => {
  assert.equal(publicationPath({ id: 7, slug: 'my-paper' }), '/records/resource/my-paper');
  assert.equal(publicationPath({ id: 7 }), '/records/resource/7');
  assert.equal(recordsSearchPath({ term: 'ebola', tag: [1, 2] }), '/records/search?term=ebola&tag=1&tag=2');
  assert.equal(withSitePrefix('nigeria', '/records'), '/nigeria/records');
  assert.equal(withSitePrefix('', 'records'), '/records');
});

test('sites are enabled by default and can be switched off', () => {
  assert.equal(normalizeSiteConfig({}, 'a').enabled, true);
  assert.equal(normalizeSiteConfig({ enabled: false }, 'b').enabled, false);
});

test('publish wizard validation mirrors the Laravel rules', async () => {
  const { validatePublishSubmission, PUBLISH_WIZARD } = await import('../src/publish-wizard.js');
  assert.equal(PUBLISH_WIZARD.steps.length, 2);

  const bad = validatePublishSubmission({ upload_type: 'attachment' }, { minWords: 10, hasFile: false });
  assert.equal(bad.valid, false);
  assert.ok(bad.errors.title);
  assert.ok(bad.errors.file);
  assert.ok(bad.errors.countries);
  assert.ok(bad.steps.start.length > 0);

  const good = validatePublishSubmission({
    upload_type: 'attachment',
    title: 'Continental cholera readiness review',
    description: 'word '.repeat(60),
    tags: [1],
    theme: 2,
    sub_theme: 3,
    data_category_id: 4,
    associated_authors: 'Jane Doe',
    author_affiliation: 'Africa CDC',
    rccs: ['all'],
  }, { minWords: 10, hasFile: true });
  assert.deepEqual(good.errors, {});
  assert.equal(good.valid, true);

  // Admin-configured required fields are honoured (tags switched off, doi switched on).
  const custom = validatePublishSubmission({
    upload_type: 'link', link: 'https://example.org/x.pdf', title: 'T'.repeat(10),
    description: 'word '.repeat(60), theme: 1, sub_theme: 1, data_category_id: 1,
    associated_authors: 'x', author_affiliation: 'y', rccs: ['all'],
  }, { minWords: 10, requiredFields: { tags: false, doi: true } });
  assert.equal(custom.errors.tags, undefined);
  assert.ok(custom.errors.doi);
});
