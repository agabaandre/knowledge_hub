#!/usr/bin/env node
/**
 * HTTP smoke test against a running Knowledge Hub API. Works the same for a
 * native install, a VM, or the Docker stack. Exits 1 if any check fails.
 *
 *   node scripts/smoke-test.js --base=http://localhost:4000
 *   node scripts/smoke-test.js --base=http://10.0.0.5:4000 --email=admin@x.org --password=...
 *
 * With credentials it also checks login, admin metrics and module toggling
 * (it switches `faqs` off and back on).
 */
const args = Object.fromEntries(process.argv.slice(2).map((arg) => arg.replace(/^--/, '').split('=')).map(([k, ...v]) => [k, v.join('=') || true]));
const base = String(args.base ?? 'http://localhost:4000').replace(/\/+$/, '');
let cookie = '';
let lastSetCookie = '';
const results = [];

const call = async (path, { method = 'GET', body } = {}) => {
  const response = await fetch(`${base}${path}`, {
    method,
    headers: { ...(body ? { 'content-type': 'application/json' } : {}), ...(cookie ? { cookie } : {}) },
    body: body ? JSON.stringify(body) : undefined,
    redirect: 'manual',
  });
  const setCookie = response.headers.get('set-cookie');
  if (setCookie) {
    lastSetCookie = setCookie;
    cookie = setCookie.split(';')[0];
  }
  const text = await response.text();
  let json = null;
  try { json = JSON.parse(text); } catch { /* not JSON */ }
  const contentType = response.headers.get('content-type') ?? '';
  return { status: response.status, type: response.headers.get('content-type') ?? '', text, json, contentType };
};

const check = async (name, run) => {
  try {
    const detail = await run();
    results.push({ name, ok: true, detail: detail ?? '' });
  } catch (error) {
    results.push({ name, ok: false, detail: error.message });
  }
};

const expect = (condition, message) => {
  if (!condition) throw new Error(message);
};

