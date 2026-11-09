import { Router } from 'express';
import { z } from 'zod';
import { asyncHandler } from '../http/errors.js';
import { validate } from '../http/validate.js';
import { AiSettings } from '../ai/settings.js';
import { SUPPORTED_TYPES } from '../ai/client.js';

const providerSchema = z.object({
  id: z.string().trim().regex(/^[a-z0-9-]{2,32}$/),
  label: z.string().trim().min(1).max(60),
  type: z.enum(SUPPORTED_TYPES),
  baseUrl: z.string().trim().max(300).refine((value) => value === '' || /^https?:\/\//.test(value), 'Base URL must be http(s)').optional().default(''),
  apiKey: z.string().trim().max(400).optional().default(''),
  model: z.string().trim().max(120),
  enabled: z.boolean(),
});

const updateSchema = z.object({
  enabled: z.boolean(),
  providers: z.array(providerSchema).max(12),
  features: z.record(z.string(), z.array(z.string())).optional(),
});

/**
 * AI configuration lives in core admin (not inside the ai-search module) so it
 * stays reachable while the module is switched off.
 */
export const aiRoutes = () => {
  const router = Router();

  router.get(
    '/ai',
    asyncHandler(async (req, res) => res.json(await new AiSettings(req.site).masked())),
  );

  router.put(
    '/ai',
    validate(updateSchema),
    asyncHandler(async (req, res) => {
      await new AiSettings(req.site).applyUpdate(req.body, req.user.id);
      res.json(await new AiSettings(req.site).masked());
    }),
  );

  router.post(
    '/ai/test/:providerId',
    asyncHandler(async (req, res) => {
      const result = await new AiSettings(req.site).testProvider(req.params.providerId);
      res.status(result.ok ? 200 : 502).json(result);
    }),
  );

  return router;
};
