import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { apiFetch } from '../../core/api.js';
import { useSite, usePermission } from '../../core/site-context.jsx';
import { withSitePrefix } from '@khub/shared/urls';
import { AdminShell, AdminCard } from './AdminShell.jsx';
import { DataTable } from '../../core/table.jsx';
import { Flash, StatusBadge, STATUS_TONE, Switch } from '../../core/ui.jsx';
import { RejectModal } from './PublicationView.jsx';
import { formatDate, formatNumber } from '../../core/format.js';

const STATUSES = [['all', 'All'], ['approved', 'Approved'], ['pending', 'Pending'], ['rejected', 'Rejected'], ['trashed', 'Trash']];

/** Forum management: moderate, pin, lock, and trash discussion threads. */
export const ForumsAdminPage = () => {
  const { manifest } = useSite();
  const canManage = usePermission('admin.forums.manage');
  const canReview = usePermission('admin.approvals.review');
  const to = (path) => withSitePrefix(manifest.site.pathPrefix, path);
  const [filters, setFilters] = useState({ term: '', status: 'all', page: 1 });
  const [payload, setPayload] = useState(null);
  const [rejecting, setRejecting] = useState(null);
  const [message, setMessage] = useState(null);
  const [busy, setBusy] = useState(false);

  const load = (next = filters) => apiFetch('/api/admin/forums', { query: { term: next.term.trim(), status: next.status, page: next.page } })
    .then(setPayload).catch((err) => setMessage({ error: err.message }));
  useEffect(() => { load(); }, []); // eslint-disable-line

  const apply = (patch) => { const next = { ...filters, page: 1, ...patch }; setFilters(next); load(next); };

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

  const review = (row, action, reason) => act(
    () => apiFetch('/api/admin/approvals/review', { method: 'POST', body: { type: 'forum', id: row.id, action, reason } }),
    `"${row.title}" ${action}d.`,
  );

  return (
    <AdminShell title="Forum management" subtitle="Moderate discussion threads: approve, reject, pin, lock, or trash.">
      <Flash message={message} />
      {!payload && !message?.error && <AdminCard><p role="status" className="mb-0">Loading…</p></AdminCard>}
      {payload && (
        <DataTable
          title="Discussion threads"
          description="Pinned threads float to the top of the public forum; locked ones close for new replies."
          rows={payload.data}
          showRowNumbers
          page={payload.meta.page}
          perPage={payload.meta.perPage}
          totalPages={payload.meta.lastPage}
          total={payload.meta.total}
          onPageChange={(page) => { const next = { ...filters, page }; setFilters(next); load(next); }}
          emptyMessage="No discussions match these filters."
          toolbar={(
            <>
              <form className="d-flex" style={{ gap: 6 }} onSubmit={(event) => { event.preventDefault(); apply({}); }}>
                <input className="form-control form-control-sm" style={{ width: 200 }} type="search" placeholder="Title, description, or author…"
                  value={filters.term} onChange={(event) => setFilters({ ...filters, term: event.target.value })} aria-label="Search forums" />
                <button type="submit" className="btn btn-sm theme-bg text-white"><i className="fa fa-magnifying-glass" aria-hidden="true" /></button>
              </form>
              <span className="d-inline-flex" style={{ gap: 4 }}>
                {STATUSES.map(([value, label]) => (
                  <button key={value} type="button" onClick={() => apply({ status: value })}
                    className={`btn btn-sm ${filters.status === value ? 'theme-bg text-white' : 'btn-outline-secondary'}`}>{label}</button>
                ))}
              </span>
            </>
          )}
          columns={[
            {
              key: 'title',
              label: 'Discussion',
              render: (row) => (
                <span style={{ display: 'inline-block', maxWidth: 300 }}>
                  <Link to={to(`/forums/${row.slug ?? row.id}`)} className="ft-medium" style={{ color: '#18181b' }}>{row.title}</Link>
                  {row.status === 'rejected' && row.rejectedReason && <small className="d-block text-danger">Reason: {row.rejectedReason}</small>}
                </span>
              ),
            },
            { key: 'author', label: 'Author', render: (row) => row.author || <span className="text-muted">—</span> },
            { key: 'status', label: 'Status', align: 'center', render: (row) => <StatusBadge tone={STATUS_TONE[row.status] ?? 'neutral'}>{row.status}</StatusBadge> },
            { key: 'comments', label: 'Replies', align: 'center', render: (row) => formatNumber(row.comments) },
            { key: 'views', label: 'Views', align: 'center', render: (row) => formatNumber(row.views) },
            {
              key: 'pinned',
              label: 'Pinned',
              align: 'center',
              render: (row) => <Switch checked={row.pinned} disabled={!canManage || busy || Boolean(row.trashedAt)} label={`Pin ${row.title}`}
                onChange={(next) => act(() => apiFetch(`/api/admin/forums/${row.id}`, { method: 'PUT', body: { is_pinned: next } }))} />,
            },
            {
              key: 'locked',
              label: 'Locked',
              align: 'center',
              render: (row) => <Switch checked={row.locked} disabled={!canManage || busy || Boolean(row.trashedAt)} label={`Lock ${row.title}`}
                onChange={(next) => act(() => apiFetch(`/api/admin/forums/${row.id}`, { method: 'PUT', body: { is_locked: next } }))} />,
            },
            { key: 'created', label: 'Created', nowrap: true, render: (row) => formatDate(row.createdAt) },
            {
              key: 'actions',
              label: 'Actions',
              align: 'end',
              render: (row) => (
                <span className="kh-table-actions">
                  {canReview && !row.trashedAt && row.status !== 'approved' && (
                    <button type="button" className="btn btn-sm theme-bg text-white" disabled={busy} onClick={() => review(row, 'approve')}>
                      <i className="fa fa-check mr-1" aria-hidden="true" /> Approve
                    </button>
                  )}
                  {canReview && !row.trashedAt && row.status === 'pending' && (
                    <button type="button" className="btn btn-sm btn-outline-danger" disabled={busy} onClick={() => setRejecting(row)}>
                      <i className="fa fa-xmark mr-1" aria-hidden="true" /> Reject
                    </button>
                  )}
                  {canManage && !row.trashedAt && (
                    <button type="button" className="btn btn-sm btn-outline-danger" disabled={busy} title="Move to trash"
                      onClick={() => act(() => apiFetch(`/api/admin/forums/${row.id}`, { method: 'DELETE' }), `"${row.title}" moved to the trash.`)}>
                      <i className="fa fa-trash" aria-hidden="true" />
                    </button>
                  )}
                  {canManage && row.trashedAt && (
                    <>
                      <button type="button" className="btn btn-sm btn-outline-secondary" disabled={busy}
                        onClick={() => act(() => apiFetch(`/api/admin/forums/${row.id}/restore`, { method: 'POST' }), `"${row.title}" restored.`)}>
                        <i className="fa fa-rotate-left mr-1" aria-hidden="true" /> Restore
                      </button>
                      <button type="button" className="btn btn-sm btn-outline-danger" disabled={busy}
                        onClick={() => window.confirm(`Permanently destroy "${row.title}" with its replies?`)
                          && act(() => apiFetch(`/api/admin/forums/${row.id}/destroy`, { method: 'DELETE' }), 'Discussion destroyed.')}>
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
      {rejecting && (
        <RejectModal onClose={() => setRejecting(null)}
          onReject={async (reason) => { const row = rejecting; setRejecting(null); await review(row, 'reject', reason); }} />
      )}
    </AdminShell>
  );
};
