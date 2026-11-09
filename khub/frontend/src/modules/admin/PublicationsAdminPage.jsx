import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { apiFetch } from '../../core/api.js';
import { useSite, usePermission } from '../../core/site-context.jsx';
import { withSitePrefix } from '@khub/shared/urls';
import { AdminShell } from './AdminShell.jsx';
import { DataTable } from '../../core/table.jsx';
import { Modal, StatusBadge, Flash, STATUS_TONE, Skeleton, Switch } from '../../core/ui.jsx';
import { ResourceCard, AttachmentsCard, CommentsCard, RejectModal } from './PublicationView.jsx';
import { formatDate, formatNumber } from '../../core/format.js';

const STATUSES = [['all', 'All'], ['approved', 'Approved'], ['pending', 'Pending'], ['rejected', 'Rejected'], ['inactive', 'Inactive'], ['trashed', 'Trash']];

/** Manage Resources: search, filter, feature, activate, delete — preview stays inside the admin. */
export const PublicationsAdminPage = () => {
  const { manifest } = useSite();
  const canEdit = usePermission('admin.publications.edit');
  const to = (path) => withSitePrefix(manifest.site.pathPrefix, path);

  const [filters, setFilters] = useState({ term: '', status: 'all', page: 1 });
  const [payload, setPayload] = useState(null);
  const [preview, setPreview] = useState(null);
  const [message, setMessage] = useState(null);
  const [busy, setBusy] = useState(false);

  const load = (next = filters) => {
    const query = { page: next.page, status: next.status };
    if (next.term.trim()) query.term = next.term.trim();
    return apiFetch('/api/admin/publications', { query }).then(setPayload).catch((err) => setMessage({ error: err.message }));
  };
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
      await run();
      await load();
      if (ok) setMessage({ ok });
    } catch (err) {
      setMessage({ error: err.message });
    } finally {
      setBusy(false);
    }
  };

  const toggle = (row, field, value) => act(() => apiFetch(`/api/admin/publications/${row.id}`, { method: 'PUT', body: { [field]: value } }));
  const remove = (row) => act(() => apiFetch(`/api/admin/publications/${row.id}`, { method: 'DELETE' }), `"${row.title}" moved to the trash.`);
  const restore = (row) => act(() => apiFetch(`/api/admin/publications/${row.id}/restore`, { method: 'POST' }), `"${row.title}" restored.`);
  const destroy = (row) => window.confirm(`Permanently destroy "${row.title}" and its files? This cannot be undone.`)
    && act(() => apiFetch(`/api/admin/publications/${row.id}/destroy`, { method: 'DELETE' }), 'Resource destroyed.');

  const columns = [
    {
      key: 'title',
      label: 'Title',
      render: (row) => (
        <span style={{ display: 'inline-block', maxWidth: 340 }}>
          <Link to={to(`/admin/publications/details?id=${row.id}`)} className="ft-medium" style={{ color: '#18181b', textDecoration: 'none' }}
            title="Open details inside the admin">
            {row.title}
          </Link>
          {row.status === 'rejected' && row.rejectedReason && <small className="d-block text-danger">Reason: {row.rejectedReason}</small>}
        </span>
      ),
    },
    { key: 'author', label: 'Author', render: (row) => row.author || <span className="text-muted">—</span> },
    { key: 'status', label: 'Status', align: 'center', render: (row) => <StatusBadge tone={STATUS_TONE[row.status] ?? 'neutral'}>{row.status}</StatusBadge> },
    { key: 'visits', label: 'Views', align: 'center', render: (row) => formatNumber(row.visits) },
    {
      key: 'submitted',
      label: 'Submitted',
      nowrap: true,
      render: (row) => <small>{row.submittedBy || '—'}<span className="text-muted d-block">{formatDate(row.createdAt)}</span></small>,
    },
    {
      key: 'featured',
      label: 'Featured',
      align: 'center',
      render: (row) => (
        <Switch checked={row.featured} disabled={!canEdit || busy} label={`Feature ${row.title}`}
          onChange={(next) => toggle(row, 'is_featured', next)} />
      ),
    },
    {
      key: 'active',
      label: 'Active',
      align: 'center',
      render: (row) => (
        <Switch checked={row.active} disabled={!canEdit || busy} label={`Activate ${row.title}`}
          onChange={(next) => toggle(row, 'is_active', next)} />
      ),
    },
    {
      key: 'actions',
      label: 'Actions',
      align: 'end',
      render: (row) => (
        <span className="kh-table-actions">
          {!row.trashed && (
            <button type="button" className="btn btn-sm btn-outline-secondary" onClick={() => setPreview(row)} aria-label={`Preview ${row.title}`}>
              <i className="fa fa-eye" aria-hidden="true" /> Preview
            </button>
          )}
          {canEdit && !row.trashed && (
            <button type="button" className="btn btn-sm btn-outline-danger" disabled={busy} onClick={() => remove(row)} title="Move to trash" aria-label={`Trash ${row.title}`}>
              <i className="fa fa-trash" aria-hidden="true" />
            </button>
          )}
          {canEdit && row.trashed && (
            <>
              <button type="button" className="btn btn-sm btn-outline-secondary" disabled={busy} onClick={() => restore(row)}>
                <i className="fa fa-rotate-left mr-1" aria-hidden="true" /> Restore
              </button>
              <button type="button" className="btn btn-sm btn-outline-danger" disabled={busy} onClick={() => destroy(row)}>
                <i className="fa fa-fire" aria-hidden="true" /> Destroy
              </button>
            </>
          )}
        </span>
      ),
    },
  ];

  return (
    <AdminShell title="Manage resources" subtitle="Search, feature, activate, or remove any publication on the hub.">
      <Flash message={message} />
      {!payload && !message?.error && <p role="status">Loading…</p>}
      {payload && (
        <DataTable
          title="Resources"
          description="All publications, including pending, rejected, and inactive ones."
          columns={columns}
          rows={payload.data}
          showRowNumbers
          page={payload.meta.page}
          perPage={payload.meta.perPage}
          totalPages={payload.meta.lastPage}
          total={payload.meta.total}
          onPageChange={(page) => { const next = { ...filters, page }; setFilters(next); load(next); }}
          emptyMessage="No resources match these filters."
          toolbar={(
            <>
              <form className="d-flex" style={{ gap: 6 }} onSubmit={(event) => { event.preventDefault(); apply({}); }}>
                <input className="form-control form-control-sm" style={{ width: 200 }} placeholder="Search title or author…"
                  value={filters.term} onChange={(event) => setFilters({ ...filters, term: event.target.value })} aria-label="Search resources" />
                <button type="submit" className="btn btn-sm theme-bg text-white"><i className="fa fa-magnifying-glass" aria-hidden="true" /></button>
              </form>
              <a className="btn btn-sm btn-outline-secondary"
                href={`/api/admin/publications/export?status=${filters.status}${filters.term.trim() ? `&term=${encodeURIComponent(filters.term.trim())}` : ''}`}
                download title="Download the current filter as CSV">
                <i className="fa fa-file-csv mr-1" aria-hidden="true" /> Export
              </a>
              <span className="d-inline-flex" style={{ gap: 4 }}>
                {STATUSES.map(([value, label]) => (
                  <button key={value} type="button" onClick={() => apply({ status: value })}
                    className={`btn btn-sm ${filters.status === value ? 'theme-bg text-white' : 'btn-outline-secondary'}`}>{label}</button>
                ))}
              </span>
            </>
          )}
        />
      )}
      {preview && (
        <PreviewModal
          id={preview.id}
          onClose={() => setPreview(null)}
          onChanged={(ok) => { setMessage({ ok }); load(); }}
          detailsPath={to(`/admin/publications/details?id=${preview.id}`)}
        />
      )}
    </AdminShell>
  );
};

