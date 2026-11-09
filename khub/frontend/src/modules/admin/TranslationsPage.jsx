import { useEffect, useMemo, useState } from 'react';
import { apiFetch } from '../../core/api.js';
import { useSite, usePermission } from '../../core/site-context.jsx';
import { useI18n } from '../../core/i18n.jsx';
import { AdminShell, AdminCard } from './AdminShell.jsx';
import { DataTable } from '../../core/table.jsx';
import { SearchSelect } from '../../core/SearchSelect.jsx';
import { Modal, FormField, Flash, StatusBadge, StatCard } from '../../core/ui.jsx';

/**
 * Language Management — every UI string per language in one table, edited in a
 * modal, with one-click AI translation of everything that is still missing.
 * English is the source; saved values override the shipped files instantly.
 */
export const TranslationsPage = () => {
  const canEdit = usePermission('admin.languages.manage');
  const { languages } = useI18n();
  const { manifest } = useSite();
  const targets = useMemo(() => languages.filter((lang) => lang.code !== 'en'), [languages]);
  const [locale, setLocale] = useState('');
  const [data, setData] = useState(null);
  const [filter, setFilter] = useState('');
  const [onlyMissing, setOnlyMissing] = useState(false);
  const [editing, setEditing] = useState(null);
  const [message, setMessage] = useState(null);
  const [aiBusy, setAiBusy] = useState(false);

  useEffect(() => { if (!locale && targets.length > 0) setLocale(targets[0].code); }, [targets, locale]);

  const load = (code = locale) => code && apiFetch('/api/admin/translations', { query: { locale: code } })
    .then(setData).catch((err) => setMessage({ error: err.message }));
  useEffect(() => { load(); }, [locale]); // eslint-disable-line

  const rows = useMemo(() => {
    if (!data) return [];
    const clean = filter.trim().toLowerCase();
    return data.rows.filter((row) =>
      (!onlyMissing || !row.value)
      && (!clean || `${row.key} ${row.source} ${row.value}`.toLowerCase().includes(clean)));
  }, [data, filter, onlyMissing]);

  const aiFill = async () => {
    if (!window.confirm(`Use AI to translate all missing ${languageName(targets, locale)} strings? Existing translations are never overwritten.`)) return;
    setAiBusy(true);
    setMessage(null);
    try {
      const result = await apiFetch('/api/admin/translations/ai-fill', { method: 'POST', body: { locale, limit: 200 } });
      setMessage(result.translated > 0
        ? { ok: `AI translated ${result.translated} string(s) via ${result.provider}. ${result.remaining} still missing.` }
        : { ok: 'Nothing was missing — all strings are already translated.' });
      await load();
    } catch (err) {
      setMessage({ error: err.message });
    } finally {
      setAiBusy(false);
    }
  };

  return (
    <AdminShell title="Language management"
      subtitle="Edit any interface string per language. English is the source; changes apply to the live site immediately."
      actions={canEdit && locale && (
        <button type="button" className="btn btn-sm theme-bg text-white" disabled={aiBusy || !data || data.missing === 0} onClick={aiFill}>
          <i className={`fa ${aiBusy ? 'fa-spinner fa-spin' : 'fa-wand-magic-sparkles'} mr-1`} aria-hidden="true" />
          {aiBusy ? 'Translating…' : `AI translate missing (${data?.missing ?? 0})`}
        </button>
      )}>
      <Flash message={message} />
      {data && (
        <div className="row">
          <div className="col-md-3 col-6 mb-3"><StatCard value={data.total} label="Strings" icon="fa-list" /></div>
          <div className="col-md-3 col-6 mb-3"><StatCard value={data.total - data.missing} label="Translated" icon="fa-circle-check" /></div>
          <div className="col-md-3 col-6 mb-3"><StatCard value={data.missing} label="Missing" icon="fa-triangle-exclamation" /></div>
          <div className="col-md-3 col-6 mb-3"><StatCard value={data.overridden} label="Edited here" icon="fa-pen" /></div>
        </div>
      )}
      <DataTable
        title={`Strings — ${languageName(targets, locale)}`}
        description="Values saved here override the shipped translation files."
        rows={rows}
        rowKey={(row) => row.key}
        showRowNumbers
        clientPerPage={15}
        emptyMessage={data ? 'No strings match this filter.' : 'Loading…'}
        toolbar={(
          <>
            <div style={{ width: 170 }}>
              <SearchSelect options={targets.map((lang) => ({ id: lang.code, name: `${lang.flag} ${lang.name}` }))}
                value={locale} placeholder="Language" onChange={(next) => { setLocale(next); setData(null); }} />
            </div>
            <input className="form-control form-control-sm" style={{ width: 200 }} placeholder="Filter strings…"
              value={filter} onChange={(event) => setFilter(event.target.value)} aria-label="Filter strings" />
            <label className="mb-0 d-flex align-items-center" style={{ gap: 6, cursor: 'pointer', fontWeight: 500, fontSize: '0.82rem' }}>
              <input type="checkbox" checked={onlyMissing} onChange={(event) => setOnlyMissing(event.target.checked)} /> Missing only
            </label>
          </>
        )}
        columns={[
          { key: 'key', label: 'Key', render: (row) => <code style={{ fontSize: '0.76rem' }}>{row.key}</code> },
          { key: 'source', label: 'English (source)', render: (row) => <span style={{ display: 'inline-block', maxWidth: 320 }}>{row.source}</span> },
          {
            key: 'value',
            label: languageName(targets, locale),
            render: (row) => row.value
              ? <span style={{ display: 'inline-block', maxWidth: 320, color: '#18181b' }}>{row.value}</span>
              : <StatusBadge tone="warning">missing</StatusBadge>,
          },
          { key: 'state', label: 'Source', align: 'center', render: (row) => <StatusBadge tone={row.overridden ? 'success' : 'neutral'}>{row.overridden ? 'edited' : 'file'}</StatusBadge> },
          ...(canEdit ? [{
            key: 'actions', label: 'Actions', align: 'end',
            render: (row) => (
              <button type="button" className="btn btn-sm btn-outline-secondary" onClick={() => setEditing(row)}>
                <i className="fa fa-pen" aria-hidden="true" /> Edit
              </button>
            ),
          }] : []),
        ]}
      />
      {!data && !message?.error && <AdminCard><p role="status" className="mb-0">Loading…</p></AdminCard>}
      {editing && (
        <TranslationModal row={editing} locale={locale} language={languageName(targets, locale)}
          onClose={() => setEditing(null)}
          onSaved={async (ok) => { setEditing(null); setMessage({ ok }); await load(); }} />
      )}
    </AdminShell>
  );
};

