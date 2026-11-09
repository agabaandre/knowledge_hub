import { useEffect, useState } from 'react';
import { Seo } from '../../core/Seo.jsx';
import { apiFetch } from '../../core/api.js';
import { formatBytes, formatNumber } from '../../core/format.js';
import { AdminShell } from './AdminShell.jsx';

const REFRESH_MS = 30000;

const Card = ({ label, value, sub }) => (
  <div className="col-sm-6 col-xl-3">
    <div className="card h-100 border-0 shadow-sm">
      <div className="card-body">
        <div className="small text-muted text-uppercase">{label}</div>
        <div className="fw-semibold" style={{ fontSize: '1.3rem' }}>{value}</div>
        {sub && <div className="small text-muted">{sub}</div>}
      </div>
    </div>
  </div>
);

const Bar = ({ percent, label }) => (
  <div className="mb-2">
    <div className="d-flex justify-content-between small"><span>{label}</span><span>{percent ?? 0}%</span></div>
    <div className="progress" role="progressbar" aria-label={label} aria-valuenow={percent ?? 0} aria-valuemin={0} aria-valuemax={100} style={{ height: 8 }}>
      <div className={`progress-bar ${percent > 90 ? 'bg-danger' : percent > 75 ? 'bg-warning' : 'bg-success'}`} style={{ width: `${Math.min(100, percent ?? 0)}%` }} />
    </div>
  </div>
);

/** Server and storage overview, refreshed every 30 seconds (same panels as the Laravel Storage Management page). */
export const MetricsPage = () => {
  const [snapshot, setSnapshot] = useState(null);
  const [error, setError] = useState('');

  const load = (fresh = false) =>
    apiFetch('/api/admin/metrics', { query: fresh ? { fresh: 1 } : {} })
      .then((data) => { setSnapshot(data); setError(''); })
      .catch((err) => setError(err.message));

  useEffect(() => {
    load();
    const timer = setInterval(() => load(), REFRESH_MS);
    return () => clearInterval(timer);
  }, []);

  if (error) return <AdminShell title="Server metrics"><div role="alert" className="alert alert-danger">{error}</div></AdminShell>;
  if (!snapshot) return <AdminShell title="Server metrics"><p role="status">Loading metrics…</p></AdminShell>;

  const { runtime, database, disk, uploads, backups, site } = snapshot;
  return (
    <AdminShell title="Server metrics" actions={<button className="btn btn-sm btn-outline-secondary bg-white" onClick={() => load(true)}>Refresh</button>}>
      <p className="small text-muted">Site <code>{site.id}</code> · storage <code>{site.paths.siteRoot}</code> · updated {new Date(snapshot.generatedAt).toLocaleTimeString()}</p>

      <div className="row g-3 mb-4">
        <Card label="Uptime" value={`${Math.round(runtime.uptimeSeconds / 60)} min`} sub={runtime.node} />
        <Card label="Database" value={database.status === 'ok' ? `OK · ${database.latencyMs} ms` : 'Error'} sub={database.version ?? database.message} />
        <Card label="Uploads" value={formatBytes(uploads.bytes)} sub={`${formatNumber(uploads.files)} files`} />
        <Card label="Backups" value={formatBytes(backups?.bytes)} sub={`${formatNumber(backups?.files)} files`} />
      </div>

      <div className="row g-3">
        <div className="col-lg-6">
          <div className="card border-0 shadow-sm"><div className="card-body">
            <h2 className="h6">Resources</h2>
            <Bar label="System memory" percent={runtime.systemMemory.usedPercent} />
            <Bar label="Host disk" percent={disk.usedPercent} />
            <p className="small mb-0">Process heap {formatBytes(runtime.processMemory.heapUsedBytes)} / {formatBytes(runtime.processMemory.heapTotalBytes)} · load {runtime.cpu.loadAverage.join(' / ')} on {runtime.cpu.cores} cores</p>
          </div></div>
        </div>
        <div className="col-lg-6">
          <div className="card border-0 shadow-sm"><div className="card-body">
            <h2 className="h6">Uploads by area</h2>
            <table className="table table-sm mb-0">
              <thead><tr><th scope="col">Area</th><th scope="col">Files</th><th scope="col">Size</th></tr></thead>
              <tbody>
                {Object.entries(uploads.areas).map(([name, usage]) => (
                  <tr key={name}><th scope="row" className="fw-normal">{name}</th><td>{formatNumber(usage.files)}</td><td>{formatBytes(usage.bytes)}</td></tr>
                ))}
              </tbody>
            </table>
          </div></div>
        </div>
      </div>
    </AdminShell>
  );
};
