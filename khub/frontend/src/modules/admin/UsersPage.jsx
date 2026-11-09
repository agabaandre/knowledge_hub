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
  const [creating, setCreating] = useState(false);
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
    <AdminShell title="Manage users" subtitle="Accounts, statuses, roles, and geographic scopes."
      actions={canEdit && (
        <button type="button" className="btn btn-sm theme-bg text-white" onClick={() => setCreating(true)}>
          <i className="fa fa-user-plus mr-1" aria-hidden="true" /> Create user
        </button>
      )}>
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
      {creating && (
        <UserCreateModal roles={roles} countries={lookups.data?.countries ?? []}
          onClose={() => setCreating(false)}
          onCreated={async (name) => { setCreating(false); setMessage({ ok: `${name} created (verified — admin-created accounts skip email verification).` }); await load(); }} />
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

/** Full edit form: profile, contact, role, country, status, optional new password. */
const UserEditModal = ({ user, roles, countries, onClose, onSaved }) => {
  const [draft, setDraft] = useState({
    first_name: user.firstName, last_name: user.lastName, email: user.email,
    phone_number: user.phone, organization_name: user.organisation, job_title: user.jobTitle,
    gender: user.gender || '', status: user.active, role_id: user.roleId ?? '', country_id: user.countryId ?? '',
    password: '',
  });
  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);
  const set = (key) => (event) => setDraft((previous) => ({ ...previous, [key]: event.target.value }));

  const save = async () => {
    setBusy(true);
    setError('');
    try {
      const body = {
        first_name: draft.first_name, last_name: draft.last_name, email: draft.email,
        phone_number: draft.phone_number || null, organization_name: draft.organization_name || null,
        job_title: draft.job_title || null, gender: draft.gender || null,
        status: draft.status,
        role_id: draft.role_id ? Number(draft.role_id) : null,
        country_id: draft.country_id ? Number(draft.country_id) : null,
      };
      if (draft.password.trim()) body.password = draft.password.trim();
      await apiFetch(`/api/admin/users/${user.id}`, { method: 'PUT', body });
      onSaved(user.name);
    } catch (err) {
      setError(err.details ? err.details.map((detail) => `${detail.path}: ${detail.message}`).join('; ') : err.message);
      setBusy(false);
    }
  };

  return (
    <Modal title={`Edit ${user.name}`} icon="fa-user-pen" onClose={onClose}
      footer={(
        <>
          <button type="button" className="btn btn-sm btn-outline-secondary" onClick={onClose}>Cancel</button>
          <button type="button" className="btn btn-sm theme-bg text-white" disabled={busy} onClick={save}>
            {busy ? 'Saving…' : 'Save changes'}
          </button>
        </>
      )}>
      {error && <div role="alert" className="alert alert-danger">{error}</div>}
      <div className="mb-3"><UserChip photo={user.photo} name={user.name} sub={user.email} size={40} /></div>
      <UserFormFields draft={draft} set={set} setDraft={setDraft} roles={roles} countries={countries}
        passwordHint="Leave blank to keep the current password." />
      <label className="mb-0 d-flex align-items-center" style={{ gap: 8, cursor: 'pointer', fontWeight: 500 }}>
        <input type="checkbox" checked={draft.status} onChange={(event) => setDraft({ ...draft, status: event.target.checked })} />
        Account active (may sign in)
      </label>
    </Modal>
  );
};

/** Create user — the Laravel add-user form; admin-created accounts are verified immediately. */
const UserCreateModal = ({ roles, countries, onClose, onCreated }) => {
  const [draft, setDraft] = useState({
    first_name: '', last_name: '', email: '', phone_number: '', organization_name: '',
    job_title: '', gender: '', role_id: '', country_id: '', password: '',
  });
  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);
  const set = (key) => (event) => setDraft((previous) => ({ ...previous, [key]: event.target.value }));

  const create = async () => {
    setBusy(true);
    setError('');
    try {
      await apiFetch('/api/admin/users', {
        method: 'POST',
        body: {
          first_name: draft.first_name, last_name: draft.last_name, email: draft.email, password: draft.password,
          phone_number: draft.phone_number || undefined, organization_name: draft.organization_name || undefined,
          job_title: draft.job_title || undefined,
          role_id: draft.role_id ? Number(draft.role_id) : undefined,
          country_id: draft.country_id ? Number(draft.country_id) : undefined,
        },
      });
      onCreated(`${draft.first_name} ${draft.last_name}`.trim());
    } catch (err) {
      setError(err.details ? err.details.map((detail) => `${detail.path}: ${detail.message}`).join('; ') : err.message);
      setBusy(false);
    }
  };

  return (
    <Modal title="Create a user" icon="fa-user-plus" onClose={onClose}
      footer={(
        <>
          <button type="button" className="btn btn-sm btn-outline-secondary" onClick={onClose}>Cancel</button>
          <button type="button" className="btn btn-sm theme-bg text-white"
            disabled={busy || !draft.first_name.trim() || !draft.last_name.trim() || !draft.email.trim() || draft.password.length < 8} onClick={create}>
            {busy ? 'Creating…' : 'Create user'}
          </button>
        </>
      )}>
      {error && <div role="alert" className="alert alert-danger">{error}</div>}
      <UserFormFields draft={draft} set={set} setDraft={setDraft} roles={roles} countries={countries}
        passwordRequired passwordHint="At least 8 characters — share it with the user securely." />
    </Modal>
  );
};

