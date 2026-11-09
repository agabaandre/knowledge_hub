import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { apiFetch } from '../../core/api.js';
import { useSite, usePermission } from '../../core/site-context.jsx';
import { withSitePrefix } from '@khub/shared/urls';
import { AdminShell, AdminCard } from './AdminShell.jsx';
import { DataTable } from '../../core/table.jsx';
import { Modal, FormField, Flash, StatusBadge } from '../../core/ui.jsx';
import { formatDate, formatBytes } from '../../core/format.js';

const KINDS = [['all', 'All'], ['primary', 'Primary files'], ['attachment', 'Attachments']];
const PREVIEWABLE = /pdf|image\/|text\/|\.pdf$|\.png$|\.jpe?g$|\.gif$|\.webp$|\.txt$/i;

/**
 * Document management: searchable, paginated registry with preview, SHA-256
 * integrity verification, duplicate detection (merge / make version / trash),
 * a recoverable trash bin, and the automated retention + trash-clean sweep.
 */
export const DocumentsPage = () => {
  const { manifest } = useSite();
  const canManage = usePermission('admin.documents.manage');
  const to = (path) => withSitePrefix(manifest.site.pathPrefix, path);
  const [filters, setFilters] = useState({ term: '', kind: 'all', view: 'active', page: 1 });
  const [payload, setPayload] = useState(null);
  const [duplicates, setDuplicates] = useState(null);
  const [editing, setEditing] = useState(null);
  const [preview, setPreview] = useState(null);
  const [message, setMessage] = useState(null);
  const [busy, setBusy] = useState(false);

  const load = (next = filters) => Promise.all([
    apiFetch('/api/admin/documents', { query: { term: next.term.trim(), kind: next.kind, view: next.view, page: next.page } }).then(setPayload),
    apiFetch('/api/admin/documents/duplicates').then(setDuplicates),
  ]).catch((err) => setMessage({ error: err.message }));
  useEffect(() => { load(); }, []); // eslint-disable-line

  const apply = (patch) => {
    const next = { ...filters, page: 1, ...patch };
    setFilters(next);
    load(next);
  };

  const act = async (run, ok) => {
    setBusy(true);
    setMessage(null);
    try {
      const result = await run();
      await load();
      if (ok) setMessage({ ok: typeof ok === 'function' ? ok(result) : ok });
    } catch (err) {
      setMessage({ error: err.message });
    } finally {
      setBusy(false);
    }
  };

  const verify = (doc) => act(
    () => apiFetch(doc.kind === 'primary' ? `/api/admin/documents/primary/${doc.id}/verify` : `/api/admin/documents/${doc.id}/verify`, { method: 'POST' }),
    (result) => (result.state === 'verified' ? `“${doc.name}” verified — checksum matches.`
      : result.state === 'registered' ? `“${doc.name}” registered — checksum recorded.`
        : result.message),
  );
  const trash = (doc) => act(() => apiFetch(`/api/admin/documents/${doc.id}`, { method: 'DELETE' }), `“${doc.name}” moved to the trash.`);
  const restore = (doc) => act(() => apiFetch(`/api/admin/documents/${doc.id}/restore`, { method: 'POST' }), `“${doc.name}” restored.`);
  const destroy = (doc) => window.confirm(`Permanently destroy "${doc.name}" and its stored file? This cannot be undone.`)
    && act(() => apiFetch(`/api/admin/documents/${doc.id}/destroy`, { method: 'DELETE' }), 'Document destroyed.');
  const sweep = () => act(
    () => apiFetch('/api/admin/documents/sweep', { method: 'POST' }),
    (result) => `Sweep: ${result.registered} checksum(s) registered, ${result.disposed} expired disposed, ${result.emptied ?? 0} trashed item(s) auto-cleaned (policy: ${result.trashDays ?? 30} days).`,
  );

  const retentionBadge = (doc) => {
    if (doc.kind === 'primary') return <StatusBadge tone="neutral">follows resource</StatusBadge>;
    if (!doc.retentionUntil) return <StatusBadge tone="neutral">keep</StatusBadge>;
    const expired = new Date(doc.retentionUntil) <= new Date();
    return <StatusBadge tone={expired ? 'danger' : 'warning'}>{expired ? 'expired' : formatDate(doc.retentionUntil)}</StatusBadge>;
  };

  const duplicateCount = (duplicates?.checksumGroups.length ?? 0) + (duplicates?.titleGroups.length ?? 0);

  return (
    <AdminShell title="Document management"
      subtitle="Every stored file: integrity checksums, previews, duplicates, retention schedules, and a recoverable trash bin."
      actions={canManage && (
        <button type="button" className="btn btn-sm btn-outline-secondary" disabled={busy} onClick={sweep}>
          <i className="fa fa-broom mr-1" aria-hidden="true" /> Run retention sweep
        </button>
      )}>
      <Flash message={message} />

      {duplicateCount > 0 && filters.view === 'active' && (
        <DuplicatesPanel duplicates={duplicates} canManage={canManage} busy={busy} to={to}
          onAction={(run, ok) => act(run, ok)} />
      )}

      {!payload && !message?.error && <AdminCard><p role="status" className="mb-0">Loading…</p></AdminCard>}
      {payload && (
        <DataTable
          title={filters.view === 'trash' ? 'Trash' : 'Document registry'}
          description={filters.view === 'trash'
            ? 'Trashed documents are destroyed automatically after the retention policy (Configure → Advanced → trash retention days).'
            : 'Documents with a passed “delete after” date are disposed of automatically (twice a day and at start-up).'}
          rows={payload.data}
          rowKey={(doc) => `${doc.kind}-${doc.id}`}
          showRowNumbers
          page={payload.meta.page}
          perPage={payload.meta.perPage}
          totalPages={payload.meta.lastPage}
          total={payload.meta.total}
          onPageChange={(page) => { const next = { ...filters, page }; setFilters(next); load(next); }}
          emptyMessage={filters.view === 'trash' ? 'The trash is empty.' : filters.term ? `No documents match “${filters.term}”.` : 'No documents registered yet.'}
          emptyIcon={filters.view === 'trash' ? 'fa-trash-can' : 'fa-database'}
          toolbar={(
            <>
              <form className="d-flex" style={{ gap: 6 }} onSubmit={(event) => { event.preventDefault(); apply({}); }}>
                <input className="form-control form-control-sm" style={{ width: 190 }} type="search" placeholder="Search documents…"
                  value={filters.term} onChange={(event) => setFilters({ ...filters, term: event.target.value })} aria-label="Search documents" />
                <button type="submit" className="btn btn-sm theme-bg text-white"><i className="fa fa-magnifying-glass" aria-hidden="true" /></button>
              </form>
              <span className="d-inline-flex" style={{ gap: 4 }}>
                {KINDS.map(([value, label]) => (
                  <button key={value} type="button" onClick={() => apply({ kind: value })}
                    className={`btn btn-sm ${filters.kind === value ? 'theme-bg text-white' : 'btn-outline-secondary'}`}>{label}</button>
                ))}
                <button type="button" onClick={() => apply({ view: filters.view === 'trash' ? 'active' : 'trash' })}
                  className={`btn btn-sm ${filters.view === 'trash' ? 'btn-outline-danger' : 'btn-outline-secondary'}`}>
                  <i className="fa fa-trash-can mr-1" aria-hidden="true" /> Trash
                </button>
              </span>
              <a className="btn btn-sm btn-outline-secondary"
                href={`/api/admin/documents/export?kind=${filters.kind}&view=${filters.view}${filters.term.trim() ? `&term=${encodeURIComponent(filters.term.trim())}` : ''}`} download>
                <i className="fa fa-file-csv mr-1" aria-hidden="true" /> Export
              </a>
            </>
          )}
          columns={[
            {
              key: 'name',
              label: 'Document',
              render: (doc) => (
                <span style={{ display: 'inline-block', maxWidth: 240 }}>
                  <button type="button" className="btn btn-link p-0 text-left ft-medium" style={{ color: '#18181b', fontSize: '0.875rem', textDecoration: 'none' }}
                    onClick={() => setPreview(doc)} title="Preview">
                    <i className={`fa ${doc.external ? 'fa-link' : 'fa-file'} mr-1 theme-cl`} aria-hidden="true" />{doc.name}
                  </button>
                  <small className="d-block text-muted">
                    <StatusBadge tone={doc.kind === 'primary' ? 'success' : 'neutral'}>{doc.kind === 'primary' ? 'primary file' : 'attachment'}</StatusBadge>
                    {doc.mimeType && <span className="ml-1">{doc.mimeType}</span>}
                  </small>
                </span>
              ),
            },
            {
              key: 'publication',
              label: 'Resource',
              render: (doc) => doc.publication
                ? <Link to={to(`/admin/publications/details?id=${doc.publication.id}`)}>{doc.publication.title}</Link>
                : <span className="text-muted">—</span>,
            },
            { key: 'size', label: 'Size', align: 'center', nowrap: true, render: (doc) => (doc.sizeBytes === null ? <span className="text-muted">—</span> : formatBytes(doc.sizeBytes)) },
            {
              key: 'checksum',
              label: 'SHA-256',
              render: (doc) => doc.checksum
                ? <code title={doc.checksum} style={{ fontSize: '0.74rem', cursor: 'help' }}>{doc.checksum.slice(0, 12)}…</code>
                : <StatusBadge tone="warning">unregistered</StatusBadge>,
            },
            { key: 'version', label: 'Ver.', align: 'center', render: (doc) => `v${doc.docVersion}` },
            {
              key: 'retention',
              label: filters.view === 'trash' ? 'Trashed' : 'Delete after',
              align: 'center',
              render: (doc) => (filters.view === 'trash' ? formatDate(doc.trashedAt) : retentionBadge(doc)),
            },
            {
              key: 'actions',
              label: 'Actions',
              align: 'end',
              render: (doc) => (
                <span className="kh-table-actions">
                  <button type="button" className="btn btn-sm btn-outline-secondary" onClick={() => setPreview(doc)} aria-label={`Preview ${doc.name}`}>
                    <i className="fa fa-eye" aria-hidden="true" />
                  </button>
                  {canManage && filters.view === 'active' && (
                    <>
                      {!doc.external && (
                        <button type="button" className="btn btn-sm btn-outline-secondary" disabled={busy} onClick={() => verify(doc)} title="Verify integrity (SHA-256)">
                          <i className="fa fa-shield-halved" aria-hidden="true" />
                        </button>
                      )}
                      {doc.kind !== 'primary' && (
                        <>
                          <button type="button" className="btn btn-sm btn-outline-secondary" disabled={busy} onClick={() => setEditing(doc)} aria-label={`Edit ${doc.name}`}>
                            <i className="fa fa-pen" aria-hidden="true" />
                          </button>
                          <button type="button" className="btn btn-sm btn-outline-danger" disabled={busy} onClick={() => trash(doc)} title="Move to trash">
                            <i className="fa fa-trash" aria-hidden="true" />
                          </button>
                        </>
                      )}
                    </>
                  )}
                  {canManage && filters.view === 'trash' && doc.kind !== 'primary' && (
                    <>
                      <button type="button" className="btn btn-sm btn-outline-secondary" disabled={busy} onClick={() => restore(doc)}>
                        <i className="fa fa-rotate-left mr-1" aria-hidden="true" /> Restore
                      </button>
                      <button type="button" className="btn btn-sm btn-outline-danger" disabled={busy} onClick={() => destroy(doc)}>
                        <i className="fa fa-fire" aria-hidden="true" /> Destroy
                      </button>
                    </>
                  )}
                </span>
              ),
            },
          ]}
        />
      )}
      {editing && (
        <DocumentModal doc={editing} onClose={() => setEditing(null)}
          onSaved={async (ok) => { setEditing(null); setMessage({ ok }); await load(); }} />
      )}
      {preview && <PreviewModal doc={preview} onClose={() => setPreview(null)} />}
    </AdminShell>
  );
};

