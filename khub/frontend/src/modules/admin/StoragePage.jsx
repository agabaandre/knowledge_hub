import { useEffect, useState } from 'react';
import { apiFetch } from '../../core/api.js';
import { usePermission } from '../../core/site-context.jsx';
import { AdminShell, AdminCard } from './AdminShell.jsx';
import { DataTable } from '../../core/table.jsx';
import { Chart } from '../../core/Chart.jsx';
import { Flash, StatCard, StatusBadge, Switch } from '../../core/ui.jsx';
import { formatBytes, formatDate, formatNumber } from '../../core/format.js';

/** Storage Management: per-site usage breakdown (khubdata/<storageId>) and backup policy. */
export const StoragePage = () => {
  const canManage = usePermission('admin.storage.manage');
  const [sites, setSites] = useState(null);
  const [message, setMessage] = useState(null);
  const [busy, setBusy] = useState(false);

  const load = () => apiFetch('/api/admin/storage').then((payload) => setSites(payload.sites)).catch((err) => setMessage({ error: err.message }));
  useEffect(() => { load(); }, []);

  const saveBackup = async (site, body) => {
    setBusy(true);
    setMessage(null);
    try {
      await apiFetch(`/api/admin/storage/${site.storageId}`, { method: 'PUT', body });
      await load();
      setMessage({ ok: `Backup policy for "${site.name}" saved.` });
    } catch (err) {
      setMessage({ error: err.message });
    } finally {
      setBusy(false);
    }
  };

  return (
    <AdminShell title="Storage management" subtitle="File usage per hub site, with SQL backup policy.">
      <Flash message={message} />
      {!sites && !message?.error && <AdminCard><p role="status" className="mb-0">Loading…</p></AdminCard>}
      {sites?.map((site) => (
        <AdminCard key={site.id} title={`${site.name} ${site.current ? '(this site)' : ''}`}>
          <div className="row">
            <div className="col-md-3 col-6 mb-3"><StatCard value={formatBytes(site.totalBytes)} label="Total storage" icon="fa-hard-drive" /></div>
            <div className="col-md-3 col-6 mb-3"><StatCard value={formatNumber(site.totalFiles)} label="Files" icon="fa-file" /></div>
            <div className="col-md-3 col-6 mb-3">
              <div className="kh-admin-stat">
                <span className="kh-admin-stat-icon"><i className="fa fa-database" aria-hidden="true" /></span>
                <span className="kh-admin-stat-value" style={{ fontSize: '1.1rem' }}>
                  <StatusBadge tone={site.backup?.autoSqlBackup ? 'success' : 'neutral'}>{site.backup?.autoSqlBackup ? 'Auto backup on' : 'Auto backup off'}</StatusBadge>
                </span>
                <span className="kh-admin-stat-label">SQL backups {site.backup?.lastBackupAt ? `· last ${formatDate(site.backup.lastBackupAt)}` : ''}</span>
              </div>
            </div>
            <div className="col-md-3 col-6 mb-3 d-flex align-items-center" style={{ gap: 10 }}>
              {canManage && (
                <>
                  <Switch checked={site.backup?.autoSqlBackup ?? false} disabled={busy} label={`Auto SQL backup for ${site.name}`}
                    onChange={(next) => saveBackup(site, { auto_sql_backup: next })} />
                  <span className="text-muted" style={{ fontSize: '0.8rem' }}>Auto SQL backup · keeps {site.backup?.retentionDays ?? 14} day(s)</span>
                </>
              )}
            </div>
          </div>
          <div className="row">
            <div className="col-lg-5 mb-3">
              <Chart height={230} options={{
                chart: { type: 'bar' },
                title: { text: 'Usage by folder' },
                xAxis: { categories: site.folders.map((folder) => folder.label) },
                yAxis: { labels: { formatter() { return formatBytes(this.value); } } },
                legend: { enabled: false },
                tooltip: { pointFormatter() { return `<b>${formatBytes(this.y)}</b>`; } },
                series: [{ name: 'Bytes', data: site.folders.map((folder) => folder.bytes) }],
              }} />
            </div>
            <div className="col-lg-7">
              <DataTable
                title={`Folders — ${site.storageId}`}
                rows={site.folders}
                rowKey={(folder) => folder.path}
                emptyMessage="No files yet."
                columns={[
                  { key: 'label', label: 'Folder', render: (folder) => <><span className="ft-medium" style={{ color: '#18181b' }}>{folder.label}</span><small className="d-block text-muted"><code style={{ fontSize: '0.72rem' }}>{folder.path}</code></small></> },
                  { key: 'files', label: 'Files', align: 'center', render: (folder) => formatNumber(folder.files) },
                  { key: 'bytes', label: 'Size', align: 'end', nowrap: true, render: (folder) => formatBytes(folder.bytes) },
                ]}
              />
            </div>
          </div>
        </AdminCard>
      ))}
    </AdminShell>
  );
};
