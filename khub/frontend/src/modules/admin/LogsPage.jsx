import { useEffect, useState } from 'react';
import { apiFetch } from '../../core/api.js';
import { usePermission } from '../../core/site-context.jsx';
import { AdminShell, AdminCard } from './AdminShell.jsx';
import { DataTable } from '../../core/table.jsx';
import { Modal, FormField, Flash, StatusBadge, UserChip } from '../../core/ui.jsx';
import { formatDate, timeAgo } from '../../core/format.js';

const TABS = [
  ['logins', 'Sign-ins', 'fa-right-to-bracket'],
  ['registrations', 'Registrations', 'fa-user-plus'],
  ['searches', 'Search activity', 'fa-magnifying-glass'],
  ['blocked', 'Blocked IPs', 'fa-shield-halved'],
];

const Agent = ({ value }) => {
  if (!value) return <span className="text-muted">—</span>;
  const short = value.includes('Chrome') ? 'Chrome' : value.includes('Firefox') ? 'Firefox' : value.includes('Safari') ? 'Safari' : value.includes('curl') ? 'curl' : value.split(' ')[0];
  return <span title={value} className="text-muted" style={{ cursor: 'help' }}>{short}</span>;
};

/** User & site access logs: tabbed data tables with suspicious-IP blocking. */
export const LogsPage = () => {
  const canBlock = usePermission('admin.security.manage');
  const [tab, setTab] = useState('logins');
  const [data, setData] = useState(null);
  const [blocked, setBlocked] = useState(null);
  const [blocking, setBlocking] = useState(null); // null | { ip, reason }
  const [message, setMessage] = useState(null);

  const load = async () => {
    const [logs, security] = await Promise.all([apiFetch('/api/admin/logs'), apiFetch('/api/admin/security/blocked')]);
    setData(logs);
    setBlocked(security.blocked);
  };
  useEffect(() => { load().catch((err) => setMessage({ error: err.message })); }, []);

  const blockedIps = new Set((blocked ?? []).map((row) => row.ip));

  const unblock = async (row) => {
    if (!window.confirm(`Unblock ${row.ip}?`)) return;
    try {
      await apiFetch(`/api/admin/security/blocked/${row.id}`, { method: 'DELETE' });
      setMessage({ ok: `${row.ip} unblocked.` });
      await load();
    } catch (err) {
      setMessage({ error: err.message });
    }
  };

  const counts = {
    logins: data?.logins.length ?? 0,
    registrations: data?.registrations.length ?? 0,
    searches: data?.searches.length ?? 0,
    blocked: blocked?.length ?? 0,
  };

  return (
    <AdminShell title="User & site access logs" subtitle="Who signed in, who registered, what was searched — and which networks are blocked.">
      <Flash message={message} />
      <nav className="mb-3 d-flex flex-wrap" style={{ gap: 6 }} aria-label="Log sections">
        {TABS.map(([id, label, icon]) => (
          <button key={id} type="button" onClick={() => setTab(id)}
            className={`btn btn-sm ${tab === id ? 'theme-bg text-white' : 'btn-outline-secondary'}`}>
            <i className={`fa ${icon} mr-1`} aria-hidden="true" /> {label}
            <span className="badge badge-light ml-2">{counts[id]}</span>
          </button>
        ))}
      </nav>
      {!data && !message?.error && <AdminCard><p role="status" className="mb-0">Loading…</p></AdminCard>}

      {data && tab === 'logins' && (
        <DataTable
          title="Recent sign-ins"
          description="Latest successful sign-in per account."
          rows={data.logins}
          showRowNumbers
          clientPerPage={15}
          emptyMessage="No sign-ins recorded."
          columns={[
            { key: 'user', label: 'User', render: (row) => <UserChip photo={row.photo} name={row.name} sub={row.email} /> },
            { key: 'status', label: 'Account', align: 'center', render: (row) => <StatusBadge tone={row.status ? 'success' : 'neutral'}>{row.status ? 'Active' : 'Inactive'}</StatusBadge> },
            { key: 'when', label: 'Last sign-in', nowrap: true, render: (row) => <span title={formatDate(row.last_login_at)}>{timeAgo(row.last_login_at)}</span> },
          ]}
        />
      )}

      {data && tab === 'registrations' && (
        <DataTable
          title="Recent registrations"
          description="Newest accounts first."
          rows={data.registrations}
          showRowNumbers
          clientPerPage={15}
          emptyMessage="No registrations yet."
          columns={[
            { key: 'user', label: 'User', render: (row) => <UserChip photo={row.photo} name={row.name} sub={row.email} /> },
            { key: 'status', label: 'Account', align: 'center', render: (row) => <StatusBadge tone={row.status ? 'success' : 'neutral'}>{row.status ? 'Active' : 'Inactive'}</StatusBadge> },
            { key: 'when', label: 'Registered', nowrap: true, render: (row) => formatDate(row.created_at) },
          ]}
        />
      )}

      {data && tab === 'searches' && (
        <DataTable
          title="Search activity"
          description="Every recorded search with its source network — block anything suspicious."
          rows={data.searches}
          showRowNumbers
          clientPerPage={15}
          emptyMessage="No searches logged yet."
          columns={[
            { key: 'term', label: 'Term', render: (row) => <span className="ft-medium" style={{ color: '#18181b' }}>“{row.term}”</span> },
            { key: 'results', label: 'Results', align: 'center', render: (row) => row.results_count ?? '—' },
            { key: 'who', label: 'User', render: (row) => row.user_name || <span className="text-muted">guest</span> },
            {
              key: 'ip',
              label: 'IP address',
              render: (row) => row.ip_address ? (
                <span className="d-inline-flex align-items-center" style={{ gap: 6 }}>
                  <code style={{ fontSize: '0.78rem' }}>{row.ip_address}</code>
                  {blockedIps.has(row.ip_address) && <StatusBadge tone="danger">blocked</StatusBadge>}
                </span>
              ) : <span className="text-muted">—</span>,
            },
            { key: 'path', label: 'Path', render: (row) => <span className="text-muted" style={{ fontSize: '0.78rem' }}>{row.request_path || '—'}</span> },
            { key: 'agent', label: 'Client', render: (row) => <Agent value={row.user_agent} /> },
            { key: 'when', label: 'When', nowrap: true, render: (row) => <span title={formatDate(row.created_at)}>{timeAgo(row.created_at)}</span> },
            ...(canBlock ? [{
              key: 'actions',
              label: 'Actions',
              align: 'end',
              render: (row) => row.ip_address && !blockedIps.has(row.ip_address) ? (
                <button type="button" className="btn btn-sm btn-outline-danger"
                  onClick={() => setBlocking({ ip: row.ip_address, reason: `Suspicious search activity (“${row.term}”)` })}>
                  <i className="fa fa-ban mr-1" aria-hidden="true" /> Block IP
                </button>
              ) : null,
            }] : []),
          ]}
        />
      )}

      {blocked && tab === 'blocked' && (
        <DataTable
          title="Blocked IPs"
          description="Requests from these addresses receive 403 across the whole site (administrators excepted)."
          rows={blocked}
          showRowNumbers
          clientPerPage={15}
          emptyMessage="No IPs are blocked."
          emptyIcon="fa-shield-halved"
          toolbar={canBlock && (
            <button type="button" className="btn btn-sm theme-bg text-white" onClick={() => setBlocking({ ip: '', reason: '' })}>
              <i className="fa fa-ban mr-1" aria-hidden="true" /> Block an IP
            </button>
          )}
          columns={[
            { key: 'ip', label: 'IP address', render: (row) => <code style={{ fontSize: '0.82rem' }}>{row.ip}</code> },
            { key: 'reason', label: 'Reason', render: (row) => row.reason || <span className="text-muted">—</span> },
            { key: 'by', label: 'Blocked by', render: (row) => row.blocked_by || '—' },
            { key: 'when', label: 'Since', nowrap: true, render: (row) => formatDate(row.created_at) },
            ...(canBlock ? [{
              key: 'actions',
              label: 'Actions',
              align: 'end',
              render: (row) => (
                <button type="button" className="btn btn-sm btn-outline-secondary" onClick={() => unblock(row)}>
                  <i className="fa fa-unlock mr-1" aria-hidden="true" /> Unblock
                </button>
              ),
            }] : []),
          ]}
        />
      )}

      {blocking && (
        <BlockIpModal
          initial={blocking}
          onClose={() => setBlocking(null)}
          onBlocked={async (ip) => {
            setBlocking(null);
            setMessage({ ok: `${ip} is now blocked site-wide.` });
            await load();
            setTab('blocked');
          }}
        />
      )}
    </AdminShell>
  );
};

