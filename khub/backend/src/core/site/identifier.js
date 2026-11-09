/**
 * Permanent per-hub storage identifier derived from the public URL.
 * Port of Laravel's App\Support\HubSiteIdentifier so an existing
 * /var/khubdata/{site-id} folder is picked up unchanged after migration.
 *
 *   https://khub.com/andrew  → khub-com-andrew
 *   https://hub.example.com  → hub-example-com
 *   http://localhost:8080    → localhost-8080
 */
export const sanitizeSiteId = (id) => {
  const clean = String(id ?? '')
    .toLowerCase()
    .trim()
    .replace(/[^a-z0-9-]+/g, '-')
    .replace(/-+/g, '-')
    .replace(/^-|-$/g, '');
  return clean || 'default';
};

export const siteIdFromUrl = (appUrl, pathPrefix = '') => {
  const text = String(appUrl ?? '').trim();
  if (text === '') return 'default';

  let url;
  try {
    url = new URL(text);
  } catch {
    return sanitizeSiteId(text);
  }

  let host = url.hostname.toLowerCase();
  if (host.startsWith('www.')) host = host.slice(4);

  let slug = host.split('.').filter(Boolean).join('-');

  if (url.port && !['80', '443'].includes(url.port)) slug += `-${url.port}`;

  const path = [url.pathname, pathPrefix]
    .map((part) => String(part ?? '').replace(/^\/+|\/+$/g, ''))
    .filter(Boolean)
    .join('/');
  if (path) slug += `-${path.toLowerCase().replace(/[^a-z0-9]+/g, '-')}`;

  return sanitizeSiteId(slug);
};