/** Duplicate warning: same checksum (identical content) or same title, with resolution powers. */
const DuplicatesPanel = ({ duplicates, canManage, busy, to, onAction }) => {
  const makeVersion = (pubId, parentId) => onAction(
    () => apiFetch(`/api/admin/publications/${pubId}/make-version`, { method: 'POST', body: { parent_id: parentId } }),
    (result) => `Publication #${pubId} is now version ${result.versionNo} of #${result.parentId}.`,
  );
  const merge = (pubId, targetId) => window.confirm(`Merge publication #${pubId} into #${targetId}? Its comments, attachments, favourites and views move over, then it goes to the trash.`)
    && onAction(() => apiFetch(`/api/admin/publications/${pubId}/merge-into`, { method: 'POST', body: { target_id: targetId } }), `Merged #${pubId} into #${targetId}.`);
  const trashPub = (pubId) => onAction(() => apiFetch(`/api/admin/publications/${pubId}`, { method: 'DELETE' }), `Publication #${pubId} moved to the trash.`);

  const Row = ({ label, entries }) => {
    const keeper = entries[0];
    return (
      <div className="py-2" style={{ borderBottom: '1px solid #f3e8e8' }}>
        <p className="mb-1"><StatusBadge tone="warning">duplicate</StatusBadge> <span className="ft-medium">{label}</span></p>
        {entries.map((entry, index) => (
          <div key={entry.pubId} className="d-flex align-items-center flex-wrap mb-1" style={{ gap: 8, marginInlineStart: 14 }}>
            <span style={{ minWidth: 0 }}>
              #{entry.pubId} — <Link to={to(`/admin/publications/details?id=${entry.pubId}`)}>{entry.title}</Link>
              {index === 0 && <StatusBadge tone="success">keep</StatusBadge>}
            </span>
            {canManage && index > 0 && (
              <span className="d-inline-flex" style={{ gap: 5 }}>
                <button type="button" className="btn btn-sm btn-outline-secondary" disabled={busy} onClick={() => makeVersion(entry.pubId, keeper.pubId)}>
                  <i className="fa fa-code-branch mr-1" aria-hidden="true" /> Make version of #{keeper.pubId}
                </button>
                <button type="button" className="btn btn-sm btn-outline-secondary" disabled={busy} onClick={() => merge(entry.pubId, keeper.pubId)}>
                  <i className="fa fa-object-group mr-1" aria-hidden="true" /> Merge into #{keeper.pubId}
                </button>
                <button type="button" className="btn btn-sm btn-outline-danger" disabled={busy} onClick={() => trashPub(entry.pubId)}>
                  <i className="fa fa-trash mr-1" aria-hidden="true" /> Trash
                </button>
              </span>
            )}
          </div>
        ))}
      </div>
    );
  };

  return (
    <AdminCard title={`⚠ Possible duplicates (${duplicates.checksumGroups.length + duplicates.titleGroups.length})`}
      className="" >
      <p className="text-muted mb-2" style={{ fontSize: '0.84rem' }}>
        Same file content (SHA-256) or identical titles. Keep the first entry and resolve the rest: turn them into versions, merge their content across, or trash them.
      </p>
      {duplicates.checksumGroups.map((group) => (
        <Row key={group.key}
          label={`Identical file content (${group.key.slice(0, 12)}…)`}
          entries={dedupeByPub(group.documents.map((doc) => ({ pubId: doc.publication?.id, title: doc.publication?.title ?? doc.name })))} />
      ))}
      {duplicates.titleGroups.map((group) => (
        <Row key={`t-${group.key}`}
          label={`Identical title: “${group.key}”`}
          entries={group.publications.map((pub) => ({ pubId: pub.id, title: pub.title }))} />
      ))}
    </AdminCard>
  );
};