/** Individual form: block one IP with a reason. */
const BlockIpModal = ({ initial, onClose, onBlocked }) => {
  const [draft, setDraft] = useState(initial);
  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);

  const block = async () => {
    setBusy(true);
    setError('');
    try {
      await apiFetch('/api/admin/security/blocked', { method: 'POST', body: { ip: draft.ip.trim(), reason: draft.reason.trim() } });
      onBlocked(draft.ip.trim());
    } catch (err) {
      setError(err.details ? err.details.map((detail) => detail.message).join(' ') : err.message);
      setBusy(false);
    }
  };

  return (
    <Modal title="Block an IP address" icon="fa-ban" onClose={onClose}
      footer={(
        <>
          <button type="button" className="btn btn-sm btn-outline-secondary" onClick={onClose}>Cancel</button>
          <button type="button" className="btn btn-sm btn-outline-danger" disabled={busy || !draft.ip.trim()} onClick={block}>
            {busy ? 'Blocking…' : 'Block this IP'}
          </button>
        </>
      )}>
      {error && <div role="alert" className="alert alert-danger">{error}</div>}
      <FormField label="IP address" required hint="IPv4 or IPv6. Every request from it will receive 403.">
        <input className="form-control" value={draft.ip} autoFocus={!draft.ip} placeholder="e.g. 203.0.113.9"
          onChange={(event) => setDraft({ ...draft, ip: event.target.value })} />
      </FormField>
      <FormField label="Reason" hint="Kept with the block entry so other administrators know why.">
        <textarea className="form-control" rows={3} value={draft.reason} autoFocus={Boolean(draft.ip)}
          onChange={(event) => setDraft({ ...draft, reason: event.target.value })} />
      </FormField>
    </Modal>
  );
};

