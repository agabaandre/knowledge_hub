import { TtlCache, MINUTE } from '../cache.js';
import { now } from '../db/helpers.js';
import { SUPPORTED_TYPES, chatComplete } from './client.js';

const cache = new TtlCache({ name: 'ai-settings' });
const MASK = '••••••••';

/**
 * Default provider list. Env vars seed keys on first read so an .env-configured
 * hub works before anyone opens the admin page (same spirit as Laravel's
 * env-or-db source priority, but with one storage shape).
 */
const defaultSettings = (env = process.env) => ({
  enabled: true,
  providers: [
    { id: 'openai', label: 'OpenAI', type: 'openai', baseUrl: '', apiKey: env.OPENAI_API_KEY ?? env.OPEN_API_KEY ?? '', model: env.OPENAI_MODEL ?? 'gpt-4o-mini', enabled: Boolean(env.OPENAI_API_KEY ?? env.OPEN_API_KEY) },
    { id: 'anthropic', label: 'Anthropic Claude', type: 'anthropic', baseUrl: '', apiKey: env.ANTHROPIC_API_KEY ?? env.CLAUDE_API_KEY ?? '', model: env.ANTHROPIC_MODEL ?? 'claude-haiku-5-5', enabled: Boolean(env.ANTHROPIC_API_KEY ?? env.CLAUDE_API_KEY) },
    { id: 'gemini', label: 'Google Gemini', type: 'gemini', baseUrl: '', apiKey: env.GEMINI_API_KEY ?? '', model: env.GEMINI_MODEL ?? 'gemini-2.5-flash', enabled: Boolean(env.GEMINI_API_KEY) },
    { id: 'deepseek', label: 'DeepSeek', type: 'openai', baseUrl: 'https://api.deepseek.com/v1', apiKey: env.DEEPSEEK_API_KEY ?? '', model: env.DEEPSEEK_MODEL ?? 'deepseek-chat', enabled: Boolean(env.DEEPSEEK_API_KEY) },
    { id: 'custom', label: 'Custom (OpenAI-compatible)', type: 'openai', baseUrl: env.AI_CUSTOM_BASE_URL ?? '', apiKey: env.AI_CUSTOM_API_KEY ?? '', model: env.AI_CUSTOM_MODEL ?? '', enabled: Boolean(env.AI_CUSTOM_BASE_URL) },
  ],
  /** Per-feature ordered fallback chain of provider ids. */
  features: {
    insights: ['openai', 'anthropic', 'gemini', 'deepseek', 'custom'],
    chat: ['openai', 'anthropic', 'gemini', 'deepseek', 'custom'],
    summarise: ['openai', 'anthropic', 'gemini', 'deepseek', 'custom'],
    translate: ['openai', 'anthropic', 'gemini', 'deepseek', 'custom'],
  },
});

export class AiSettings {
  constructor(site) {
    this.site = site;
    this.db = site.db;
  }

  async load() {
    return cache.remember(`ai:${this.site.id}`, 5 * MINUTE, async () => {
      if (!(await this.db.schema.hasTable('ai_settings'))) return defaultSettings();
      const row = await this.db('ai_settings').orderBy('id').first();
      if (!row?.data) return defaultSettings();
      const stored = typeof row.data === 'string' ? JSON.parse(row.data) : row.data;
      const defaults = defaultSettings();
      return {
        ...defaults,
        ...stored,
        providers: (stored.providers ?? defaults.providers).filter((p) => SUPPORTED_TYPES.includes(p.type)),
        features: { ...defaults.features, ...(stored.features ?? {}) },
      };
    });
  }

  async save(data, userId) {
    const existing = await this.db('ai_settings').orderBy('id').first();
    const payload = { data: JSON.stringify(data), updated_by: userId ?? null, updated_at: now() };
    if (existing) await this.db('ai_settings').where({ id: existing.id }).update(payload);
    else await this.db('ai_settings').insert({ ...payload, created_at: now() });
    cache.forget(`ai:${this.site.id}`);
  }

  /** Settings safe to show in the admin UI: keys are masked, never echoed back. */
  async masked() {
    const settings = await this.load();
    return {
      ...settings,
      providers: settings.providers.map((p) => ({ ...p, apiKey: p.apiKey ? MASK : '', hasKey: Boolean(p.apiKey) })),
    };
  }

  /** Merge an admin update: a masked apiKey means "keep the stored one". */
  async applyUpdate(update, userId) {
    const current = await this.load();
    const providers = (update.providers ?? []).map((incoming) => {
      const stored = current.providers.find((p) => p.id === incoming.id);
      const apiKey = !incoming.apiKey || incoming.apiKey === MASK ? (stored?.apiKey ?? '') : incoming.apiKey;
      return { ...incoming, apiKey };
    });
    const next = { enabled: Boolean(update.enabled), providers, features: update.features ?? current.features };
    await this.save(next, userId);
    return next;
  }

  /** Run one feature through its fallback chain of enabled, configured providers. */
  async completeForFeature(feature, options) {
    const settings = await this.load();
    if (!settings.enabled) return { ok: false, error: 'AI features are turned off for this site.' };

    const chain = (settings.features[feature] ?? settings.features.summarise ?? [])
      .map((id) => settings.providers.find((p) => p.id === id))
      .filter((p) => p?.enabled && p.model && (p.apiKey || p.baseUrl));
    if (chain.length === 0) return { ok: false, error: `No AI provider is enabled and configured for ${feature}.` };

    let lastError = 'No provider answered.';
    for (const provider of chain) {
      const result = await chatComplete(provider, options);
      if (result.ok) return { ...result, provider: provider.id };
      lastError = result.error;
    }
    return { ok: false, error: lastError };
  }

  /** Connectivity test for the admin UI. */
  async testProvider(providerId) {
    const settings = await this.load();
    const provider = settings.providers.find((p) => p.id === providerId);
    if (!provider) return { ok: false, error: `Unknown provider "${providerId}".` };
    const result = await chatComplete(provider, { messages: [{ role: 'user', content: 'Reply with the single word: ok' }], maxTokens: 10, timeoutMs: 20000 });
    return result.ok ? { ok: true, reply: result.content.slice(0, 80) } : result;
  }
}
