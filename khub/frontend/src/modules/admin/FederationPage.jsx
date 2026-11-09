import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { apiFetch } from '../../core/api.js';
import { useSite, usePermission } from '../../core/site-context.jsx';
import { withSitePrefix } from '@khub/shared/urls';
import { AdminShell, AdminCard } from './AdminShell.jsx';
import { DataTable } from '../../core/table.jsx';
import { Modal, FormField, Flash, StatusBadge, StatCard, Switch } from '../../core/ui.jsx';
import { formatDate, formatNumber } from '../../core/format.js';

const DIRECTIONS = [['both', 'Pull & push'], ['pull', 'Pull only'], ['push', 'Push only']];

/** Federated Knowledge Hubs: connect partner hubs, test the link, and sync shared content. */
export const FederationPage = () => {
  const { manifest } = useSite();
  const canManage = usePermission('admin.federation.manage');
  const to = (path) => withSitePrefix(manifest.site.pathPrefix, path);
  const [payload, setPayload] = useState(null);
  const [editing, setEditing] = useState(null); // null | {} (add) | hub (edit)
  const [message, setMessage] = useState(null);
  const [busy, setBusy] = useState(0);

  const load = () => apiFetch('/api/admin/federation/hubs').then(setPayload).catch((err) => setMessage({ error: err.message }));
  useEffect(() => { load(); }, []);

  const act = async (hubId, run, ok) => {
    setBusy(hubId);
    setMessage(null);
    try {
      const result = await run();
      await load();
      setMessage({ ok: typeof ok === 'function' ? ok(result) : ok });
    } catch (err) {
      setMessage({ error: err.message });
    } finally {
      setBusy(0);
    }
  };

  const test = (hub) => act(hub.id, () => apiFetch(`/api/admin/federation/hubs/${hub.id}/test`, { method: 'POST' }),
    (result) => `Connected to "${result.manifest.hub}" (${result.manifest.protocol}).`);
  const sync = (hub) => act(hub.id, () => apiFetch(`/api/admin/federation/hubs/${hub.id}/sync`, { method: 'POST' }),
    (result) => result.summary);
  const remove = (hub) => window.confirm(`Disconnect "${hub.name}"? Already-imported content stays.`)
    && act(hub.id, () => apiFetch(`/api/admin/federation/hubs/${hub.id}`, { method: 'DELETE' }), `"${hub.name}" disconnected.`);

  return (
    <AdminShell title="Federated knowledge hubs"
      subtitle="Share approved resources with partner hubs — pull theirs in, push yours out."
      actions={canManage && (
        <button type="button" className="btn btn-sm theme-bg text-white" onClick={() => setEditing({})}>
          <i className="fa fa-plus mr-1" aria-hidden="true" /> Connect a hub
        </button>
      )}>
      <Flash message={message} />
      {payload && (
        <div className="row">
          <div className="col-md-4 col-6 mb-3"><StatCard value={payload.hubs.length} label="Connected hubs" icon="fa-circle-nodes" /></div>
          <div className="col-md-4 col-6 mb-3"><StatCard value={payload.importedPublications} label="Imported resources" icon="fa-cloud-arrow-down" /></div>
          <div className="col-md-4 col-12 mb-3">
            <div className="kh-admin-stat">
              <span className="kh-admin-stat-icon"><i className="fa fa-key" aria-hidden="true" /></span>
              <span className="kh-admin-stat-label" style={{ marginTop: 4 }}>
                Partners authenticate with this hub's federation token —
                set it under <Link to={to('/admin/configure')}>Configure → Advanced</Link>.
              </span>
            </div>
          </div>
        </div>
      )}
      {!payload && !message?.error && <AdminCard><p role="status" className="mb-0">Loading…</p></AdminCard>}
      {payload && (
        <DataTable
          title="Partner hubs"
          description="Pull imports their approved resources here; push sends ours to them. Imported records keep their origin."
          rows={payload.hubs}
          showRowNumbers
          emptyMessage="No federated hubs connected yet."
          emptyIcon="fa-circle-nodes"
          columns={[
            {
              key: 'name',
              label: 'Hub',
              render: (hub) => (
                <>
                  <span className="ft-medium" style={{ color: '#18181b' }}>{hub.name}</span>
                  <small className="d-block text-muted"><code style={{ fontSize: '0.72rem' }}>{hub.baseUrl}</code></small>
                </>
              ),
            },
            { key: 'direction', label: 'Direction', align: 'center', render: (hub) => <StatusBadge tone="neutral">{DIRECTIONS.find(([v]) => v === hub.direction)?.[1] ?? hub.direction}</StatusBadge> },
            {
              key: 'status',
              label: 'Last status',
              render: (hub) => hub.lastStatus
                ? <small className={hub.lastStatus.includes('failed') ? 'text-danger' : 'text-muted'}>{hub.lastStatus}</small>
                : <span className="text-muted">—</span>,
            },
            { key: 'synced', label: 'Last sync', nowrap: true, render: (hub) => (hub.lastSyncedAt ? formatDate(hub.lastSyncedAt) : <span className="text-muted">never</span>) },
            {
              key: 'active',
              label: 'Active',
              align: 'center',
              render: (hub) => <Switch checked={hub.isActive} disabled={!canManage || busy === hub.id} label={`Activate ${hub.name}`}
                onChange={(next) => act(hub.id, () => apiFetch(`/api/admin/federation/hubs/${hub.id}`, { method: 'PUT', body: { is_active: next } }), `"${hub.name}" ${next ? 'activated' : 'deactivated'}.`)} />,
            },
            ...(canManage ? [{
              key: 'actions',
              label: 'Actions',
              align: 'end',
              render: (hub) => (
                <span className="kh-table-actions">
                  <button type="button" className="btn btn-sm btn-outline-secondary" disabled={busy === hub.id} onClick={() => test(hub)}>
                    <i className="fa fa-plug mr-1" aria-hidden="true" /> Test
                  </button>
                  <button type="button" className="btn btn-sm theme-bg text-white" disabled={busy === hub.id || !hub.isActive} onClick={() => sync(hub)}>
                    <i className={`fa ${busy === hub.id ? 'fa-spinner fa-spin' : 'fa-rotate'} mr-1`} aria-hidden="true" /> Sync now
                  </button>
                  <button type="button" className="btn btn-sm btn-outline-secondary" disabled={busy === hub.id} onClick={() => setEditing(hub)} aria-label={`Edit ${hub.name}`}>
                    <i className="fa fa-pen" aria-hidden="true" />
                  </button>
                  <button type="button" className="btn btn-sm btn-outline-danger" disabled={busy === hub.id} onClick={() => remove(hub)} aria-label={`Disconnect ${hub.name}`}>
                    <i className="fa fa-link-slash" aria-hidden="true" />
                  </button>
                </span>
              ),
            }] : []),
          ]}
        />
      )}
      {editing !== null && (
        <HubModal hub={editing.id ? editing : null} onClose={() => setEditing(null)}
          onSaved={async (ok) => { setEditing(null); setMessage({ ok }); await load(); }} />
      )}
    </AdminShell>
  );
};