const dedupeByPub = (entries) => {
  const seen = new Set();
  return entries.filter((entry) => {
    if (!entry.pubId || seen.has(entry.pubId)) return false;
    seen.add(entry.pubId);
    return true;
  });
};

/** In-admin preview: PDFs/images/text render inline; anything else offers the download. */
const PreviewModal = ({ doc, onClose }) => {
  const canEmbed = !doc.external && PREVIEWABLE.test(`${doc.mimeType ?? ''} ${doc.file}`);
  return (
    <Modal wide title={doc.name} icon="fa-eye" onClose={onClose}
      footer={(
        <>
          <span className="d-inline-flex align-items-center" style={{ gap: 8, marginInlineEnd: 'auto' }}>
            {doc.checksum && <small className="text-muted">SHA-256 <code style={{ fontSize: '0.68rem' }}>{doc.checksum.slice(0, 16)}…</code></small>}
            {doc.sizeBytes !== null && <small className="text-muted">{formatBytes(doc.sizeBytes)}</small>}
          </span>
          <a className="btn btn-sm btn-outline-secondary" href={doc.url} target="_blank" rel="noopener noreferrer">
            <i className="fa fa-download mr-1" aria-hidden="true" /> Open / download
          </a>
          <button type="button" className="btn btn-sm btn-outline-secondary" onClick={onClose}>Close</button>
        </>
      )}>
      {canEmbed
        ? <iframe className="kh-admin-preview" src={doc.url} title={`Preview of ${doc.name}`} />
        : (
          <div className="kh-admin-empty">
            <i className={`fa ${doc.external ? 'fa-link' : 'fa-file'}`} aria-hidden="true" />
            <p className="mb-0 ft-medium" style={{ color: '#475569' }}>
              {doc.external ? 'External link — open it in a new tab.' : 'No inline preview for this type — use Open / download.'}
            </p>
          </div>
        )}
    </Modal>
  );
};

