import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { apiFetch } from '../../core/api.js';
import { useApi } from '../../core/hooks.js';
import { useSite } from '../../core/site-context.jsx';
import { withSitePrefix } from '@khub/shared/urls';
import { AdminShell, AdminCard } from './AdminShell.jsx';
import { SearchSelect } from '../../core/SearchSelect.jsx';
import { StatCard, Skeleton } from '../../core/ui.jsx';
import { formatNumber } from '../../core/format.js';

const PERIODS = [['7d', '7 days'], ['30d', '30 days'], ['90d', '90 days'], ['ytd', 'YTD'], ['all', 'All time']];

const Stat = (props) => <div className="col-lg-3 col-md-4 col-6 mb-3"><StatCard {...props} /></div>;

const Metric = ({ value, label }) => (
  <div className="col-md-3 col-6 mb-3">
    <div className="kh-admin-stat" style={{ padding: '14px 16px' }}>
      <span className="kh-admin-stat-value" style={{ fontSize: '1.45rem' }}>{formatNumber(value)}</span>
      <span className="kh-admin-stat-label">{label}</span>
    </div>
  </div>
);

/** Admin dashboard: stat cards + Analytics & Insights with period/country filters. */
export const DashboardPage = () => {
  const { manifest } = useSite();
  const { data, error, loading } = useApi('/api/admin/dashboard');
  const lookups = useApi('/api/publications/lookups');
  const to = (path) => withSitePrefix(manifest.site.pathPrefix, path);

  const [filters, setFilters] = useState({ period: '30d', from: '', to: '', country_id: '' });
  const [analytics, setAnalytics] = useState(null);
  const [analyticsError, setAnalyticsError] = useState('');

  const loadAnalytics = (next = filters) => {
    const query = { period: next.period };
    if (next.from) query.from = next.from;
    if (next.to) query.to = next.to;
    if (next.country_id) query.country_id = next.country_id;
    apiFetch('/api/admin/analytics', { query }).then(setAnalytics).catch((err) => setAnalyticsError(err.message));
  };
  useEffect(() => { loadAnalytics(); }, []); // eslint-disable-line

  const pendingTotal = data ? data.pending.publications + data.pending.forums + data.pending.comments : 0;

  return (
    <AdminShell title="Dashboard" subtitle="A live overview of content, people, and activity across the hub.">
      {error && <div className="alert alert-danger" role="alert">{error.message}</div>}
      {loading && <AdminCard><Skeleton lines={4} /></AdminCard>}
      {data && (
        <>
          <div className="row">
            <Stat value={data.content.publications} label="Publications" icon="fa-file-lines" link={to('/admin/publications')} linkLabel="Manage" />
            <Stat value={data.content.authors ?? 0} label="Resource authors" icon="fa-users" link={to('/authors')} linkLabel="View list" />
            <Stat value={data.content.forums} label="Forums" icon="fa-comments" link={to('/forums')} linkLabel="View list" />
            <Stat value={data.content.communities} label="Communities" icon="fa-people-group" link={to('/communities')} linkLabel="View list" />
            <Stat value={data.content.memberStates ?? 0} label="Member states" icon="fa-earth-africa" link={to('/countries')} linkLabel="View list" />
            <Stat value={data.users.total} label="Users" icon="fa-user" link={to('/admin/users')} linkLabel="Manage" />
            <Stat value={pendingTotal} label="Pending approvals" icon="fa-inbox" link={to('/admin/approvals')} linkLabel="Review" />
            <Stat value={data.activity.searchesLast30Days} label="Searches (30 days)" icon="fa-magnifying-glass-chart" link={to('/admin/logs')} linkLabel="View logs" />
          </div>

          <AdminCard>
            <div className="d-flex align-items-center justify-content-between flex-wrap mb-3" style={{ gap: 10 }}>
              <div>
                <h2 className="ft-bold mb-0" style={{ fontSize: '1.15rem' }}><i className="fa fa-chart-column theme-cl mr-1" aria-hidden="true" /> Analytics &amp; Insights</h2>
                <small className="text-muted">Portal activity, registrations, and member-state indicators</small>
              </div>
              <div className="d-flex align-items-center flex-wrap" style={{ gap: 6 }}>
                {PERIODS.map(([value, label]) => (
                  <button key={value} type="button"
                    className={`btn btn-sm ${filters.period === value && !filters.from && !filters.to ? 'theme-bg text-white' : 'btn-outline-secondary bg-white'}`}
                    onClick={() => { const next = { ...filters, period: value, from: '', to: '' }; setFilters(next); loadAnalytics(next); }}>
                    {label}
                  </button>
                ))}
                <input type="date" className="form-control form-control-sm" style={{ width: 150 }} value={filters.from} aria-label="From date" onChange={(event) => setFilters({ ...filters, from: event.target.value })} />
                <input type="date" className="form-control form-control-sm" style={{ width: 150 }} value={filters.to} aria-label="To date" onChange={(event) => setFilters({ ...filters, to: event.target.value })} />
                <div style={{ width: 190 }}>
                  <SearchSelect options={[{ id: '', name: 'All Countries' }, ...(lookups.data?.countries ?? []).map((country) => ({ id: country.id, name: country.name }))]}
                    value={filters.country_id} placeholder="All Countries" onChange={(next) => setFilters({ ...filters, country_id: next })} />
                </div>
                <button type="button" className="btn btn-sm theme-bg text-white" onClick={() => loadAnalytics()}>
                  <i className="fa fa-filter mr-1" aria-hidden="true" /> Apply
                </button>
              </div>
            </div>
            {analyticsError && <div className="alert alert-warning">{analyticsError}</div>}
            {analytics && (
              <div className="row">
                <Metric value={analytics.visits} label="Searches (period)" />
                <Metric value={analytics.signups} label="Signups (period)" />
                <Metric value={analytics.newPublications} label="New publications (period)" />
                <Metric value={analytics.signupCountries} label="Countries (signups)" />
              </div>
            )}
            {analytics && <small className="text-muted">Resource views, all time: {formatNumber(analytics.resourceViewsAllTime)}. Page-level visit tracking is not ported yet; "searches" reflect recorded search activity.</small>}
          </AdminCard>

          <AdminCard title="Needs attention">
            {pendingTotal === 0 ? (
              <p className="mb-0 text-muted">Nothing is waiting for review.</p>
            ) : (
              <p className="mb-0">{pendingTotal} item{pendingTotal === 1 ? '' : 's'} awaiting moderation — <Link to={to('/admin/approvals')} className="theme-cl ft-medium">open the approvals inbox</Link>.</p>
            )}
          </AdminCard>
        </>
      )}
    </AdminShell>
  );
};
