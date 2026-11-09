import { useState } from 'react';
import { Link, useParams } from 'react-router-dom';
import { Seo } from '../../core/Seo.jsx';
import { useApi } from '../../core/hooks.js';
import { useSite } from '../../core/site-context.jsx';
import { withSitePrefix } from '@khub/shared/urls';
import { Page, Loading } from '../../core/Page.jsx';
import { formatDate, formatNumber } from '../../core/format.js';
import { FeedCard } from '../publications/FeedCard.jsx';
import { Avatar } from '../../core/Avatar.jsx';

/** Badge pill, coloured from the admin-configured badge_types row. */
export const BadgePill = ({ badge, size = 'sm' }) => {
  if (!badge) return null;
  return (
    <span className="d-inline-flex align-items-center ft-medium rounded"
      style={{ gap: 5, background: badge.color, color: '#1e293b', padding: size === 'sm' ? '3px 10px' : '5px 12px', fontSize: size === 'sm' ? '0.78rem' : '0.9rem' }}>
      <i className="fa fa-gem" aria-hidden="true" /> {badge.name}
    </span>
  );
};

const ContributorPhoto = ({ contributor, size = 72 }) =>
  contributor.photo || !contributor.isOrganisation ? (
    <Avatar photo={contributor.photo} name={contributor.name} size={size} />
  ) : (
    <span className="d-inline-flex align-items-center justify-content-center rounded border" aria-hidden="true"
      style={{ width: size, height: size, background: '#f8fafc' }}>
      <i className="fa fa-building theme-cl" style={{ fontSize: size * 0.42 }} />
    </span>
  );

/** Contributors & Authors grid — the production /browse/authors page. */
export const AuthorsPage = () => {
  const { manifest } = useSite();
  const [term, setTerm] = useState('');
  const [query, setQuery] = useState({ page: 1 });
  const { data, error, loading } = useApi('/api/authors', { ...query, per_page: 12 });
  const to = (path) => withSitePrefix(manifest.site.pathPrefix, path);
  const total = data?.meta?.total ?? 0;

  return (
    <div className="gray py-4">
      <Seo title="Contributors & Authors" path="/authors"
        description={`Discover researchers, clinicians, ministries, and institutions sharing verified public health publications, resources, and forum expertise on ${manifest.settings.site_name}.`} />
      <div className="container">
        <div className="text-center mb-4">
          <h1 className="ft-bold mb-2" style={{ fontSize: '2rem', color: '#2d3748' }}>
            <i className="fa fa-users mr-2" aria-hidden="true" />Contributors &amp; Authors <span className="text-muted ft-regular">({formatNumber(total)} total)</span>
          </h1>
          <p className="text-muted mx-auto mb-0" style={{ maxWidth: 640 }}>
            Discover researchers, clinicians, ministries, and institutions sharing verified public health publications, resources, and forum expertise across Africa.
          </p>
        </div>

        <form className="row no-gutters bg-white rounded border mb-4 mx-auto" style={{ maxWidth: 760 }} role="search"
          onSubmit={(event) => { event.preventDefault(); setQuery({ page: 1, term: term.trim() }); }}>
          <div className="col-md-9 col-12">
            <label htmlFor="contrib-term" className="sr-only">Search contributors</label>
            <input id="contrib-term" className="form-control border-0 main-search" type="search" value={term}
              onChange={(event) => setTerm(event.target.value)} placeholder="Search contributors and institutions…" />
          </div>
          <div className="col-md-3 col-12">
            <button className="btn theme-bg text-white full-width" type="submit" style={{ minHeight: 54, width: '100%' }}>
              <i className="fa fa-magnifying-glass mr-1" aria-hidden="true" /> Search
            </button>
          </div>
        </form>

        {error && <div role="alert" className="alert alert-danger">{error.message}</div>}
        {loading && <Loading />}

        <div className="row">
          {data?.data.map((contributor) => (
            <div className="col-lg-4 col-md-6 col-12 mb-4" key={contributor.id}>
              <div className="jbr-wrap bg-white border rounded h-100 d-flex flex-column">
                <div className="p-3 d-flex" style={{ gap: 14 }}>
                  <ContributorPhoto contributor={contributor} />
                  <div style={{ minWidth: 0 }}>
                    <h2 className="ft-bold mb-1" style={{ fontSize: '1.05rem', color: '#1e293b' }}>
                      <Link to={to(contributor.path)} className="text-decoration-none" style={{ color: 'inherit' }}>{contributor.name}</Link>
                    </h2>
                    {contributor.jobTitle && <p className="text-muted medium mb-1"><i className="fa fa-briefcase mr-1" aria-hidden="true" />{contributor.jobTitle}</p>}
                    {contributor.organization && <p className="text-muted medium mb-1"><i className="fa fa-building mr-1" aria-hidden="true" />{contributor.organization.slice(0, 40)}{contributor.organization.length > 40 ? '…' : ''}</p>}
                    {contributor.country && <p className="text-muted medium mb-1"><i className="fa fa-location-dot mr-1" aria-hidden="true" />{contributor.country}</p>}
                    <BadgePill badge={contributor.badge} />
                  </div>
                </div>
                <div className="mt-auto d-flex align-items-center justify-content-between px-3 py-2" style={{ borderTop: '1px solid #e2e8f0' }}>
                  <span className="text-muted medium"><i className="fa fa-book mr-1" aria-hidden="true" />{formatNumber(contributor.contributions)} Contribution{contributor.contributions === 1 ? '' : 's'}</span>
                  <Link to={to(contributor.path)} className="theme-cl ft-medium text-decoration-none">View profile <i className="fa fa-arrow-right" aria-hidden="true" /></Link>
                </div>
              </div>
            </div>
          ))}
        </div>

        {data && data.meta.lastPage > 1 && (
          <nav className="d-flex justify-content-between align-items-center" aria-label="Pagination">
            <button type="button" className="btn btn-outline-secondary bg-white" disabled={data.meta.page <= 1} onClick={() => setQuery((previous) => ({ ...previous, page: data.meta.page - 1 }))}>Previous</button>
            <span>Page {data.meta.page} of {data.meta.lastPage}</span>
            <button type="button" className="btn btn-outline-secondary bg-white" disabled={data.meta.page >= data.meta.lastPage} onClick={() => setQuery((previous) => ({ ...previous, page: data.meta.page + 1 }))}>Next</button>
          </nav>
        )}
      </div>
    </div>
  );
};