await check('health', async () => {
  const r = await call('/health');
  expect(r.status === 200 && r.json?.status === 'ok', `status ${r.status}`);
});
await check('site manifest', async () => {
  const r = await call('/api/site/manifest');
  expect(r.status === 200 && r.json?.site?.id, `status ${r.status}`);
  return `site=${r.json.site.id} modules=${Object.values(r.json.modules).filter(Boolean).length} on`;
});
await check('records search', async () => {
  const r = await call('/api/publications/search?term=health&per_page=5');
  expect(r.status === 200 && Array.isArray(r.json?.data), `status ${r.status}`);
  return `${r.json.meta.total} results`;
});
await check('search filters (tag, sort, file type)', async () => {
  const r = await call('/api/publications/search?tag=1&sort=popular&file_type_id=1&file_type_id=2');
  expect(r.status === 200, `status ${r.status} ${r.text.slice(0, 120)}`);
});
await check('search rejects bad input', async () => {
  const r = await call('/api/publications/search?per_page=9999');
  expect(r.status === 422, `expected 422, got ${r.status}`);
});
await check('home sections', async () => {
  const r = await call('/api/publications/home');
  expect(r.status === 200 && 'featured' in (r.json ?? {}), `status ${r.status}`);
});
await check('lookups', async () => {
  const r = await call('/api/publications/lookups');
  expect(r.status === 200 && Array.isArray(r.json?.themes), `status ${r.status}`);
  return `${r.json.themes.length} themes, ${r.json.countries.length} countries`;
});
await check('first publication detail', async () => {
  const list = await call('/api/publications/search?per_page=1');
  const first = list.json?.data?.[0];
  if (!first) return 'skipped (no publications)';
  const r = await call(`/api/publications/${first.slug ?? first.id}`);
  expect(r.status === 200 && r.json?.data?.id === first.id, `status ${r.status}`);
  return first.title;
});
await check('unknown publication → 404', async () => {
  const r = await call('/api/publications/this-slug-does-not-exist-xyz');
  expect(r.status === 404, `expected 404, got ${r.status}`);
});
for (const [name, path] of [['forums', '/api/forums'], ['communities', '/api/communities'], ['health topics', '/api/health-topics'], ['events', '/api/events'], ['faqs', '/api/faqs']]) {
  await check(name, async () => {
    const r = await call(path);
    expect(r.status === 200 || r.status === 404, `status ${r.status} ${r.text.slice(0, 120)}`);
    return r.status === 404 ? 'module disabled' : '';
  });
}
await check('AU languages in manifest', async () => {
  const r = await call('/api/site/manifest');
  const codes = (r.json?.languages ?? []).map((l) => l.code);
  expect(['en', 'fr', 'ar', 'es', 'pt', 'sw'].every((c) => codes.includes(c)), `got: ${codes.join(',')}`);
  return codes.join(', ');
});
await check('french dictionary', async () => {
  const r = await call('/api/i18n/fr');
  expect(r.status === 200 && r.json?.messages?.['frontend_nav.home'] === 'Accueil', `status ${r.status}`);
  return `${Object.keys(r.json.messages).length} keys`;
});
await check('arabic is RTL', async () => {
  const r = await call('/api/i18n/ar');
  expect(r.status === 200 && r.json?.dir === 'rtl', `dir ${r.json?.dir}`);
});
await check('robots.txt', async () => {
  const r = await call('/robots.txt');
  expect(r.status === 200 && r.text.includes('Sitemap:'), `status ${r.status}`);
});
await check('sitemap index', async () => {
  const r = await call('/sitemap.xml');
  expect(r.status === 200 && r.text.includes('<sitemapindex'), `status ${r.status}`);
});
await check('sitemap publications-1', async () => {
  const r = await call('/sitemaps/publications-1.xml');
  expect(r.status === 200 && r.text.includes('<urlset'), `status ${r.status}`);
  return `${(r.text.match(/<url>/g) ?? []).length} urls`;
});
await check('llms.txt', async () => {
  const r = await call('/llms.txt');
  expect(r.status === 200 && r.text.startsWith('# '), `status ${r.status}`);
});
await check('prometheus /metrics', async () => {
  const r = await call('/metrics');
  expect((r.status === 200 && r.text.includes('khub_http_request_duration_seconds')) || r.status === 401, `status ${r.status}`);
  return r.status === 401 ? 'token protected' : '';
});
await check('admin API requires login', async () => {
  const r = await call('/api/admin/modules');
  expect(r.status === 401, `expected 401, got ${r.status}`);
});
await check('SPA page served', async () => {
  const r = await call('/records/search');
  expect(r.status === 200 || r.status === 404, `status ${r.status}`);
  return r.type.includes('text/html') ? 'html' : 'no frontend build (API only)';
});

