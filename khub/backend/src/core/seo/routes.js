import { Router } from 'express';
import { ROBOTS_DISALLOW } from '@khub/shared';
import { asyncHandler, HttpError } from '../http/errors.js';
import { SitemapService } from './sitemap-service.js';
import { renderLlmsTxt } from './llms-txt.js';
import { TtlCache, HOUR } from '../cache.js';

const cache = new TtlCache({ name: 'seo' });
const SEO_TTL = 6 * HOUR;

const send = (res, type, body) => {
  res.setHeader('Content-Type', type);
  res.setHeader('Cache-Control', 'public, max-age=3600');
  res.send(body);
};

/**
 * /robots.txt, /sitemap.xml, /sitemaps/{section}.xml, /llms.txt
 */
export const seoRouter = ({ sites, settings }) => {
  const router = Router();

  router.get('/robots.txt', (req, res) => {
    const lines = [
      `# ${req.site.name}`, 'User-agent: *', 'Allow: /', '',
      '# Admin, account, and non-public areas',
      ...ROBOTS_DISALLOW.map((path) => `Disallow: ${path}`), '',
      `Sitemap: ${req.site.publicUrl}/sitemap.xml`, '',
      '# LLM / agent site map (llmstxt.org convention)', `# See: ${req.site.publicUrl}/llms.txt`, '',
      'User-agent: facebookexternalhit', 'Allow: /', '', 'User-agent: Twitterbot', 'Allow: /', '', 'User-agent: LinkedInBot', 'Allow: /',
    ];
    send(res, 'text/plain; charset=UTF-8', `${lines.join('\n')}\n`);
  });

  router.get(
    '/sitemap.xml',
    asyncHandler(async (req, res) => {
      const xml = await cache.remember(`sitemap:index:${req.site.id}`, SEO_TTL, async () =>
        new SitemapService({ site: req.site, modules: await sites.moduleFlags(req.site) }).renderIndex(),
      );
      send(res, 'application/xml; charset=UTF-8', xml);
    }),
  );

  router.get(
    '/sitemaps/:name.xml',
    asyncHandler(async (req, res) => {
      const name = String(req.params.name);
      if (!/^[a-z0-9-]+$/.test(name)) throw new HttpError(404, 'Unknown sitemap');
      const xml = await cache.remember(`sitemap:${req.site.id}:${name}`, SEO_TTL, async () =>
        new SitemapService({ site: req.site, modules: await sites.moduleFlags(req.site) }).renderSection(name),
      );
      if (!xml) throw new HttpError(404, 'Unknown sitemap');
      send(res, 'application/xml; charset=UTF-8', xml);
    }),
  );

  router.get(
    '/llms.txt',
    asyncHandler(async (req, res) => {
      const body = await cache.remember(`llms:${req.site.id}`, SEO_TTL, async () =>
        renderLlmsTxt({ site: req.site, settings: await settings.forSite(req.site), modules: await sites.moduleFlags(req.site) }),
      );
      send(res, 'text/markdown; charset=UTF-8', body);
    }),
  );

  return router;
};