/** Mailing list: newsletter subscribers and users who opted in on their profile. */
export const MailingPage = () => {
  const canEdit = usePermission('admin.mailing.edit');
  const [data, setData] = useState(null);
  const [message, setMessage] = useState(null);
  const load = () => apiFetch('/api/admin/mailing').then(setData).catch((err) => setMessage({ error: err.message }));
  useEffect(() => { load(); }, []);

  const remove = async (row) => {
    if (!window.confirm(`Remove ${row.email} from the mailing list?`)) return;
    try {
      await apiFetch(`/api/admin/mailing/${row.id}`, { method: 'DELETE' });
      setMessage({ ok: `${row.email} removed.` });
      await load();
    } catch (err) {
      setMessage({ error: err.message });
    }
  };

  return (
    <AdminShell title="Mailing list" subtitle="Newsletter subscribers and users who opted in from their profile.">
      <Flash message={message} />
      {!data && !message?.error && <AdminCard><p role="status" className="mb-0">Loading…</p></AdminCard>}
      {data && (
        <div className="row">
          <div className="col-lg-7 mb-3">
            <DataTable
              title="Newsletter subscribers"
              rows={data.subscribers}
              showRowNumbers
              clientPerPage={12}
              emptyMessage="No subscribers yet."
              columns={[
                { key: 'email', label: 'Email', render: (row) => <span className="ft-medium" style={{ color: '#18181b' }}>{row.email}</span> },
                { key: 'name', label: 'Name', render: (row) => row.name || <span className="text-muted">—</span> },
                { key: 'status', label: 'Status', align: 'center', render: (row) => <StatusBadge tone={String(row.status).toLowerCase() === 'subscribed' ? 'success' : 'neutral'}>{row.status}</StatusBadge> },
                { key: 'when', label: 'Since', nowrap: true, render: (row) => formatDate(row.created_at) },
                ...(canEdit ? [{
                  key: 'actions', label: 'Actions', align: 'end',
                  render: (row) => (
                    <button type="button" className="btn btn-sm btn-outline-danger" onClick={() => remove(row)} aria-label={`Remove ${row.email}`}>
                      <i className="fa fa-trash" aria-hidden="true" />
                    </button>
                  ),
                }] : []),
              ]}
            />
          </div>
          <div className="col-lg-5 mb-3">
            <DataTable
              title="Users subscribed via profile"
              rows={data.optedInUsers}
              showRowNumbers
              clientPerPage={12}
              emptyMessage="No users have opted in yet."
              columns={[
                { key: 'user', label: 'User', render: (row) => <UserChip photo={row.photo} name={row.name} sub={row.email} /> },
                { key: 'when', label: 'Joined', nowrap: true, render: (row) => formatDate(row.created_at) },
              ]}
            />
          </div>
        </div>
      )}
    </AdminShell>
  );
};
