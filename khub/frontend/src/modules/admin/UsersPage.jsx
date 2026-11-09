import { useEffect, useState } from 'react';
import { apiFetch } from '../../core/api.js';
import { useApi } from '../../core/hooks.js';
import { usePermission } from '../../core/site-context.jsx';
import { AdminShell } from './AdminShell.jsx';
import { SearchSelect } from '../../core/SearchSelect.jsx';
import { DataTable } from '../../core/table.jsx';
import { Modal, FormField, StatusBadge, Flash, UserChip } from '../../core/ui.jsx';
import { formatDate } from '../../core/format.js';

/** Manage Users: search + table; status, role, and country are edited in a modal. */
export const UsersPage = () => {
  const canEdit = usePermission('admin.users.edit');
  const [term, setTerm] = useState('');
  const [query, setQuery] = useState({ page: 1 });
  const [data, setData] = useState(null);
  const [roles, setRoles] = useState([]);
  const [editing, setEditing] = useState(null);
  const [message, setMessage] = useState(null);
  const lookups = useApi('/api/publications/lookups');

  const load = () =>
    apiFetch('/api/admin/users', { query: { ...query, per_page: 15 } }).then(setData).catch((err) => setMessage({ error: err.message }));
  useEffect(() => { load(); }, [query]); // eslint-disable-line
  useEffect(() => {
    apiFetch('/api/admin/roles').then((payload) => setRoles(payload.roles)).catch(() => setRoles([]));
  }, []);

  const columns = [
    {
      key: 'user',
      label: 'User',
      render: (user) => (
        <UserChip photo={user.photo} name={user.name} sub={user.email} />
      ),
    },
    { key: 'role', label: 'Role', render: (user) => user.role || <span className="text-muted">Member</span> },
    { key: 'country', label: 'Country', render: (user) => user.country || <span className="text-muted">—</span> },
    { key: 'lastLogin', label: 'Last login', nowrap: true, render: (user) => <span className="text-muted">{user.lastLoginAt ? formatDate(user.lastLoginAt) : 'never'}</span> },
    { key: 'status', label: 'Status', align: 'center', render: (user) => <StatusBadge tone={user.active ? 'success' : 'neutral'}>{user.active ? 'Active' : 'Inactive'}</StatusBadge> },
    ...(canEdit ? [{
      key: 'actions',
      label: 'Actions',
      align: 'end',
      render: (user) => (
        <button type="button" className="btn btn-sm btn-outline-secondary" onClick={() => setEditing(user)} aria-label={`Edit ${user.name}`}>
          <i className="fa fa-pen" aria-hidden="true" /> Edit
        </button>
      ),
    }] : []),
  ];

  return (
    <AdminShell title="Manage users" subtitle="Accounts, statuses, roles, and geographic scopes.">
      <Flash message={message} />
      {!data && !message?.error && <p role="status">Loading…</p>}
      {data && (
        <DataTable
          title="Users"
          description="Role and country control what each account may see and approve."
          columns={columns}
          rows={data.data}
          showRowNumbers
          page={data.meta.page}
          perPage={data.meta.perPage ?? 15}
          totalPages={data.meta.lastPage}
          total={data.meta.total}
          onPageChange={(page) => setQuery((previous) => ({ ...previous, page }))}
          emptyMessage="No users match this search."
          toolbar={(
            <form className="d-flex" style={{ gap: 6 }} role="search"
              onSubmit={(event) => { event.preventDefault(); setQuery({ page: 1, term: term.trim() }); }}>
              <input className="form-control form-control-sm" type="search" style={{ width: 220 }} placeholder="Search name or email…"
                value={term} onChange={(event) => setTerm(event.target.value)} aria-label="Search users" />
              <button type="submit" className="btn btn-sm theme-bg text-white"><i className="fa fa-magnifying-glass" aria-hidden="true" /></button>
            </form>
          )}
        />
      )}
      {editing && (
        <UserEditModal
          user={editing}
          roles={roles}
          countries={lookups.data?.countries ?? []}
          onClose={() => setEditing(null)}
          onSaved={async (name) => {
            setEditing(null);
            setMessage({ ok: `${name} updated.` });
            await load();
          }}
        />
      )}
    </AdminShell>
  );
};

/** Individual edit form: status, role, and country scope in one dialog. */
const UserEditModal = ({ user, roles, countries, onClose, onSaved }) => {
  const [draft, setDraft] = useState({ status: user.active, role_id: user.roleId ?? '', country_id: user.countryId ?? '' });
  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);

  const save = async () => {
    setBusy(true);
    setError('');
    try {
      await apiFetch(`/api/admin/users/${user.id}`, {
        method: 'PUT',
        body: {
          status: draft.status,
          role_id: draft.role_id ? Number(draft.role_id) : null,
          country_id: draft.country_id ? Number(draft.country_id) : null,
        },
      });
      onSaved(user.name);
    } catch (err) {
      setError(err.message);
      setBusy(false);
    }
  };

  return (
    <Modal
      title={`Edit ${user.name}`}
      icon="fa-user-pen"
      onClose={onClose}
      footer={(
        <>
          <button type="button" className="btn btn-sm btn-outline-secondary" onClick={onClose}>Cancel</button>
          <button type="button" className="btn btn-sm theme-bg text-white" disabled={busy} onClick={save}>
            {busy ? 'Saving…' : 'Save changes'}
          </button>
        </>
      )}
    >
      {error && <div role="alert" className="alert alert-danger">{error}</div>}
      <div className="mb-3"><UserChip photo={user.photo} name={user.name} sub={user.email} size={40} /></div>
      <FormField label="Role" hint="Determines the account's admin permissions and access level.">
        <SearchSelect options={[{ id: '', name: '— Member (no role) —' }, ...roles.map((role) => ({ id: role.id, name: role.name }))]}
          value={draft.role_id} placeholder="— Member —" onChange={(next) => setDraft({ ...draft, role_id: next })} />
      </FormField>
      <FormField label="Country" hint="Scopes country-level moderators to this member state.">
        <SearchSelect options={[{ id: '', name: '— None —' }, ...countries.map((country) => ({ id: country.id, name: country.name }))]}
          value={draft.country_id} placeholder="— None —" onChange={(next) => setDraft({ ...draft, country_id: next })} />
      </FormField>
      <label className="mb-0 d-flex align-items-center" style={{ gap: 8, cursor: 'pointer', fontWeight: 500 }}>
        <input type="checkbox" checked={draft.status} onChange={(event) => setDraft({ ...draft, status: event.target.checked })} />
        Account active (may sign in)
      </label>
    </Modal>
  );
};
