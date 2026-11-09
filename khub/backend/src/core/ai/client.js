/**
 * Provider-agnostic chat-completion client.
 *
 * A provider is plain data: { type, baseUrl, apiKey, model, headers }.
 * Three wire formats cover every mainstream and self-hosted service:
 *   - "openai"    OpenAI, Azure OpenAI, DeepSeek, Mistral, Groq, OpenRouter,
 *                 Ollama, LM Studio, vLLM, ... (any /chat/completions endpoint)
 *   - "anthropic" Anthropic's /v1/messages
 *   - "gemini"    Google generativelanguage :generateContent
 *
 * This replaces the Laravel per-provider methods with one table of adapters,
 * so a new HTTP provider is a config row, not new code.
 */
const DEFAULT_BASE_URLS = {
  openai: 'https://api.openai.com/v1',
  anthropic: 'https://api.anthropic.com/v1',
  gemini: 'https://generativelanguage.googleapis.com/v1beta',
};

const splitSystem = (messages) => {
  const system = messages.filter((m) => m.role === 'system').map((m) => m.content).join('\n');
  return { system, rest: messages.filter((m) => m.role !== 'system') };
};

const ADAPTERS = {
  openai: {
    request: ({ baseUrl, apiKey, model, messages, maxTokens, json }) => ({
      url: `${baseUrl}/chat/completions`,
      headers: { Authorization: `Bearer ${apiKey}` },
      body: { model, messages, max_tokens: maxTokens, ...(json ? { response_format: { type: 'json_object' } } : {}) },
    }),
    content: (data) => data.choices?.[0]?.message?.content,
  },
  anthropic: {
    request: ({ baseUrl, apiKey, model, messages, maxTokens }) => {
      const { system, rest } = splitSystem(messages);
      return {
        url: `${baseUrl}/messages`,
        headers: { 'x-api-key': apiKey, 'anthropic-version': '2023-06-01' },
        body: { model, max_tokens: maxTokens, ...(system ? { system } : {}), messages: rest },
      };
    },
    content: (data) => data.content?.filter((block) => block.type === 'text').map((block) => block.text).join(''),
  },
  gemini: {
    request: ({ baseUrl, apiKey, model, messages, maxTokens }) => {
      const { system, rest } = splitSystem(messages);
      return {
        url: `${baseUrl}/models/${model}:generateContent?key=${encodeURIComponent(apiKey)}`,
        headers: {},
        body: {
          ...(system ? { systemInstruction: { parts: [{ text: system }] } } : {}),
          contents: rest.map((m) => ({ role: m.role === 'assistant' ? 'model' : 'user', parts: [{ text: m.content }] })),
          generationConfig: { maxOutputTokens: maxTokens },
        },
      };
    },
    content: (data) => data.candidates?.[0]?.content?.parts?.map((part) => part.text).join(''),
  },
};

export const SUPPORTED_TYPES = Object.freeze(Object.keys(ADAPTERS));

/**
 * @param {{type: string, baseUrl?: string, apiKey?: string, model: string, headers?: Record<string,string>}} provider
 * @param {{messages: Array<{role: string, content: string}>, maxTokens?: number, json?: boolean, timeoutMs?: number}} options
 * @returns {Promise<{ok: true, content: string} | {ok: false, error: string}>}
 */
export const chatComplete = async (provider, { messages, maxTokens = 1024, json = false, timeoutMs = 45000 }) => {
  const adapter = ADAPTERS[provider.type];
  if (!adapter) return { ok: false, error: `Unsupported provider type "${provider.type}". Use one of: ${SUPPORTED_TYPES.join(', ')}.` };
  if (!provider.model) return { ok: false, error: 'No model configured.' };
  if (!provider.apiKey && provider.type !== 'openai') return { ok: false, error: 'API key is not configured.' };

  const baseUrl = (provider.baseUrl || DEFAULT_BASE_URLS[provider.type]).replace(/\/+$/, '');
  const { url, headers, body } = adapter.request({ ...provider, baseUrl, messages, maxTokens, json });

  try {
    const response = await fetch(url, {
      method: 'POST',
      signal: AbortSignal.timeout(timeoutMs),
      headers: { 'content-type': 'application/json', ...headers, ...(provider.headers ?? {}) },
      body: JSON.stringify(body),
    });
    const data = await response.json().catch(() => ({}));
    if (!response.ok) {
      const message = data?.error?.message ?? data?.message ?? `HTTP ${response.status}`;
      return { ok: false, error: `${provider.type} API error: ${message}` };
    }
    const content = adapter.content(data);
    if (!content) return { ok: false, error: `Empty response from ${provider.type}.` };
    return { ok: true, content };
  } catch (error) {
    return { ok: false, error: error.name === 'TimeoutError' ? 'The AI provider timed out.' : `Request failed: ${error.message}` };
  }
};
