import { useEffect, useState } from 'react';
import { apiFetch } from '../../core/api.js';
import { usePermission } from '../../core/site-context.jsx';
import { AdminShell, AdminCard } from './AdminShell.jsx';
import { DataTable } from '../../core/table.jsx';
import { Modal, FormField, Flash, StatusBadge, Switch } from '../../core/ui.jsx';
import { SearchSelect } from '../../core/SearchSelect.jsx';
import { useApi } from '../../core/hooks.js';

/** Hub Sites: the multi-site registry — create sites and switch them on/off. */
export const SitesPage = () => {
  const canManage = usePermission('admin.sites.manage');
  const languages = useApi('/api/admin/languages');
  const [sites, setSites] = useState(null);
  const [creating, setCreating] = useState(false);
  const [message, setMessage] = useState(null);
  const [busy, setBusy] = useState(false);

  const load = () => apiFetch('/api/admin/sites').then((payload) => setSites(payload.sites)).catch((err) => setMessage({ error: err.message }));
  useEffect(() => { load(); }, []);

  const toggle = async (site, enabled) => {
    setBusy(true);
    setMessage(null);
    try {
      await apiFetch(`/api/admin/sites/${site.id}`, { method: 'PUT', body: { enabled } });
      await load();
      setMessage({ ok: `"${site.name}" ${enabled ? 'enabled' : 'disabled'}.` });
    } catch (err) {
      setMessage({ error: err.message });
    } finally {
      setBusy(false);
    }
  };

  return (
    <AdminShell title="Hub sites" subtitle="One hub, many sites — khub.org, khub.org/nigeria, and so on. Each site has its own storage and settings.">
      <Flash message={message} />
      {!sites && !message?.error && <AdminCard><p role="status" className="mb-0">Loading…</p></AdminCard>}
      {sites && (
        <DataTable
          title="Sites"
          description="New sites inherit the default database until their own connection is configured in sites/<id>/site.json."
          rows={sites}
          rowKey={(site) => site.id}
          showRowNumbers
          emptyMessage="No sites configured."
          toolbar={canManage && (
            <button type="button" className="btn btn-sm theme-bg text-white" onClick={() => setCreating(true)}>
              <i className="fa fa-plus mr-1" aria-hidden="true" /> Create site
            </button>
          )}
          columns={[
            {
              key: 'name',
              label: 'Site',
              render: (site) => (
                <>
                  <span className="ft-medium" style={{ color: '#18181b' }}>{site.name}</span>
                  {site.current && <StatusBadge tone="success">current</StatusBadge>}
                  <small className="d-block text-muted"><code style={{ fontSize: '0.72rem' }}>{site.id}</code></small>
                </>
              ),
            },
            { key: 'prefix', label: 'Path / hosts', render: (site) => <code style={{ fontSize: '0.78rem' }}>{site.pathPrefix || site.hosts.join(', ') || '/'}</code> },
            { key: 'storage', label: 'Storage', render: (site) => <code style={{ fontSize: '0.78rem' }}>khubdata/{site.storageId}</code> },
            { key: 'locales', label: 'Languages', align: 'center', render: (site) => site.locales.join(', ') },
            {
              key: 'enabled',
              label: 'Enabled',
              align: 'center',
              render: (site) => <Switch checked={site.enabled} disabled={!canManage || busy || site.id === 'default'} label={`Enable ${site.name}`}
                onChange={(next) => toggle(site, next)} />,
            },
          ]}
        />
      )}
      {creating && (
        <CreateSiteModal languages={languages.data?.languages ?? []} onClose={() => setCreating(false)}
          onCreated={async (result) => {
            setCreating(false);
            setMessage({ ok: `Site "${result.id}" created and serving at ${result.pathPrefix}. Point its database in sites/${result.id}/site.json when it needs its own.` });
            await load();
          }} />
      )}
    </AdminShell>
  );
};

/** Individual form: provision a new site (config + storage folders, live immediately). */
const CreateSiteModal = ({ languages, onClose, onCreated }) => {
  const [draft, setDraft] = useState({ id: '', name: '', pathPrefix: '', locales: ['en'] });
  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);

  const create = async () => {
    setBusy(true);
    setError('');
    try {
      const result = await apiFetch('/api/admin/sites', {
        method: 'POST',
        body: {
          id: draft.id.trim(),
          name: draft.name.trim(),
          pathPrefix: draft.pathPrefix.trim() || undefined,
          locales: draft.locales,
        },
      });
      onCreated(result);
    } catch (err) {
      setError(err.details ? err.details.map((detail) => detail.message).join(' ') : err.message);
      setBusy(false);
    }
  };

  return (
    <Modal title="Create a hub site" icon="fa-sitemap" onClose={onClose}
      footer={(
        <>
          <button type="button" className="btn btn-sm btn-outline-secondary" onClick={onClose}>Cancel</button>
          <button type="button" className="btn btn-sm theme-bg text-white" disabled={busy || !draft.id.trim() || draft.name.trim().length < 3} onClick={create}>
            {busy ? 'Creating…' : 'Create site'}
          </button>
        </>
      )}>
      {error && <div role="alert" className="alert alert-danger">{error}</div>}
      <FormField label="Site id" required hint="Lowercase letters, digits, dashes — becomes the folder and storage name (e.g. nigeria).">
        <input className="form-control" value={draft.id} autoFocus onChange={(event) => setDraft({ ...draft, id: event.target.value })} />
      </FormField>
      <FormField label="Site name" required>
        <input className="form-control" value={draft.name} onChange={(event) => setDraft({ ...draft, name: event.target.value })} />
      </FormField>
      <FormField label="Path prefix" hint="Defaults to /<id>, e.g. /nigeria → khub.org/nigeria.">
        <input className="form-control" value={draft.pathPrefix} placeholder="/nigeria" onChange={(event) => setDraft({ ...draft, pathPrefix: event.target.value })} />
      </FormField>
      <FormField label="Languages" hint="The site's available languages — the same list as Site Languages.">
        <SearchSelect multiple
          options={languages.map((lang) => ({ id: lang.locale_code, name: `${lang.flag_emoji ?? ''} ${lang.name} (${lang.locale_code})`.trim() }))}
          value={draft.locales} placeholder="Select languages"
          onChange={(next) => setDraft({ ...draft, locales: next.length ? next : ['en'] })} />
      </FormField>
    </Modal>
  );
};
