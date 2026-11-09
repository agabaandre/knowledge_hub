import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { apiFetch } from '../../core/api.js';
import { useSite, usePermission } from '../../core/site-context.jsx';
import { withSitePrefix } from '@khub/shared/urls';
import { AdminShell, AdminCard } from './AdminShell.jsx';
import { DataTable } from '../../core/table.jsx';
import { Modal, FormField, Flash, StatusBadge, StatCard, Switch, UserChip } from '../../core/ui.jsx';
import { SearchSelect } from '../../core/SearchSelect.jsx';
import { useApi } from '../../core/hooks.js';
import { formatDate } from '../../core/format.js';

const VIEWS = [['all', 'All'], ['active', 'Active'], ['inactive', 'Inactive'], ['trash', 'Trash']];
const MEMBER_STATUS = [['all', 'All'], ['pending', 'Pending'], ['approved', 'Approved']];

/** Communities of Practice: Manage COPs + the COP Participants Directory. */
export const CommunitiesAdminPage = () => {
  const { manifest } = useSite();
  const canManage = usePermission('admin.communities.manage');
  const to = (path) => withSitePrefix(manifest.site.pathPrefix, path);
  const [tab, setTab] = useState('communities');
  const [creating, setCreating] = useState(false);
  const lookups = useApi('/api/publications/lookups');
  const [stats, setStats] = useState(null);
  const [message, setMessage] = useState(null);
  const [busy, setBusy] = useState(false);

  const [copFilters, setCopFilters] = useState({ term: '', view: 'all', page: 1 });
  const [cops, setCops] = useState(null);
  const [memberFilters, setMemberFilters] = useState({ term: '', status: 'all', page: 1 });
  const [members, setMembers] = useState(null);

  const loadStats = () => apiFetch('/api/admin/communities/stats').then(setStats).catch(() => {});
  const loadCops = (next = copFilters) => apiFetch('/api/admin/communities', { query: { term: next.term.trim(), view: next.view, page: next.page } }).then(setCops).catch((err) => setMessage({ error: err.message }));
  const loadMembers = (next = memberFilters) => apiFetch('/api/admin/communities/participants', { query: { term: next.term.trim(), status: next.status, page: next.page } }).then(setMembers).catch((err) => setMessage({ error: err.message }));
  useEffect(() => { loadStats(); loadCops(); loadMembers(); }, []); // eslint-disable-line

  const act = async (run, ok) => {
    setBusy(true);
    setMessage(null);
    try {
      await run();
      await Promise.all([loadStats(), loadCops(), loadMembers()]);
      if (ok) setMessage({ ok });
    } catch (err) {
      setMessage({ error: err.message });
    } finally {
      setBusy(false);
    }
  };

  const applyCops = (patch) => { const next = { ...copFilters, page: 1, ...patch }; setCopFilters(next); loadCops(next); };
  const applyMembers = (patch) => { const next = { ...memberFilters, page: 1, ...patch }; setMemberFilters(next); loadMembers(next); };

  return (
    <AdminShell title="Communities of Practice" subtitle="Manage COPs, memberships, and the participants directory."
      actions={canManage && (
        <button type="button" className="btn btn-sm theme-bg text-white" onClick={() => setCreating(true)}>
          <i className="fa fa-plus mr-1" aria-hidden="true" /> Create community
        </button>
      )}>
      <Flash message={message} />
      {stats && (
        <div className="row">
          <div className="col-md-3 col-6 mb-3"><StatCard value={stats.total} label="Total communities" icon="fa-people-group" /></div>
          <div className="col-md-3 col-6 mb-3"><StatCard value={stats.active} label="Active communities" icon="fa-circle-check" /></div>
          <div className="col-md-3 col-6 mb-3"><StatCard value={stats.approvedMemberships} label="Approved memberships" icon="fa-user-check" /></div>
          <div className="col-md-3 col-6 mb-3"><StatCard value={stats.pendingMemberships} label="Pending approvals" icon="fa-hourglass-half" /></div>
        </div>
      )}
      <nav className="mb-3 d-flex flex-wrap" style={{ gap: 6 }} aria-label="COP sections">
        <button type="button" className={`btn btn-sm ${tab === 'communities' ? 'theme-bg text-white' : 'btn-outline-secondary'}`} onClick={() => setTab('communities')}>
          <i className="fa fa-people-group mr-1" aria-hidden="true" /> Manage COPs
        </button>
        <button type="button" className={`btn btn-sm ${tab === 'participants' ? 'theme-bg text-white' : 'btn-outline-secondary'}`} onClick={() => setTab('participants')}>
          <i className="fa fa-address-book mr-1" aria-hidden="true" /> Participants Directory
          {stats?.pendingMemberships > 0 && <span className="badge badge-danger ml-2">{stats.pendingMemberships}</span>}
        </button>
      </nav>

      {tab === 'communities' && cops && (
        <DataTable
          title="Communities"
          description="Search by community name, description, or creator email."
          rows={cops.data}
          showRowNumbers
          page={cops.meta.page}
          perPage={cops.meta.perPage}
          totalPages={cops.meta.lastPage}
          total={cops.meta.total}
          onPageChange={(page) => { const next = { ...copFilters, page }; setCopFilters(next); loadCops(next); }}
          emptyMessage="No communities match."
          toolbar={(
            <>
              <form className="d-flex" style={{ gap: 6 }} onSubmit={(event) => { event.preventDefault(); applyCops({}); }}>
                <input className="form-control form-control-sm" style={{ width: 210 }} type="search" placeholder="Name, description, creator email…"
                  value={copFilters.term} onChange={(event) => setCopFilters({ ...copFilters, term: event.target.value })} aria-label="Search communities" />
                <button type="submit" className="btn btn-sm theme-bg text-white"><i className="fa fa-magnifying-glass" aria-hidden="true" /></button>
              </form>
              <span className="d-inline-flex" style={{ gap: 4 }}>
                {VIEWS.map(([value, label]) => (
                  <button key={value} type="button" onClick={() => applyCops({ view: value })}
                    className={`btn btn-sm ${copFilters.view === value ? 'theme-bg text-white' : 'btn-outline-secondary'}`}>{label}</button>
                ))}
              </span>
            </>
          )}
          columns={[
            {
              key: 'name',
              label: 'Community',
              render: (row) => (
                <span style={{ display: 'inline-block', maxWidth: 260 }}>
                  <Link to={to(`/communities/${row.slug ?? row.id}`)} className="ft-medium" style={{ color: '#18181b' }}>{row.name}</Link>
                  <small className="d-block text-muted">{row.creator ? `by ${row.creator.name} (${row.creator.email})` : '—'}</small>
                </span>
              ),
            },
            { key: 'country', label: 'Country', render: (row) => row.country || <span className="text-muted">—</span> },
            { key: 'members', label: 'Members', align: 'center', render: (row) => <>{row.members}{row.pendingMembers > 0 && <StatusBadge tone="warning">+{row.pendingMembers} pending</StatusBadge>}</> },
            { key: 'publications', label: 'Resources', align: 'center', render: (row) => row.publications },
            {
              key: 'visibility',
              label: 'Public',
              align: 'center',
              render: (row) => <Switch checked={row.isPublic} disabled={!canManage || busy || Boolean(row.trashedAt)} label={`Public ${row.name}`}
                onChange={(next) => act(() => apiFetch(`/api/admin/communities/${row.id}`, { method: 'PUT', body: { is_public: next } }))} />,
            },
            {
              key: 'active',
              label: 'Active',
              align: 'center',
              render: (row) => <Switch checked={row.isActive} disabled={!canManage || busy || Boolean(row.trashedAt)} label={`Activate ${row.name}`}
                onChange={(next) => act(() => apiFetch(`/api/admin/communities/${row.id}`, { method: 'PUT', body: { is_active: next } }))} />,
            },
            { key: 'created', label: 'Created', nowrap: true, render: (row) => formatDate(row.createdAt) },
            ...(canManage ? [{
              key: 'actions',
              label: 'Actions',
              align: 'end',
              render: (row) => (
                <span className="kh-table-actions">
                  {!row.trashedAt && (
                    <button type="button" className="btn btn-sm btn-outline-danger" disabled={busy} title="Move to trash"
                      onClick={() => act(() => apiFetch(`/api/admin/communities/${row.id}`, { method: 'DELETE' }), `"${row.name}" moved to the trash.`)}>
                      <i className="fa fa-trash" aria-hidden="true" />
                    </button>
                  )}
                  {row.trashedAt && (
                    <>
                      <button type="button" className="btn btn-sm btn-outline-secondary" disabled={busy}
                        onClick={() => act(() => apiFetch(`/api/admin/communities/${row.id}/restore`, { method: 'POST' }), `"${row.name}" restored.`)}>
                        <i className="fa fa-rotate-left mr-1" aria-hidden="true" /> Restore
                      </button>
                      <button type="button" className="btn btn-sm btn-outline-danger" disabled={busy}
                        onClick={() => window.confirm(`Permanently destroy "${row.name}" and its memberships?`)
                          && act(() => apiFetch(`/api/admin/communities/${row.id}/destroy`, { method: 'DELETE' }), 'Community destroyed.')}>
                        <i className="fa fa-fire" aria-hidden="true" /> Destroy
                      </button>
                    </>
                  )}
                </span>
              ),
            }] : []),
          ]}
        />
      )}

      {tab === 'participants' && members && (
        <DataTable
          title="COP Participants Directory"
          description="Every membership across communities — approve pending requests, grant community-admin, or remove."
          rows={members.data}
          showRowNumbers
          page={members.meta.page}
          perPage={members.meta.perPage}
          totalPages={members.meta.lastPage}
          total={members.meta.total}
          onPageChange={(page) => { const next = { ...memberFilters, page }; setMemberFilters(next); loadMembers(next); }}
          emptyMessage="No memberships match."
          toolbar={(
            <>
              <form className="d-flex" style={{ gap: 6 }} onSubmit={(event) => { event.preventDefault(); applyMembers({}); }}>
                <input className="form-control form-control-sm" style={{ width: 210 }} type="search" placeholder="Member, email, or community…"
                  value={memberFilters.term} onChange={(event) => setMemberFilters({ ...memberFilters, term: event.target.value })} aria-label="Search participants" />
                <button type="submit" className="btn btn-sm theme-bg text-white"><i className="fa fa-magnifying-glass" aria-hidden="true" /></button>
              </form>
              <span className="d-inline-flex" style={{ gap: 4 }}>
                {MEMBER_STATUS.map(([value, label]) => (
                  <button key={value} type="button" onClick={() => applyMembers({ status: value })}
                    className={`btn btn-sm ${memberFilters.status === value ? 'theme-bg text-white' : 'btn-outline-secondary'}`}>{label}</button>
                ))}
              </span>
            </>
          )}
          columns={[
            { key: 'user', label: 'Participant', render: (row) => <UserChip photo={row.user.photo} name={row.user.name} sub={row.user.email} /> },
            { key: 'community', label: 'Community', render: (row) => row.community.name },
            { key: 'status', label: 'Status', align: 'center', render: (row) => <StatusBadge tone={row.approved ? 'success' : 'warning'}>{row.approved ? 'approved' : 'pending'}</StatusBadge> },
            {
              key: 'admin',
              label: 'Community admin',
              align: 'center',
              render: (row) => <Switch checked={row.isAdmin} disabled={!canManage || busy} label={`Community admin ${row.user.name}`}
                onChange={(next) => act(() => apiFetch(`/api/admin/communities/participants/${row.id}`, { method: 'PUT', body: { is_admin: next } }))} />,
            },
            { key: 'joined', label: 'Joined', nowrap: true, render: (row) => formatDate(row.joinedAt) },
            ...(canManage ? [{
              key: 'actions',
              label: 'Actions',
              align: 'end',
              render: (row) => (
                <span className="kh-table-actions">
                  {!row.approved && (
                    <button type="button" className="btn btn-sm theme-bg text-white" disabled={busy}
                      onClick={() => act(() => apiFetch(`/api/admin/communities/participants/${row.id}`, { method: 'PUT', body: { is_approved: true } }), `${row.user.name} approved into ${row.community.name}.`)}>
                      <i className="fa fa-check mr-1" aria-hidden="true" /> Approve
                    </button>
                  )}
                  <button type="button" className="btn btn-sm btn-outline-danger" disabled={busy} aria-label={`Remove ${row.user.name}`}
                    onClick={() => window.confirm(`Remove ${row.user.name} from ${row.community.name}?`)
                      && act(() => apiFetch(`/api/admin/communities/participants/${row.id}`, { method: 'DELETE' }), 'Membership removed.')}>
                    <i className="fa fa-user-minus" aria-hidden="true" />
                  </button>
                </span>
              ),
            }] : []),
          ]}
        />
      )}
      {((tab === 'communities' && !cops) || (tab === 'participants' && !members)) && !message?.error && (
        <AdminCard><p role="status" className="mb-0">Loading…</p></AdminCard>
      )}
      {creating && (
        <CreateCopModal countries={lookups.data?.countries ?? []} onClose={() => setCreating(false)}
          onCreated={async (name) => {
            setCreating(false);
            setMessage({ ok: `"${name}" created.` });
            await Promise.all([loadStats(), loadCops()]);
          }} />
      )}
    </AdminShell>
  );
};