/** Individual form: description + the retention ("delete after") date. */
const DocumentModal = ({ doc, onClose, onSaved }) => {
  const [draft, setDraft] = useState({ description: doc.description ?? '', retention_until: doc.retentionUntil ? String(doc.retentionUntil).slice(0, 10) : '' });
  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);

  const save = async () => {
    setBusy(true);
    setError('');
    try {
      await apiFetch(`/api/admin/documents/${doc.id}`, {
        method: 'PUT',
        body: { description: draft.description, retention_until: draft.retention_until || null },
      });
      onSaved(`“${doc.name}” updated${draft.retention_until ? ` — scheduled for disposal after ${draft.retention_until}.` : '.'}`);
    } catch (err) {
      setError(err.message);
      setBusy(false);
    }
  };

  return (
    <Modal title={`Edit document: ${doc.name}`} icon="fa-folder-open" onClose={onClose}
      footer={(
        <>
          <button type="button" className="btn btn-sm btn-outline-secondary" onClick={onClose}>Cancel</button>
          <button type="button" className="btn btn-sm theme-bg text-white" disabled={busy} onClick={save}>
            {busy ? 'Saving…' : 'Save'}
          </button>
        </>
      )}>
      {error && <div role="alert" className="alert alert-danger">{error}</div>}
      <FormField label="Description">
        <input className="form-control" value={draft.description} onChange={(event) => setDraft({ ...draft, description: event.target.value })} />
      </FormField>
      <FormField label="Delete after" hint="The automated retention sweep disposes of the file and its registry entry once this date passes. Clear the date to keep the document indefinitely.">
        <input type="date" className="form-control" value={draft.retention_until}
          onChange={(event) => setDraft({ ...draft, retention_until: event.target.value })} />
      </FormField>
      {doc.checksum && <p className="mb-0"><small className="text-muted">SHA-256: <code style={{ fontSize: '0.7rem' }}>{doc.checksum}</code></small></p>}
    </Modal>
  );
};