/** Individual form: connect or edit one partner hub. */
const HubModal = ({ hub, onClose, onSaved }) => {
  const [draft, setDraft] = useState({
    name: hub?.name ?? '',
    base_url: hub?.baseUrl ?? '',
    api_token: '',
    direction: hub?.direction ?? 'both',
  });
  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);
  const isEdit = Boolean(hub);

  const save = async () => {
    setBusy(true);
    setError('');
    try {
      const body = { name: draft.name.trim(), base_url: draft.base_url.trim(), direction: draft.direction };
      if (draft.api_token.trim()) body.api_token = draft.api_token.trim();
      if (isEdit) await apiFetch(`/api/admin/federation/hubs/${hub.id}`, { method: 'PUT', body });
      else await apiFetch('/api/admin/federation/hubs', { method: 'POST', body: { ...body, api_token: draft.api_token.trim() } });
      onSaved(`"${draft.name}" ${isEdit ? 'updated' : 'connected'} — use Test to verify the link.`);
    } catch (err) {
      setError(err.details ? err.details.map((detail) => detail.message).join(' ') : err.message);
      setBusy(false);
    }
  };

  return (
    <Modal title={isEdit ? `Edit ${hub.name}` : 'Connect a federated hub'} icon="fa-circle-nodes" onClose={onClose}
      footer={(
        <>
          <button type="button" className="btn btn-sm btn-outline-secondary" onClick={onClose}>Cancel</button>
          <button type="button" className="btn btn-sm theme-bg text-white"
            disabled={busy || !draft.name.trim() || !draft.base_url.trim() || (!isEdit && draft.api_token.trim().length < 8)} onClick={save}>
            {busy ? 'Saving…' : isEdit ? 'Save changes' : 'Connect hub'}
          </button>
        </>
      )}>
      {error && <div role="alert" className="alert alert-danger">{error}</div>}
      <FormField label="Name" required>
        <input className="form-control" value={draft.name} autoFocus onChange={(event) => setDraft({ ...draft, name: event.target.value })} />
      </FormField>
      <FormField label="Base URL" required hint="The partner hub's root address, e.g. https://khub.partner.org">
        <input className="form-control" value={draft.base_url} placeholder="https://…" onChange={(event) => setDraft({ ...draft, base_url: event.target.value })} />
      </FormField>
      <FormField label={isEdit ? 'API token (leave blank to keep the saved one)' : 'API token'} required={!isEdit}
        hint="The federation token the partner hub gave you (their Configure → Advanced value).">
        <input type="password" className="form-control" autoComplete="new-password" value={draft.api_token}
          onChange={(event) => setDraft({ ...draft, api_token: event.target.value })} />
      </FormField>
      <FormField label="Direction">
        <span className="d-inline-flex" style={{ gap: 4 }}>
          {DIRECTIONS.map(([value, label]) => (
            <button key={value} type="button" onClick={() => setDraft({ ...draft, direction: value })}
              className={`btn btn-sm ${draft.direction === value ? 'theme-bg text-white' : 'btn-outline-secondary'}`}>{label}</button>
          ))}
        </span>
      </FormField>
    </Modal>
  );
};