/** The shared field grid both user dialogs render. */
const UserFormFields = ({ draft, set, setDraft, roles, countries, passwordRequired = false, passwordHint }) => (
  <>
    <div className="row">
      <div className="col-md-6"><FormField label="First name" required>
        <input className="form-control" value={draft.first_name} onChange={set('first_name')} />
      </FormField></div>
      <div className="col-md-6"><FormField label="Last name" required>
        <input className="form-control" value={draft.last_name} onChange={set('last_name')} />
      </FormField></div>
    </div>
    <div className="row">
      <div className="col-md-7"><FormField label="Email" required>
        <input type="email" className="form-control" value={draft.email} onChange={set('email')} />
      </FormField></div>
      <div className="col-md-5"><FormField label="Phone">
        <input className="form-control" value={draft.phone_number} onChange={set('phone_number')} />
      </FormField></div>
    </div>
    <div className="row">
      <div className="col-md-6"><FormField label="Organisation">
        <input className="form-control" value={draft.organization_name} onChange={set('organization_name')} />
      </FormField></div>
      <div className="col-md-6"><FormField label="Job title">
        <input className="form-control" value={draft.job_title} onChange={set('job_title')} />
      </FormField></div>
    </div>
    <div className="row">
      <div className="col-md-4"><FormField label="Gender">
        <SearchSelect options={[{ id: '', name: '—' }, { id: 'Male', name: 'Male' }, { id: 'Female', name: 'Female' }, { id: 'Other', name: 'Other' }]}
          value={draft.gender} placeholder="—" onChange={(next) => setDraft((previous) => ({ ...previous, gender: next }))} />
      </FormField></div>
      <div className="col-md-4"><FormField label="Role">
        <SearchSelect options={[{ id: '', name: '— Member —' }, ...roles.map((role) => ({ id: role.id, name: role.name }))]}
          value={draft.role_id} placeholder="— Member —" onChange={(next) => setDraft((previous) => ({ ...previous, role_id: next }))} />
      </FormField></div>
      <div className="col-md-4"><FormField label="Country">
        <SearchSelect options={[{ id: '', name: '— None —' }, ...countries.map((country) => ({ id: country.id, name: country.name }))]}
          value={draft.country_id} placeholder="— None —" onChange={(next) => setDraft((previous) => ({ ...previous, country_id: next }))} />
      </FormField></div>
    </div>
    <FormField label={passwordRequired ? 'Password' : 'New password'} required={passwordRequired} hint={passwordHint}>
      <input type="password" className="form-control" autoComplete="new-password" value={draft.password} onChange={set('password')} />
    </FormField>
  </>
);
