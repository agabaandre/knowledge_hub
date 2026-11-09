import { Router } from 'express';
import { HubStorage } from './hub-storage.js';
import { asyncHandler, HttpError } from '../http/errors.js';

/**
 * GET /hub-media/{path} — streams a stored upload (same URL as the Laravel hub).
 */
export const mediaRouter = () => {
  const router = Router();

  router.get(
    /^\/hub-media\/(.+)$/,
    asyncHandler(async (req, res) => {
      const relative = decodeURIComponent(req.params[0]);
      const absolute = await new HubStorage(req.site).resolveReadableFile(relative);
      if (!absolute) throw new HttpError(404, 'File not found');

      res.setHeader('Cache-Control', 'public, max-age=86400');
      res.sendFile(absolute);
    }),
  );

  return router;
};
