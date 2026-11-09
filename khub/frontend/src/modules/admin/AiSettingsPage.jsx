import { useEffect, useState } from 'react';
import { apiFetch } from '../../core/api.js';
import { AdminShell, AdminCard } from './AdminShell.jsx';
import { Flash } from '../../core/ui.jsx';

const TYPE_HELP = {
  openai: 'OpenAI wire format — also DeepSeek, Mistral, Groq, OpenRouter, Ollama, vLLM, Azure OpenAI or any /chat/completions endpoint.',
  anthropic: 'Anthropic Claude (/v1/messages).',
  gemini: 'Google Gemini (generateContent).',
};

/** Provider-agnostic AI configuration: any model, any HTTP provider, per-feature fallback. */
export const AiSettingsPage = () => {
  const [form, setForm] = useState(null);
  const [message, setMessage] = useState(null);
  const [busy, setBusy] = useState('');

  useEffect(() => {
    apiFetch('/api/admin/ai').then(setForm).catch((err) => setMessage({ error: err.message }));
  }, []);

  const setProvider = (index, key, value) =>
    setForm((previous) => ({ ...previous, providers: previous.providers.map((p, i) => (i === index ? { ...p, [key]: value } : p)) }));

  const save = async () => {
    setBusy('save');
    setMessage(null);
    try {
      const payload = await apiFetch('/api/admin/ai', { method: 'PUT', body: { enabled: form.enabled, providers: form.providers.map(({ hasKey, ...p }) => p), features: form.features } });
      setForm(payload);
      setMessage({ ok: 'AI settings saved.' });
    } catch (err) {
      setMessage({ error: err.details ? `${err.message}: ${err.details.map((d) => d.path).join(', ')}` : err.message });
    } finally {
      setBusy('');
    }
  };

  const test = async (provider) => {
    setBusy(`test-${provider.id}`);
    setMessage(null);
    try {
      const result = await apiFetch(`/api/admin/ai/test/${provider.id}`, { method: 'POST' });
      setMessage({ ok: `${provider.label}: connection OK — "${result.reply}"` });
    } catch (err) {
      setMessage({ error: `${provider.label}: ${err.message}` });
    } finally {
      setBusy('');
    }
  };

  const addProvider = () =>
    setForm((previous) => ({
      ...previous,
      providers: [...previous.providers, { id: `provider-${previous.providers.length + 1}`, label: 'New provider', type: 'openai', baseUrl: '', apiKey: '', model: '', enabled: false }],
    }));

  return (
    <AdminShell
      title="AI integration"
      actions={
        <span className="d-inline-flex" style={{ gap: 8 }}>
          <button type="button" className="btn btn-sm btn-outline-secondary bg-white" onClick={addProvider} disabled={!form}>
            <i className="fa fa-plus mr-1" aria-hidden="true" /> Add provider
          </button>
          <button type="button" className="btn btn-sm theme-bg text-white" onClick={save} disabled={!form || Boolean(busy)}>
            {busy === 'save' ? 'Saving…' : 'Save settings'}
          </button>
        </span>
      }
    >
      <Flash message={message} />
      {!form && !message?.error && <p role="status">Loading…</p>}
      {form && (
        <>
          <AdminCard>
            <label className="d-flex align-items-center mb-0" style={{ gap: 10, fontWeight: 400, cursor: 'pointer' }}>
              <span className="kh-switch">
                <input type="checkbox" checked={form.enabled} onChange={(event) => setForm({ ...form, enabled: event.target.checked })} />
                <span className="kh-slider" />
              </span>
              Enable AI features on this site (search insights, assistant chat, summaries)
            </label>
            <p className="text-muted medium mb-0 mt-2">
              Providers are tried in order until one answers. Any OpenAI-compatible HTTP endpoint works via the base URL, so self-hosted and regional providers plug in without code changes.
            </p>
          </AdminCard>

          {form.providers.map((provider, index) => (
            <AdminCard key={provider.id}>
              <div className="row">
                <div className="col-md-3 form-group">
                  <label>Provider</label>
                  <input className="form-control" value={provider.label} onChange={(event) => setProvider(index, 'label', event.target.value)} />
                  <small className="text-muted">id: {provider.id}</small>
                </div>
                <div className="col-md-2 form-group">
                  <label>Wire format</label>
                  <select className="custom-select" value={provider.type} onChange={(event) => setProvider(index, 'type', event.target.value)}>
                    <option value="openai">OpenAI-compatible</option>
                    <option value="anthropic">Anthropic</option>
                    <option value="gemini">Gemini</option>
                  </select>
                </div>
                <div className="col-md-3 form-group">
                  <label>Base URL <span className="text-muted">(blank = official)</span></label>
                  <input className="form-control" placeholder="https://…" value={provider.baseUrl ?? ''} onChange={(event) => setProvider(index, 'baseUrl', event.target.value)} />
                </div>
                <div className="col-md-2 form-group">
                  <label>Model</label>
                  <input className="form-control" placeholder="model id" value={provider.model} onChange={(event) => setProvider(index, 'model', event.target.value)} />
                </div>
                <div className="col-md-2 form-group">
                  <label>API key {provider.hasKey && <span className="text-success">(saved)</span>}</label>
                  <input className="form-control" type="password" placeholder={provider.hasKey ? '•••••••• (keep)' : 'sk-…'} value={provider.apiKey ?? ''} onChange={(event) => setProvider(index, 'apiKey', event.target.value)} />
                </div>
              </div>
              <div className="d-flex align-items-center justify-content-between flex-wrap" style={{ gap: 8 }}>
                <small className="text-muted">{TYPE_HELP[provider.type]}</small>
                <span className="d-inline-flex align-items-center" style={{ gap: 10 }}>
                  <label className="d-flex align-items-center mb-0" style={{ gap: 8, fontWeight: 400, cursor: 'pointer' }}>
                    <span className="kh-switch">
                      <input type="checkbox" checked={provider.enabled} onChange={(event) => setProvider(index, 'enabled', event.target.checked)} />
                      <span className="kh-slider" />
                    </span>
                    Enabled
                  </label>
                  <button type="button" className="btn btn-sm btn-outline-secondary" disabled={Boolean(busy)} onClick={() => test(provider)}>
                    {busy === `test-${provider.id}` ? 'Testing…' : 'Test connection'}
                  </button>
                </span>
              </div>
            </AdminCard>
          ))}
          <p className="text-muted medium">Unsaved key fields showing dots keep the stored key. Save after testing.</p>
        </>
      )}
    </AdminShell>
  );
};