const languageName = (targets, code) => targets.find((lang) => lang.code === code)?.name ?? code;

/** Individual edit form for one string. Clearing the text reverts to the file value. */
const TranslationModal = ({ row, locale, language, onClose, onSaved }) => {
  const [text, setText] = useState(row.overridden ? row.value : row.value);
  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);

  const save = async () => {
    setBusy(true);
    setError('');
    try {
      await apiFetch('/api/admin/translations', { method: 'PUT', body: { locale, key: row.key, text: text.trim() } });
      onSaved(`"${row.key}" ${text.trim() ? 'saved' : 'reverted to the file value'}.`);
    } catch (err) {
      setError(err.message);
      setBusy(false);
    }
  };

  return (
    <Modal title={`Translate: ${row.key}`} icon="fa-language" onClose={onClose}
      footer={(
        <>
          <button type="button" className="btn btn-sm btn-outline-secondary" onClick={onClose}>Cancel</button>
          <button type="button" className="btn btn-sm theme-bg text-white" disabled={busy} onClick={save}>
            {busy ? 'Saving…' : 'Save translation'}
          </button>
        </>
      )}>
      {error && <div role="alert" className="alert alert-danger">{error}</div>}
      <FormField label="English (source)">
        <div className="form-control" style={{ background: '#fafafa', minHeight: 38 }}>{row.source}</div>
      </FormField>
      <FormField label={language} hint="Keep placeholders like :name untouched. Leave empty to revert to the shipped file value.">
        <textarea className="form-control" rows={3} value={text} autoFocus onChange={(event) => setText(event.target.value)} />
      </FormField>
    </Modal>
  );
};
