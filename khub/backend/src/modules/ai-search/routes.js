import { Router } from 'express';
import { z } from 'zod';
import rateLimit from 'express-rate-limit';
import { publicationPath } from '@khub/shared';
import { validate } from '../../core/http/validate.js';
import { asyncHandler } from '../../core/http/errors.js';
import { AiSettings } from '../../core/ai/settings.js';
import { PublicationsRepository } from '../publications/repository.js';
import { TtlCache, HOUR } from '../../core/cache.js';

const insightsSchema = z.object({ term: z.string().trim().min(2).max(200) });
const draftSchema = z.object({
  title: z.string().trim().min(5).max(300),
  link: z.string().trim().max(500).optional().default(''),
  language: z.string().trim().max(10).optional().default('en'),
});

const chatSchema = z.object({
  messages: z.array(z.object({ role: z.enum(['user', 'assistant']), content: z.string().trim().min(1).max(4000) })).min(1).max(20),
});

const limiter = rateLimit({ windowMs: 60 * 1000, limit: 10, standardHeaders: true, legacyHeaders: false });
const insightsCache = new TtlCache({ name: 'ai-insights' });

const sourcesFor = async (req, term) => {
  const { rows } = await new PublicationsRepository(req.db).search({ term }, { page: 1, per_page: 6 }, req.user);
  return rows.map((row) => ({
    title: row.title,
    excerpt: String(row.description ?? '').replace(/<[^>]*>/g, '').slice(0, 400),
    year: row.year_published,
    author: row.author_name,
    url: `${req.site.publicUrl}${publicationPath(row)}`,
  }));
};

/**
 * AI search assistance, same features as the Laravel portal (search insights +
 * follow-up chat grounded in the top matching records), on the new
 * provider-agnostic client.
 */
export const aiSearchRouter = () => {
  const router = Router();

  router.post(
    '/insights',
    limiter,
    validate(insightsSchema),
    asyncHandler(async (req, res) => {
      const term = req.body.term;
      const payload = await insightsCache.remember(`insights:${req.site.id}:${term.toLowerCase()}`, HOUR, async () => {
        const sources = await sourcesFor(req, term);
        if (sources.length === 0) return { ok: false, error: 'No matching resources to summarise.' };
        const result = await new AiSettings(req.site).completeForFeature('insights', {
          maxTokens: 500,
          messages: [
            { role: 'system', content: 'You summarise public health resources for the Africa CDC Knowledge Hub. Answer in 3-5 short sentences, factual, grounded ONLY in the provided sources. Mention which sources support the summary by title.' },
            { role: 'user', content: `Search term: "${term}"\n\nSources:\n${sources.map((s, i) => `${i + 1}. ${s.title} (${s.year ?? 'n.d.'}, ${s.author ?? 'unknown'})\n${s.excerpt}`).join('\n\n')}` },
          ],
        });
        return result.ok ? { ok: true, summary: result.content, provider: result.provider, sources } : result;
      });
      res.status(payload.ok ? 200 : 503).json(payload);
    }),
  );

  // AI draft description for the publish wizard (the Laravel "AI summary" assist).
  router.post(
    '/draft-description',
    limiter,
    validate(draftSchema),
    asyncHandler(async (req, res) => {
      if (!req.user) return res.status(401).json({ ok: false, error: 'Sign in to use AI drafting.' });
      const result = await new AiSettings(req.site).completeForFeature('summarise', {
        maxTokens: 400,
        messages: [
          { role: 'system', content: 'You draft abstract-style descriptions for public health resources on the Africa CDC Knowledge Hub. Write 120-180 words, factual tone, no headings, in the requested language. The human will review and edit before submitting.' },
          { role: 'user', content: `Language: ${req.body.language}
Resource title: "${req.body.title}"${req.body.link ? `
Source link: ${req.body.link}` : ''}

Draft the description.` },
        ],
      });
      return res.status(result.ok ? 200 : 503).json(result.ok ? { ok: true, draft: result.content, provider: result.provider } : result);
    }),
  );

  router.post(
    '/chat',
    limiter,
    validate(chatSchema),
    asyncHandler(async (req, res) => {
      const lastUser = [...req.body.messages].reverse().find((m) => m.role === 'user');
      const sources = await sourcesFor(req, lastUser.content.slice(0, 120));
      const result = await new AiSettings(req.site).completeForFeature('chat', {
        maxTokens: 700,
        messages: [
          { role: 'system', content: `You are the Knowledge Hub search assistant. Ground answers in these resources when relevant and cite them by title:\n${sources.map((s) => `- ${s.title}: ${s.excerpt.slice(0, 160)}`).join('\n') || '(no matching resources)'}` },
          ...req.body.messages,
        ],
      });
      res.status(result.ok ? 200 : 503).json(result.ok ? { ok: true, reply: result.content, provider: result.provider, sources } : result);
    }),
  );

  return router;
};