if (args.email && args.password) {
  await check('login', async () => {
    const r = await call('/api/auth/login', { method: 'POST', body: { email: args.email, password: args.password } });
    expect(r.status === 200 && r.json?.user, `status ${r.status} ${r.json?.error ?? ''}`);
    expect(r.json.user.isAdmin, 'user is not an admin');
    // Browsers drop Secure cookies on plain http, so login would silently fail there.
    expect(!(base.startsWith('http://') && /;\s*secure/i.test(lastSetCookie)), 'session cookie is Secure on an http URL; browsers will drop it');
  });
  await check('admin metrics', async () => {
    const r = await call('/api/admin/metrics?fresh=1');
    expect(r.status === 200 && r.json?.database?.status === 'ok', `status ${r.status} db=${r.json?.database?.status}`);
    return `${r.json.database.client} ${r.json.database.version} ${r.json.database.latencyMs}ms`;
  });
  await check('module toggle off/on', async () => {
    const off = await call('/api/admin/modules/faqs', { method: 'PUT', body: { enabled: false } });
    expect(off.status === 200, `disable status ${off.status}`);
    const blocked = await call('/api/faqs');
    const on = await call('/api/admin/modules/faqs', { method: 'PUT', body: { enabled: true } });
    expect(blocked.status === 404, `faqs still reachable while disabled (${blocked.status})`);
    expect(on.status === 200, `enable status ${on.status}`);
  });
  await check('admin dashboard', async () => {
    const r = await call('/api/admin/dashboard');
    expect(r.status === 200 && r.json?.content, `status ${r.status}`);
    return `${r.json.content.publications} publications, ${r.json.users.total} users`;
  });
  await check('approvals inbox', async () => {
    const r = await call('/api/admin/approvals');
    expect(r.status === 200 && Array.isArray(r.json?.publications), `status ${r.status}`);
  });
  await check('settings editor', async () => {
    const r = await call('/api/admin/settings');
    expect(r.status === 200 && r.json?.settings?.site_name !== undefined, `status ${r.status}`);
  });
  await check('ai settings masked', async () => {
    const r = await call('/api/admin/ai');
    expect(r.status === 200 && Array.isArray(r.json?.providers), `status ${r.status}`);
    expect(!JSON.stringify(r.json).includes('sk-'), 'a raw API key leaked into the response');
    return `${r.json.providers.length} providers`;
  });
  await check('themes list', async () => {
    const r = await call('/api/admin/themes');
    expect(r.status === 200 && r.json?.themes?.some((t) => t.slug === 'classic'), `status ${r.status}`);
    return `active: ${r.json.active}`;
  });
  await check('admin sections (users/roles/languages/lists/logs/mailing)', async () => {
    for (const section of ['users', 'roles', 'languages', 'lists', 'logs', 'mailing']) {
      const r = await call(`/api/admin/${section}`);
      expect(r.status === 200, `${section} → ${r.status}`);
    }
  });
  await check('admin nav filtered by permissions (shared definition)', async () => {
    const r = await call('/api/admin/nav');
    expect(r.status === 200 && Array.isArray(r.json?.groups) && r.json.groups.length >= 3, `status ${r.status}`);
    const items = r.json.groups.flatMap((group) => group.items);
    expect(items.some((item) => item.path === '/admin/publications'), 'Manage Resources missing from nav');
    return `${items.length} items in ${r.json.groups.length} groups`;
  });
  await check('dropdown lists cover the production menu', async () => {
    const r = await call('/api/admin/lists');
    const keys = (r.json?.lists ?? []).map((list) => list.key);
    for (const key of ['sub-categories', 'geographical-coverage', 'countries', 'static-links', 'faqs', 'quotes']) {
      expect(keys.includes(key), `missing list "${key}"`);
    }
    const faqs = r.json.lists.find((list) => list.key === 'faqs');
    expect(faqs.extraType === 'textarea', 'faqs answer should be a textarea');
    return `${keys.length} lists`;
  });
  await check('publications admin (manage resources)', async () => {
    const r = await call('/api/admin/publications?status=all&per_page=1');
    expect(r.status === 200 && typeof r.json?.meta?.total === 'number', `status ${r.status}`);
    return `${r.json.meta.total} resources`;
  });
  await check('publication admin details (any status)', async () => {
    const list = await call('/api/admin/publications?status=all&per_page=1');
    const first = list.json?.data?.[0];
    expect(first, 'no publications to inspect');
    const r = await call(`/api/admin/publications/${first.id}`);
    expect(r.status === 200 && r.json?.data?.title && Array.isArray(r.json.data.comments), `status ${r.status}`);
    return `#${first.id} "${r.json.data.title.slice(0, 30)}" · ${r.json.data.comments.length} comment(s)`;
  });
  await check('document registry + retention sweep', async () => {
    const r = await call('/api/admin/documents?per_page=5');
    expect(r.status === 200 && Array.isArray(r.json?.data) && typeof r.json?.meta?.total === 'number', `status ${r.status}`);
    const dupes = await call('/api/admin/documents/duplicates');
    expect(dupes.status === 200 && Array.isArray(dupes.json?.checksumGroups), `duplicates ${dupes.status}`);
    const sweep = await call('/api/admin/documents/sweep', { method: 'POST' });
    expect(sweep.status === 200 && typeof sweep.json?.disposed === 'number', `sweep ${sweep.status}`);
    return `${r.json.meta.total} document(s); sweep disposed ${sweep.json.disposed}, emptied ${sweep.json.emptied ?? 0}`;
  });
  await check('version chain on resource detail', async () => {
    const list = await call('/api/admin/publications?status=all&per_page=1');
    const r = await call(`/api/publications/${list.json.data[0].id}`);
    expect(r.status === 200 && Array.isArray(r.json?.versions) && r.json.versions.length >= 1, `status ${r.status}`);
    return `${r.json.versions.length} version(s) in chain`;
  });
  await check('storage, sites, maps, federation admin APIs', async () => {
    const storage = await call('/api/admin/storage');
    expect(storage.status === 200 && Array.isArray(storage.json?.sites), `storage ${storage.status}`);
    const sites = await call('/api/admin/sites');
    expect(sites.status === 200 && sites.json.sites.some((site) => site.id === 'default'), `sites ${sites.status}`);
    const geo = await call('/api/admin/analytics/geo');
    expect(geo.status === 200 && Array.isArray(geo.json?.publications), `geo ${geo.status}`);
    const series = await call('/api/admin/analytics/series');
    expect(series.status === 200 && series.json?.months?.length === 12, `series ${series.status}`);
    const hubs = await call('/api/admin/federation/hubs');
    expect(hubs.status === 200 && Array.isArray(hubs.json?.hubs), `federation ${hubs.status}`);
    return `${sites.json.sites.length} site(s), ${hubs.json.hubs.length} federated hub(s)`;
  });
  await check('communities admin (COPs + participants)', async () => {
    const stats = await call('/api/admin/communities/stats');
    expect(stats.status === 200 && typeof stats.json?.total === 'number', `stats ${stats.status}`);
    const members = await call('/api/admin/communities/participants?per_page=1');
    expect(members.status === 200 && Array.isArray(members.json?.data), `participants ${members.status}`);
    return `${stats.json.total} communities, ${stats.json.approvedMemberships} memberships`;
  });
  await check('forums admin management', async () => {
    const r = await call('/api/admin/forums?per_page=1');
    expect(r.status === 200 && Array.isArray(r.json?.data), `status ${r.status}`);
    return `${r.json.meta.total} thread(s)`;
  });
  await check('publications CSV export', async () => {
    const r = await call('/api/admin/publications/export?status=all', { raw: true });
    expect(r.status === 200 && r.contentType?.includes('text/csv'), `status ${r.status} ${r.contentType}`);
    expect(r.text.includes('ID,Title,Author'), 'CSV header missing');
    return `${r.text.trim().split('\n').length - 1} rows`;
  });
  await check('ai draft-description responds', async () => {
    const r = await call('/api/ai-search/draft-description', { method: 'POST', body: { title: 'Smoke test resource title' } });
    expect([200, 503].includes(r.status) && typeof r.json?.ok === 'boolean', `status ${r.status}`);
    return r.json.ok ? `drafted via ${r.json.provider}` : 'no provider configured (graceful)';
  });
  await check('analytics with period filter', async () => {
    const r = await call('/api/admin/analytics?period=30d');
    expect(r.status === 200 && typeof r.json?.signups === 'number', `status ${r.status}`);
    return `${r.json.signups} signups / 30d`;
  });
  await check('permission catalogue seeded', async () => {
    const r = await call('/api/admin/roles');
    expect(r.json?.catalogue?.length >= 5, 'catalogue missing');
    const admin = r.json.roles.find((role) => role.name === 'Admin');
    expect(admin?.permissions?.length >= 20, `Admin has ${admin?.permissions?.length} permissions`);
    return `${admin.permissions.length} permissions on Admin`;
  });
  await check('core module protected', async () => {
    const r = await call('/api/admin/modules/publications', { method: 'PUT', body: { enabled: false } });
    expect(r.status === 422, `expected 422, got ${r.status}`);
  });
}

const width = Math.max(...results.map((r) => r.name.length));
for (const r of results) console.log(`${r.ok ? 'PASS' : 'FAIL'}  ${r.name.padEnd(width)}  ${r.detail}`);
const failed = results.filter((r) => !r.ok).length;
console.log(`\n${results.length - failed}/${results.length} passed against ${base}`);
process.exitCode = failed ? 1 : 0;
