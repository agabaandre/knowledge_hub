import { useEffect, useMemo, useState } from 'react';
import { Link } from 'react-router-dom';
import { apiFetch } from '../../core/api.js';
import { useSite, usePermission } from '../../core/site-context.jsx';
import { withSitePrefix } from '@khub/shared/urls';
import { AdminShell, AdminCard } from './AdminShell.jsx';
import { DataTable } from '../../core/table.jsx';
import { Modal, FormField, Flash, StatusBadge } from '../../core/ui.jsx';
import { formatDate, formatBytes } from '../../core/format.js';

/**
 * Document management: the registry of stored attachments with
 * industry-standard controls — SHA-256 integrity verification, document
 * versions, uploader audit, and a retention ("delete after") date enforced by
 * the automated disposition sweep.
 */
export const DocumentsPage = () => {
  const { manifest } = useSite();
  const canManage = usePermission('admin.documents.manage');
  const to = (path) => withSitePrefix(manifest.site.pathPrefix, path);
  const [documents, setDocuments] = useState(null);
  const [term, setTerm] = useState('');
  const [editing, setEditing] = useState(null);
  const [message, setMessage] = useState(null);
  const [busy, setBusy] = useState(false);

  const load = () => apiFetch('/api/admin/documents').then((payload) => setDocuments(payload.documents)).catch((err) => setMessage({ error: err.message }));
  useEffect(() => { load(); }, []);

  const filtered = useMemo(() => {
    if (!documents) return [];
    const clean = term.trim().toLowerCase();
    if (!clean) return documents;
    return documents.filter((doc) => `${doc.name} ${doc.publication?.title ?? ''} ${doc.mimeType ?? ''}`.toLowerCase().includes(clean));
  }, [documents, term]);

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
    () => apiFetch(`/api/admin/documents/${doc.id}/verify`, { method: 'POST' }),
    (result) => (result.state === 'verified' ? `“${doc.name}” verified — checksum matches.`
      : result.state === 'registered' ? `“${doc.name}” registered — checksum recorded.`
        : result.message),
  );
  const remove = (doc) => window.confirm(`Delete "${doc.name}" and its stored file? This cannot be undone.`)
    && act(() => apiFetch(`/api/admin/documents/${doc.id}`, { method: 'DELETE' }), 'Document disposed of.');
  const sweep = () => act(
    () => apiFetch('/api/admin/documents/sweep', { method: 'POST' }),
    (result) => `Sweep complete: ${result.registered} checksum(s) registered, ${result.disposed} expired document(s) disposed of.`,
  );

  const retentionBadge = (doc) => {
    if (!doc.retentionUntil) return <StatusBadge tone="neutral">keep</StatusBadge>;
    const expired = new Date(doc.retentionUntil) <= new Date();
    return <StatusBadge tone={expired ? 'danger' : 'warning'}>{expired ? 'expired' : formatDate(doc.retentionUntil)}</StatusBadge>;
  };

  return (
    <AdminShell title="Document management"
      subtitle="Every stored attachment: integrity checksums, versions, uploader audit, and retention schedules."
      actions={canManage && (
        <button type="button" className="btn btn-sm btn-outline-secondary" disabled={busy} onClick={sweep}>
          <i className="fa fa-broom mr-1" aria-hidden="true" /> Run retention sweep
        </button>
      )}>
      <Flash message={message} />
      {!documents && !message?.error && <AdminCard><p role="status" className="mb-0">Loading…</p></AdminCard>}
      {documents && (
        <DataTable
          title="Document registry"
          description="Documents whose “delete after” date has passed are disposed of automatically (twice a day and at start-up)."
          rows={filtered}
          showRowNumbers
          clientPerPage={12}
          emptyMessage={term ? `No documents match “${term}”.` : 'No documents registered yet.'}
          toolbar={(
            <>
              <input className="form-control form-control-sm" style={{ width: 200 }} type="search" placeholder="Search documents…"
                value={term} onChange={(event) => setTerm(event.target.value)} aria-label="Search documents" />
              <a className="btn btn-sm btn-outline-secondary" href="/api/admin/documents/export" download>
                <i className="fa fa-file-csv mr-1" aria-hidden="true" /> Export
              </a>
            </>
          )}
          columns={[
            {
              key: 'name',
              label: 'Document',
              render: (doc) => (
                <span style={{ display: 'inline-block', maxWidth: 260 }}>
                  <a href={doc.url} target="_blank" rel="noopener noreferrer" className="ft-medium" style={{ color: '#18181b' }}>
                    <i className={`fa ${doc.external ? 'fa-link' : 'fa-file'} mr-1 theme-cl`} aria-hidden="true" />{doc.name}
                  </a>
                  {doc.mimeType && <small className="d-block text-muted">{doc.mimeType}</small>}
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
            { key: 'uploader', label: 'Uploaded by', render: (doc) => doc.uploadedBy ?? <span className="text-muted">legacy</span> },
            { key: 'retention', label: 'Delete after', align: 'center', render: retentionBadge },
            ...(canManage ? [{
              key: 'actions',
              label: 'Actions',
              align: 'end',
              render: (doc) => (
                <span className="kh-table-actions">
                  {!doc.external && (
                    <button type="button" className="btn btn-sm btn-outline-secondary" disabled={busy} onClick={() => verify(doc)} title="Verify integrity (SHA-256)">
                      <i className="fa fa-shield-halved" aria-hidden="true" /> Verify
                    </button>
                  )}
                  <button type="button" className="btn btn-sm btn-outline-secondary" disabled={busy} onClick={() => setEditing(doc)}>
                    <i className="fa fa-pen" aria-hidden="true" /> Edit
                  </button>
                  <button type="button" className="btn btn-sm btn-outline-danger" disabled={busy} onClick={() => remove(doc)} aria-label={`Delete ${doc.name}`}>
                    <i className="fa fa-trash" aria-hidden="true" />
                  </button>
                </span>
              ),
            }] : []),
          ]}
        />
      )}
      {editing && (
        <DocumentModal doc={editing} onClose={() => setEditing(null)}
          onSaved={async (ok) => { setEditing(null); setMessage({ ok }); await load(); }} />
      )}
    </AdminShell>
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
