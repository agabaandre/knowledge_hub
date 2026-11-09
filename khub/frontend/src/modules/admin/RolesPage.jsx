import { useEffect, useState } from 'react';
import { apiFetch } from '../../core/api.js';
import { usePermission, useSite } from '../../core/site-context.jsx';
import { AdminShell, AdminCard } from './AdminShell.jsx';
import { Flash } from '../../core/ui.jsx';

/**
 * Roles & permissions matrix. Every admin link/edit is a permission; any of
 * them can be granted to — or revoked from — any role, including Admin.
 */
export const RolesPage = () => {
  const { refresh } = useSite();
  const canEdit = usePermission('admin.roles.edit');
  const [data, setData] = useState(null);
  const [activeRole, setActiveRole] = useState(null);
  const [draft, setDraft] = useState(null);
  const [message, setMessage] = useState(null);
  const [busy, setBusy] = useState(false);

  const load = () =>
    apiFetch('/api/admin/roles').then((payload) => {
      setData(payload);
      const role = payload.roles.find((candidate) => candidate.id === activeRole?.id) ?? payload.roles[0];
      setActiveRole(role);
      setDraft(role ? { name: role.name, permissions: [...role.permissions] } : null);
    }).catch((err) => setMessage({ error: err.message }));
  useEffect(() => { load(); }, []); // eslint-disable-line

  const selectRole = (role) => {
    setActiveRole(role);
    setDraft({ name: role.name, permissions: [...role.permissions] });
    setMessage(null);
  };

  const togglePermission = (name) =>
    setDraft((previous) => ({
      ...previous,
      permissions: previous.permissions.includes(name) ? previous.permissions.filter((candidate) => candidate !== name) : [...previous.permissions, name],
    }));

  const save = async () => {
    setBusy(true);
    setMessage(null);
    try {
      await apiFetch(`/api/admin/roles/${activeRole.id}`, { method: 'PUT', body: draft });
      await load();
      await refresh();
      setMessage({ ok: `Role "${draft.name}" saved.` });
    } catch (err) {
      setMessage({ error: err.message });
    } finally {
      setBusy(false);
    }
  };

  const addRole = async () => {
    const name = window.prompt('Name for the new role:');
    if (!name) return;
    setBusy(true);
    try {
      await apiFetch('/api/admin/roles', { method: 'POST', body: { name, permissions: [] } });
      await load();
    } catch (err) {
      setMessage({ error: err.message });
    } finally {
      setBusy(false);
    }
  };

  const removeRole = async (role) => {
    if (!window.confirm(`Delete role "${role.name}"?`)) return;
    setBusy(true);
    try {
      await apiFetch(`/api/admin/roles/${role.id}`, { method: 'DELETE' });
      setActiveRole(null);
      await load();
    } catch (err) {
      setMessage({ error: err.message });
    } finally {
      setBusy(false);
    }
  };

  return (
    <AdminShell
      title="Roles & permissions"
      actions={canEdit && <button type="button" className="btn btn-sm btn-outline-secondary bg-white" onClick={addRole} disabled={busy}><i className="fa fa-plus mr-1" aria-hidden="true" /> Add role</button>}
    >
      <Flash message={message} />
      {!data && !message?.error && <p role="status">Loading…</p>}
      {data && draft && (
        <div className="row">
          <div className="col-lg-3 col-md-4 mb-3">
            <div className="bg-white rounded border p-2">
              {data.roles.map((role) => (
                <button key={role.id} type="button" onClick={() => selectRole(role)}
                  className={`btn w-100 text-left mb-1 ${activeRole?.id === role.id ? 'theme-bg text-white' : 'btn-light'}`}>
                  <span className="ft-medium d-block">{role.name}</span>
                  <small className={activeRole?.id === role.id ? 'text-white-50' : 'text-muted'}>{role.users} user(s) · {role.permissions.length} permission(s)</small>
                </button>
              ))}
            </div>
          </div>
          <div className="col-lg-9 col-md-8">
            <AdminCard>
              <div className="d-flex align-items-end justify-content-between flex-wrap mb-3" style={{ gap: 10 }}>
                <div className="form-group mb-0" style={{ minWidth: 260 }}>
                  <label htmlFor="role-name">Role name</label>
                  <input id="role-name" className="form-control" value={draft.name} disabled={!canEdit}
                    onChange={(event) => setDraft((previous) => ({ ...previous, name: event.target.value }))} />
                </div>
                {canEdit && (
                  <span className="d-inline-flex" style={{ gap: 8 }}>
                    <button type="button" className="btn btn-sm btn-outline-secondary" onClick={() => setDraft((previous) => ({ ...previous, permissions: [] }))}>Revoke all</button>
                    <button type="button" className="btn btn-sm btn-outline-danger" onClick={() => removeRole(activeRole)} disabled={busy}>Delete role</button>
                    <button type="button" className="btn btn-sm theme-bg text-white" onClick={save} disabled={busy}>{busy ? 'Saving…' : 'Save role'}</button>
                  </span>
                )}
              </div>
              {data.catalogue.map((group) => (
                <div key={group.group} className="mb-3">
                  <p className="text-uppercase ft-medium mb-2 pb-1" style={{ color: '#64748b', fontSize: '0.75rem', letterSpacing: '0.06em', borderBottom: '1px solid #e2e8f0' }}>{group.group}</p>
                  <div className="row">
                    {group.permissions.map((permission) => (
                      <div className="col-md-6 mb-1" key={permission.name}>
                        <label className="d-flex align-items-center mb-0" style={{ gap: 8, fontWeight: 400, cursor: canEdit ? 'pointer' : 'default' }}>
                          <input type="checkbox" disabled={!canEdit} checked={draft.permissions.includes(permission.name)} onChange={() => togglePermission(permission.name)} />
                          <span>{permission.label} <code className="text-muted" style={{ fontSize: '0.7rem' }}>{permission.name}</code></span>
                        </label>
                      </div>
                    ))}
                  </div>
                </div>
              ))}
            </AdminCard>
          </div>
        </div>
      )}
    </AdminShell>
  );
};
