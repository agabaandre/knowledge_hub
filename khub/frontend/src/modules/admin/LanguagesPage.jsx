import { useEffect, useState } from 'react';
import { apiFetch } from '../../core/api.js';
import { useSite } from '../../core/site-context.jsx';
import { AdminShell, AdminCard } from './AdminShell.jsx';
import { Flash } from '../../core/ui.jsx';

const BLANK = { locale_code: '', name: '', flag_emoji: '', is_active: true };

/** Site languages: the switcher's source (site_languages). Order = display order. */
export const LanguagesPage = () => {
  const { refresh } = useSite();
  const [languages, setLanguages] = useState(null);
  const [message, setMessage] = useState(null);
  const [busy, setBusy] = useState(false);

  useEffect(() => {
    apiFetch('/api/admin/languages').then((payload) => setLanguages(payload.languages)).catch((err) => setMessage({ error: err.message }));
  }, []);

  const set = (index, key, value) => setLanguages((previous) => previous.map((language, i) => (i === index ? { ...language, [key]: value } : language)));
  const move = (index, delta) =>
    setLanguages((previous) => {
      const next = [...previous];
      const target = index + delta;
      if (target < 0 || target >= next.length) return previous;
      [next[index], next[target]] = [next[target], next[index]];
      return next;
    });

  const save = async () => {
    setBusy(true);
    setMessage(null);
    try {
      const payload = await apiFetch('/api/admin/languages', { method: 'PUT', body: { languages } });
      setLanguages(payload.languages);
      await refresh();
      setMessage({ ok: 'Languages saved. The switcher updates immediately.' });
    } catch (err) {
      setMessage({ error: err.details ? err.details.map((d) => `${d.path}: ${d.message}`).join('; ') : err.message });
    } finally {
      setBusy(false);
    }
  };

  return (
    <AdminShell
      title="Site languages"
      actions={
        <span className="d-inline-flex" style={{ gap: 8 }}>
          <button type="button" className="btn btn-sm btn-outline-secondary bg-white" disabled={!languages} onClick={() => setLanguages((previous) => [...previous, { ...BLANK }])}>
            <i className="fa fa-plus mr-1" aria-hidden="true" /> Add language
          </button>
          <button type="button" className="btn btn-sm theme-bg text-white" onClick={save} disabled={!languages || busy}>{busy ? 'Saving…' : 'Save languages'}</button>
        </span>
      }
    >
      <Flash message={message} />
      {!languages && !message?.error && <p role="status">Loading…</p>}
      {languages && (
        <AdminCard>
          <p className="text-muted">Translations ship for the six AU official languages (en, fr, ar, es, pt, sw); other locales fall back to English until dictionaries are added.</p>
          {languages.map((language, index) => (
            <div className="row align-items-end pb-2 mb-2" style={{ borderBottom: '1px solid #f1f5f9' }} key={language.id ?? `new-${index}`}>
              <div className="col-md-2 form-group mb-0">
                <label>Code</label>
                <input className="form-control" value={language.locale_code} placeholder="fr" maxLength={5} onChange={(event) => set(index, 'locale_code', event.target.value.toLowerCase())} />
              </div>
              <div className="col-md-4 form-group mb-0">
                <label>Name</label>
                <input className="form-control" value={language.name} placeholder="Français" onChange={(event) => set(index, 'name', event.target.value)} />
              </div>
              <div className="col-md-2 form-group mb-0">
                <label>Flag</label>
                <input className="form-control" value={language.flag_emoji} placeholder="🇫🇷" maxLength={8} onChange={(event) => set(index, 'flag_emoji', event.target.value)} />
              </div>
              <div className="col-md-4 form-group mb-0 d-flex align-items-center" style={{ gap: 12, paddingTop: 22 }}>
                <label className="mb-0 d-flex align-items-center" style={{ gap: 6, cursor: 'pointer', fontWeight: 400 }}>
                  <input type="checkbox" checked={language.is_active} onChange={(event) => set(index, 'is_active', event.target.checked)} /> Active
                </label>
                <button type="button" className="btn btn-sm btn-light" aria-label="Move up" onClick={() => move(index, -1)}><i className="fa fa-arrow-up" aria-hidden="true" /></button>
                <button type="button" className="btn btn-sm btn-light" aria-label="Move down" onClick={() => move(index, 1)}><i className="fa fa-arrow-down" aria-hidden="true" /></button>
                <button type="button" className="btn btn-sm btn-outline-danger" aria-label="Remove" onClick={() => setLanguages((previous) => previous.filter((_, i) => i !== index))}><i className="fa fa-trash" aria-hidden="true" /></button>
              </div>
            </div>
          ))}
        </AdminCard>
      )}
    </AdminShell>
  );
};
