import { useEffect, useState } from 'react';
import { apiFetch } from '../../core/api.js';
import { useSite } from '../../core/site-context.jsx';
import { AdminShell, AdminCard } from './AdminShell.jsx';
import { Flash } from '../../core/ui.jsx';

/** Theme switching is managed here: list, activate, upload (zip), delete, guidelines. */
export const ThemesPage = () => {
  const { refresh } = useSite();
  const [data, setData] = useState(null);
  const [guidelines, setGuidelines] = useState('');
  const [showGuidelines, setShowGuidelines] = useState(false);
  const [message, setMessage] = useState(null);
  const [busy, setBusy] = useState('');

  const load = () => apiFetch('/api/admin/themes').then(setData).catch((err) => setMessage({ error: err.message }));
  useEffect(() => { load(); }, []);

  const act = async (label, run) => {
    setBusy(label);
    setMessage(null);
    try {
      await run();
      await load();
      await refresh();
    } catch (err) {
      setMessage({ error: err.message });
    } finally {
      setBusy('');
    }
  };

  const uploadZip = (file) => act('upload', async () => {
    const body = new FormData();
    body.append('theme', file);
    const response = await fetch('/api/admin/themes/upload', { method: 'POST', body, credentials: 'same-origin' });
    const payload = await response.json().catch(() => ({}));
    if (!response.ok) throw new Error(payload.error ?? `Upload failed (${response.status})`);
    setMessage({ ok: `Theme "${payload.theme.name}" installed. Activate it below.` });
  });

  const openGuidelines = async () => {
    if (!guidelines) {
      const payload = await apiFetch('/api/admin/themes/guidelines');
      setGuidelines(payload.markdown);
    }
    setShowGuidelines((value) => !value);
  };

  return (
    <AdminShell
      title="Themes"
      actions={
        <span className="d-inline-flex" style={{ gap: 8 }}>
          <button type="button" className="btn btn-sm btn-outline-secondary bg-white" onClick={openGuidelines}>
            <i className="fa fa-book mr-1" aria-hidden="true" /> Theme guidelines
          </button>
          <label className="btn btn-sm theme-bg text-white mb-0" style={{ cursor: 'pointer' }}>
            <i className="fa fa-upload mr-1" aria-hidden="true" /> {busy === 'upload' ? 'Uploading…' : 'Upload theme (.zip)'}
            <input type="file" accept=".zip" hidden disabled={Boolean(busy)}
              onChange={(event) => { if (event.target.files?.[0]) uploadZip(event.target.files[0]); event.target.value = ''; }} />
          </label>
        </span>
      }
    >
      <Flash message={message} />

      {showGuidelines && (
        <AdminCard title="Theme guidelines">
          <pre style={{ whiteSpace: 'pre-wrap', fontFamily: 'inherit', fontSize: '0.85rem', maxHeight: 420, overflow: 'auto' }}>{guidelines}</pre>
        </AdminCard>
      )}

      {!data && !message?.error && <p role="status">Loading…</p>}
      <div className="row">
        {data?.themes.map((theme) => {
          const active = data.active === theme.slug;
          return (
            <div className="col-lg-4 col-md-6 col-12 mb-3" key={theme.slug}>
              <div className={`bg-white rounded border p-3 h-100 ${active ? 'border-success' : ''}`} style={active ? { borderWidth: 2 } : undefined}>
                <div className="d-flex align-items-center justify-content-between">
                  <h2 className="ft-bold mb-0" style={{ fontSize: '1.02rem' }}>{theme.name}</h2>
                  {active && <span className="badge theme-bg text-white" style={{ padding: '4px 8px' }}>Active</span>}
                </div>
                <p className="text-muted medium mb-1">v{theme.version}{theme.author ? ` · ${theme.author}` : ''}{theme.builtIn ? ' · built-in' : ''}</p>
                {theme.description && <p className="medium mb-2">{theme.description}</p>}
                {Object.keys(theme.tokens ?? {}).length > 0 && (
                  <p className="mb-2 d-flex" style={{ gap: 6 }}>
                    {['primaryColor', 'secondaryColor'].filter((key) => theme.tokens[key]).map((key) => (
                      <span key={key} title={`${key}: ${theme.tokens[key]}`} style={{ width: 22, height: 22, borderRadius: 4, background: theme.tokens[key], border: '1px solid #e2e8f0', display: 'inline-block' }} />
                    ))}
                  </p>
                )}
                <div className="d-flex" style={{ gap: 8 }}>
                  {!active && (
                    <button type="button" className="btn btn-sm theme-bg text-white" disabled={Boolean(busy)}
                      onClick={() => act(theme.slug, () => apiFetch(`/api/admin/themes/${theme.slug}/activate`, { method: 'POST' }))}>
                      Activate
                    </button>
                  )}
                  {!theme.builtIn && !active && (
                    <button type="button" className="btn btn-sm btn-outline-danger" disabled={Boolean(busy)}
                      onClick={() => window.confirm(`Delete theme "${theme.name}"?`) && act(`del-${theme.slug}`, () => apiFetch(`/api/admin/themes/${theme.slug}`, { method: 'DELETE' }))}>
                      Delete
                    </button>
                  )}
                </div>
              </div>
            </div>
          );
        })}
      </div>
    </AdminShell>
  );
};
