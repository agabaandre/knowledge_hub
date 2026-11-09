import { useEffect, useState } from 'react';
import { apiFetch } from '../../core/api.js';
import { AdminShell, AdminCard } from './AdminShell.jsx';
import { AfricaMapChart } from '../../core/Chart.jsx';
import { Flash, StatCard } from '../../core/ui.jsx';

const METRICS = [
  ['publications', 'Approved resources', 'fa-file-lines'],
  ['users', 'Registered users', 'fa-users'],
];

/** Maps Management: member-state activity on the Africa map (Highcharts, no credits). */
export const MapsPage = () => {
  const [metric, setMetric] = useState('publications');
  const [geo, setGeo] = useState(null);
  const [message, setMessage] = useState(null);

  useEffect(() => {
    apiFetch('/api/admin/analytics/geo').then(setGeo).catch((err) => setMessage({ error: err.message }));
  }, []);

  const rows = geo?.[metric] ?? [];
  const mapData = rows.map((row) => [row.iso, row.total]);
  const label = METRICS.find(([value]) => value === metric)?.[1] ?? metric;

  return (
    <AdminShell title="Maps management" subtitle="Where resources and users come from, across the member states.">
      <Flash message={message} />
      {geo && (
        <div className="row">
          <div className="col-md-4 col-6 mb-3"><StatCard value={geo.memberStates} label="Member states" icon="fa-earth-africa" /></div>
          <div className="col-md-4 col-6 mb-3"><StatCard value={geo.publications.reduce((sum, row) => sum + row.total, 0)} label="Resources with coverage" icon="fa-file-lines" /></div>
          <div className="col-md-4 col-6 mb-3"><StatCard value={geo.users.reduce((sum, row) => sum + row.total, 0)} label="Users with a country" icon="fa-users" /></div>
        </div>
      )}
      <AdminCard
        title="Africa map"
        actions={(
          <span className="d-inline-flex" style={{ gap: 4 }}>
            {METRICS.map(([value, text, icon]) => (
              <button key={value} type="button" onClick={() => setMetric(value)}
                className={`btn btn-sm ${metric === value ? 'theme-bg text-white' : 'btn-outline-secondary'}`}>
                <i className={`fa ${icon} mr-1`} aria-hidden="true" /> {text}
              </button>
            ))}
          </span>
        )}
      >
        {!geo && !message?.error && <p role="status" className="mb-0">Loading…</p>}
        {geo && <AfricaMapChart data={mapData} seriesName={label} />}
        {geo && rows.length > 0 && (
          <p className="text-muted mb-0 mt-2" style={{ fontSize: '0.8rem' }}>
            Top: {rows.slice().sort((a, b) => b.total - a.total).slice(0, 5).map((row) => `${row.name} (${row.total})`).join(' · ')}
          </p>
        )}
      </AdminCard>
    </AdminShell>
  );
};
