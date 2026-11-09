import { useEffect, useState } from 'react';
import { apiFetch } from '../../core/api.js';
import { formatDate } from '../../core/format.js';
import { AdminShell, AdminCard } from './AdminShell.jsx';

/** Approvals inbox: pending publications, forum threads, and comments. */
export const ApprovalsPage = () => {
  const [data, setData] = useState(null);
  const [error, setError] = useState('');
  const [busy, setBusy] = useState('');

  const load = () => apiFetch('/api/admin/approvals').then(setData).catch((err) => setError(err.message));
  useEffect(() => { load(); }, []);

  const review = async (type, id, action) => {
    const reason = action === 'reject' ? window.prompt('Reason for rejection (shown to the submitter):') ?? undefined : undefined;
    if (action === 'reject' && reason === undefined) return;
    setBusy(`${type}:${id}`);
    setError('');
    try {
      await apiFetch('/api/admin/approvals/review', { method: 'POST', body: { type, id, action, reason } });
      await load();
    } catch (err) {
      setError(err.message);
    } finally {
      setBusy('');
    }
  };

  const Actions = ({ type, id }) => (
    <span className="d-inline-flex" style={{ gap: 6 }}>
      <button type="button" className="btn btn-sm theme-bg text-white" disabled={busy === `${type}:${id}`} onClick={() => review(type, id, 'approve')}>
        <i className="fa fa-check mr-1" aria-hidden="true" />Approve
      </button>
      <button type="button" className="btn btn-sm btn-outline-danger" disabled={busy === `${type}:${id}`} onClick={() => review(type, id, 'reject')}>
        <i className="fa fa-xmark mr-1" aria-hidden="true" />Reject
      </button>
    </span>
  );

  const Section = ({ title, rows, type, render }) => (
    <AdminCard title={`${title} (${rows.length})`}>
      {rows.length === 0 ? <p className="mb-0 text-muted">Nothing pending.</p> : (
        <ul className="list-unstyled mb-0">
          {rows.map((row) => (
            <li key={`${row.type ?? type}:${row.id}`} className="d-flex align-items-start justify-content-between py-2" style={{ gap: 12, borderBottom: '1px solid #f1f5f9' }}>
              <div>
                {render(row)}
                <div className="text-muted medium">
                  {row.submitted_by ?? 'Unknown'} · {formatDate(row.created_at)}
                  {row.country_name && <span className="ml-2"><i className="fa fa-earth-africa mr-1" aria-hidden="true" />{row.country_name}</span>}
                </div>
              </div>
              <Actions type={row.type ?? type} id={row.id} />
            </li>
          ))}
        </ul>
      )}
    </AdminCard>
  );

  return (
    <AdminShell title="Approvals inbox">
      {error && <div role="alert" className="alert alert-danger">{error}</div>}
      {!data && !error && <p role="status">Loading…</p>}
      {data && (
        <>
          {data.scope && (
            <div className="alert alert-light border d-flex align-items-center" style={{ gap: 8 }}>
              <i className="fa fa-user-shield theme-cl" aria-hidden="true" />
              <span>
                Reviewing as <strong>{data.scope.label}</strong>
                {data.scope.scopeLabel && <> — scope: <strong>{data.scope.scopeLabel}</strong></>}
                {data.scope.level !== 'overall' && <span className="text-muted"> (items outside this scope are not shown)</span>}
              </span>
            </div>
          )}
          <Section title="Publications" rows={data.publications} type="publication" render={(row) => <strong>{row.title}</strong>} />
          <Section title="Forum discussions" rows={data.forums} type="forum" render={(row) => <strong>{row.title}</strong>} />
          <Section title="Comments" rows={data.comments} type="comment" render={(row) => <span>{String(row.comment).slice(0, 180)}</span>} />
        </>
      )}
    </AdminShell>
  );
};