/** Individual form: create a community of practice (the Laravel admin form). */
const CreateCopModal = ({ countries, onClose, onCreated }) => {
  const [draft, setDraft] = useState({ name: '', description: '', organisation: '', country_id: '', is_public: true, is_active: true });
  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);

  const create = async () => {
    setBusy(true);
    setError('');
    try {
      await apiFetch('/api/admin/communities', {
        method: 'POST',
        body: { ...draft, country_id: draft.country_id ? Number(draft.country_id) : null },
      });
      onCreated(draft.name);
    } catch (err) {
      setError(err.details ? err.details.map((detail) => detail.message).join(' ') : err.message);
      setBusy(false);
    }
  };

  return (
    <Modal title="Create a community of practice" icon="fa-people-group" onClose={onClose}
      footer={(
        <>
          <button type="button" className="btn btn-sm btn-outline-secondary" onClick={onClose}>Cancel</button>
          <button type="button" className="btn btn-sm theme-bg text-white" disabled={busy || draft.name.trim().length < 3 || draft.description.trim().length < 10} onClick={create}>
            {busy ? 'Creating…' : 'Create community'}
          </button>
        </>
      )}>
      {error && <div role="alert" className="alert alert-danger">{error}</div>}
      <FormField label="Community name" required>
        <input className="form-control" value={draft.name} autoFocus onChange={(event) => setDraft({ ...draft, name: event.target.value })} />
      </FormField>
      <FormField label="Description" required>
        <textarea className="form-control" rows={4} value={draft.description} onChange={(event) => setDraft({ ...draft, description: event.target.value })} />
      </FormField>
      <div className="row">
        <div className="col-md-6"><FormField label="Organisation">
          <input className="form-control" value={draft.organisation} onChange={(event) => setDraft({ ...draft, organisation: event.target.value })} />
        </FormField></div>
        <div className="col-md-6"><FormField label="Country">
          <SearchSelect options={[{ id: '', name: '— Continental —' }, ...countries.map((country) => ({ id: country.id, name: country.name }))]}
            value={draft.country_id} placeholder="— Continental —" onChange={(next) => setDraft({ ...draft, country_id: next })} />
        </FormField></div>
      </div>
      <label className="d-flex align-items-center mb-2" style={{ gap: 8, cursor: 'pointer', fontWeight: 500 }}>
        <input type="checkbox" checked={draft.is_public} onChange={(event) => setDraft({ ...draft, is_public: event.target.checked })} />
        Public — anyone can join instantly (private needs member approval)
      </label>
      <label className="d-flex align-items-center mb-0" style={{ gap: 8, cursor: 'pointer', fontWeight: 500 }}>
        <input type="checkbox" checked={draft.is_active} onChange={(event) => setDraft({ ...draft, is_active: event.target.checked })} />
        Active (visible on the portal)
      </label>
    </Modal>
  );
};