/**
 * In-admin preview of the actual resource (shared view components, not the
 * public page): full content, every comment with its moderation status, and
 * approve/reject right in the dialog.
 */
const PreviewModal = ({ id, onClose, onChanged, detailsPath }) => {
  const canReview = usePermission('admin.approvals.review');
  const [item, setItem] = useState(null);
  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);
  const [rejecting, setRejecting] = useState(false);

  const load = () => apiFetch(`/api/admin/publications/${id}`).then((payload) => setItem(payload.data)).catch((err) => setError(err.message));
  useEffect(() => { load(); }, [id]); // eslint-disable-line

  const act = async (run, ok) => {
    setBusy(true);
    setError('');
    try {
      await run();
      await load();
      onChanged(ok);
    } catch (err) {
      setError(err.message);
    } finally {
      setBusy(false);
    }
  };
  const review = (action, reason) => act(
    () => apiFetch('/api/admin/approvals/review', { method: 'POST', body: { type: 'publication', id, action, reason } }),
    action === 'approve' ? 'Resource approved and published.' : 'Resource rejected.',
  );
  const reviewComment = (commentId, action) => act(
    () => apiFetch('/api/admin/approvals/review', { method: 'POST', body: { type: 'publication_comment', id: commentId, action } }),
    `Comment ${action}d.`,
  );

  return (
    <Modal wide title={item ? item.title : 'Preview'} icon="fa-eye" onClose={onClose}
      footer={(
        <>
          {item && (
            <span className="d-inline-flex align-items-center" style={{ gap: 8, marginInlineEnd: 'auto' }}>
              <StatusBadge tone={STATUS_TONE[item.status] ?? 'neutral'}>{item.status}</StatusBadge>
              {item.featured && <StatusBadge tone="warning">Featured</StatusBadge>}
              <small className="text-muted">{formatNumber(item.visits)} view(s) · submitted {formatDate(item.createdAt)}{item.submittedBy ? ` by ${item.submittedBy.name}` : ''}</small>
            </span>
          )}
          {item && canReview && item.status !== 'approved' && (
            <button type="button" className="btn btn-sm theme-bg text-white" disabled={busy} onClick={() => review('approve')}>
              <i className="fa fa-check mr-1" aria-hidden="true" /> Approve
            </button>
          )}
          {item && canReview && item.status !== 'rejected' && (
            <button type="button" className="btn btn-sm btn-outline-danger" disabled={busy} onClick={() => setRejecting(true)}>
              <i className="fa fa-xmark mr-1" aria-hidden="true" /> Reject
            </button>
          )}
          <Link to={detailsPath} className="btn btn-sm btn-outline-secondary">
            <i className="fa fa-up-right-from-square mr-1" aria-hidden="true" /> Full details
          </Link>
          <button type="button" className="btn btn-sm btn-outline-secondary" onClick={onClose}>Close</button>
        </>
      )}>
      {error && <div role="alert" className="alert alert-danger">{error}</div>}
      {!item && !error && <Skeleton lines={6} />}
      {item && (
        <>
          {item.status === 'rejected' && item.rejectedReason && (
            <div className="alert alert-warning"><strong>Rejection reason:</strong> {item.rejectedReason}</div>
          )}
          <ResourceCard item={item} title={null} />
          <AttachmentsCard item={item} />
          <CommentsCard item={item} canReview={canReview} busy={busy} onReviewComment={reviewComment} />
        </>
      )}
      {rejecting && (
        <RejectModal onClose={() => setRejecting(false)} onReject={async (reason) => { setRejecting(false); await review('reject', reason); }} />
      )}
    </Modal>
  );
};