const StatTile = ({ icon, tint, value, label }) => (
  <div className="col-md-3 col-6 mb-3">
    <div className="bg-white rounded border p-3 h-100 d-flex align-items-center" style={{ gap: 12, boxShadow: '0 2px 8px rgba(15,23,42,0.04)' }}>
      <span className="d-inline-flex align-items-center justify-content-center rounded" aria-hidden="true" style={{ width: 44, height: 44, background: tint.bg, color: tint.fg, fontSize: '1.1rem' }}>
        <i className={`fa ${icon}`} />
      </span>
      <span>
        <span className="ft-bold d-block" style={{ fontSize: '1.5rem', color: '#0f172a' }}>{formatNumber(value)}</span>
        <span className="text-muted">{label}</span>
      </span>
    </div>
  </div>
);

/** Contributor drill-down — header, badge card, stat tiles, forums, resources, communities. */
export const AuthorPage = () => {
  const { key } = useParams();
  const { manifest } = useSite();
  const { data, error, loading } = useApi(`/api/authors/${encodeURIComponent(key)}`);
  const to = (path) => withSitePrefix(manifest.site.pathPrefix, path);

  if (loading && !data) return <Loading />;
  if (error) return <Page title="Contributors"><div className="alert alert-warning">{error.message}</div></Page>;
  if (!data) return null;
  const contributor = data.data;
  const badge = contributor.badgeDetail;

  return (
    <div className="gray py-4">
      <Seo title={contributor.name} path={to(contributor.path)} description={`${contributor.name} — publications, resources and forum expertise on ${manifest.settings.site_name}.`} />
      <div className="container">
        <nav className="mb-3" aria-label="Breadcrumb">
          <ol className="breadcrumb mb-0" style={{ background: 'transparent', padding: 0, fontSize: '0.875rem' }}>
            <li className="breadcrumb-item"><Link to={to('/')}>Home</Link></li>
            <li className="breadcrumb-item"><Link to={to('/authors')}>Contributors</Link></li>
            <li className="breadcrumb-item active" aria-current="page">{contributor.name}</li>
          </ol>
        </nav>

        {/* Header card + badge card */}
        <div className="rounded border mb-4 p-4 d-flex flex-wrap justify-content-between" style={{ gap: 20, background: 'linear-gradient(135deg, #f0f7f3, #ffffff)' }}>
          <div className="d-flex" style={{ gap: 18 }}>
            <ContributorPhoto contributor={contributor} size={110} />
            <div>
              <p className="text-uppercase ft-medium mb-1" style={{ color: '#64748b', fontSize: '0.75rem', letterSpacing: '0.08em' }}>Contributor</p>
              <h1 className="ft-bold mb-1" style={{ fontSize: '1.8rem', color: '#0f172a' }}>{contributor.name}</h1>
              {contributor.jobTitle && <p className="ft-medium mb-1" style={{ color: '#4a2c3d', fontSize: '1.05rem' }}>{contributor.jobTitle}</p>}
              {contributor.organization && (
                <>
                  <p className="text-uppercase ft-medium mb-0 mt-2" style={{ color: '#94a3b8', fontSize: '0.7rem', letterSpacing: '0.08em' }}>Organization / Institution</p>
                  <p className="ft-medium mb-1"><i className="fa fa-building mr-1 theme-cl" aria-hidden="true" />{contributor.organization}</p>
                </>
              )}
              {contributor.country && <p className="text-muted mb-0"><i className="fa fa-location-dot mr-1" aria-hidden="true" />{contributor.country}</p>}
            </div>
          </div>
          {badge && (
            <div style={{ minWidth: 280 }}>
              <p className="text-uppercase ft-medium mb-2" style={{ color: '#64748b', fontSize: '0.75rem', letterSpacing: '0.08em' }}>Contributor Badge</p>
              <div className="bg-white rounded border p-3 d-flex" style={{ gap: 14 }}>
                <span className="d-inline-flex align-items-center justify-content-center rounded" aria-hidden="true" style={{ width: 52, height: 52, background: badge.color, fontSize: '1.3rem' }}>
                  <i className="fa fa-gem" style={{ color: '#1e293b' }} />
                </span>
                <span>
                  <span className="ft-bold d-block" style={{ fontSize: '1.05rem' }}>{badge.name}</span>
                  <span className="text-muted d-block medium">{formatNumber(badge.lifetimeContributions)} lifetime contributions</span>
                  {badge.acquiredAt && <span className="text-muted d-block medium">Acquired {formatDate(badge.acquiredAt)}</span>}
                </span>
              </div>
            </div>
          )}
        </div>

        {/* Stat tiles */}
        <div className="row mb-3">
          <StatTile icon="fa-chart-line" tint={{ bg: '#e7f5ee', fg: '#0e7a4f' }} value={contributor.contributions} label="Total contributions" />
          <StatTile icon="fa-book" tint={{ bg: '#e8effb', fg: '#2563eb' }} value={contributor.publicationCount} label="Published resources" />
          <StatTile icon="fa-comments" tint={{ bg: '#f1e9fb', fg: '#7c3aed' }} value={contributor.forumCount} label="Forum threads started" />
          <StatTile icon="fa-reply" tint={{ bg: '#fdeeea', fg: '#ea580c' }} value={contributor.commentCount} label="Forum comments" />
        </div>

        <div className="row">
          <div className="col-lg-8">
            <h2 className="ft-bold pb-2 mb-3" style={{ fontSize: '1.3rem', borderBottom: '1px solid #e2e8f0' }}>Forum contributions</h2>
            {contributor.forumThreads.length === 0 ? (
              <div className="bg-white rounded border p-3 mb-4 text-muted">No forum contributions found for this contributor yet.</div>
            ) : (
              <div className="mb-4">
                {contributor.forumThreads.map((thread) => (
                  <div key={thread.id} className="bg-white rounded border p-3 mb-2">
                    <Link to={to(thread.path)} className="ft-medium">{thread.title}</Link>
                    <p className="text-muted medium mb-0"><i className="fa fa-clock mr-1" aria-hidden="true" />{formatDate(thread.createdAt)} · <i className="fa fa-eye mr-1" aria-hidden="true" />{thread.views} views</p>
                  </div>
                ))}
              </div>
            )}

            <h2 className="ft-bold pb-2 mb-3" style={{ fontSize: '1.3rem', borderBottom: '1px solid #e2e8f0' }}>Published resources</h2>
            <div className="publication-feed-card-scope">
              {data.publications.data.map((item) => <FeedCard item={item} key={item.id} />)}
            </div>
            {data.publications.data.length === 0 && <div className="bg-white rounded border p-3 text-muted">No published resources yet.</div>}
          </div>

          <aside className="col-lg-4" aria-label="Communities of practice">
            <div className="bg-white rounded border p-3">
              <h2 className="ft-bold mb-1" style={{ fontSize: '1.05rem' }}><i className="fa fa-users theme-cl mr-1" aria-hidden="true" /> Communities of practice</h2>
              <p className="text-muted medium">{contributor.communities.length ? `Member of ${contributor.communities.length} communit${contributor.communities.length === 1 ? 'y' : 'ies'} on the hub.` : 'Not a member of any community yet.'}</p>
              {contributor.communities.map((community) => (
                <div key={community.id} className="rounded border p-2 mb-2">
                  <Link to={to(`/communities/${community.slug ?? community.id}`)} className="ft-medium d-block">{community.name}</Link>
                  <span className="text-muted medium"><i className="fa fa-users mr-1" aria-hidden="true" />{community.memberCount} members</span>
                </div>
              ))}
            </div>
          </aside>
        </div>
      </div>
    </div>
  );
};
