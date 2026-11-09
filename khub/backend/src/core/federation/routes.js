import { Router } from 'express';
import { z } from 'zod';
import { asyncHandler, HttpError } from '../http/errors.js';
import { validate } from '../http/validate.js';
import { FederationService, portablePublication } from './service.js';

/** Incoming federation calls authenticate with this hub's own token (setting.federation_api_token). */
const requireFederationToken = (settings) => async (req, res, next) => {
  try {
    const siteSettings = await settings.forSite(req.site);
    const expected = String(siteSettings.federation_api_token ?? '').trim();
    const presented = String(req.get('authorization') ?? '').replace(/^Bearer\s+/i, '').trim();
    if (!expected) throw new HttpError(403, 'Federation is not enabled on this hub (no federation token configured).');
    if (!presented || presented !== expected) throw new HttpError(401, 'Invalid federation token.');
    return next();
  } catch (error) {
    return next(error);
  }
};

const importSchema = z.object({
  source: z.string().trim().url(),
  publications: z.array(z.object({
    remoteId: z.string().trim().min(1).max(100),
    title: z.string().trim().min(3).max(500),
    description: z.string().optional().default(''),
    yearPublished: z.coerce.number().int().nullable().optional(),
    link: z.string().trim().max(500).optional(),
    authors: z.string().trim().max(1000).optional(),
    publisher: z.string().trim().max(255).nullable().optional(),
    doi: z.string().trim().max(255).nullable().optional(),
  })).max(200),
});

/** The server-to-server federation API this hub exposes to its partners. */
export const federationRouter = ({ settings }) => {
  const router = Router();
  const auth = requireFederationToken(settings);

  router.get('/manifest', auth, asyncHandler(async (req, res) => {
    const siteSettings = await settings.forSite(req.site);
    res.json({ hub: siteSettings.site_name, url: req.site.publicUrl, protocol: 'khub-federation/1' });
  }));

  router.get('/publications', auth, asyncHandler(async (req, res) => {
    const perPage = Math.min(200, Math.max(1, Number(req.query.per_page) || 100));
    const rows = await new FederationService(req.site).exportQuery().orderBy('publication.updated_at', 'desc').limit(perPage);
    res.json({ data: rows.map((row) => portablePublication(req.site, row)) });
  }));

  router.post('/import', auth, validate(importSchema), asyncHandler(async (req, res) => {
    const hub = { base_url: req.body.source };
    const service = new FederationService(req.site);
    let created = 0;
    let updated = 0;
    for (const record of req.body.publications) {
      // The same upsert path as pull, so push and pull stay identical.
      const result = await service.upsertImported(hub, record);
      if (result === 'created') created += 1;
      else updated += 1;
    }
    res.json({ created, updated });
  }));

  return router;
};
