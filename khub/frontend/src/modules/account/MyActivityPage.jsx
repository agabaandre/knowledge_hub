import { Link, useParams } from 'react-router-dom';
import { useApi } from '../../core/hooks.js';
import { useSite } from '../../core/site-context.jsx';
import { withSitePrefix } from '@khub/shared/urls';
import { Page, Loading } from '../../core/Page.jsx';
import { Seo } from '../../core/Seo.jsx';
import { formatDate } from '../../core/format.js';

const KINDS = {
  publications: { title: 'My Publications', endpoint: '/api/account/publications', empty: 'You have not submitted any resources yet.', cta: ['Publish a resource', '/account/publish'] },
  forums: { title: 'My Forums', endpoint: '/api/account/forums', empty: 'You have not started any discussions yet.', cta: ['Browse forums', '/forums'] },
  favourites: { title: 'My Favourites', endpoint: '/api/account/favourites', empty: 'You have no favourites yet — tap the heart on any resource.', cta: ['Browse resources', '/records/search'] },
};

const STATUS_BADGES = {
  approved: ['Approved', '#0e7a4f'],
  pending: ['Pending review', '#b45309'],
  rejected: ['Rejected', '#b91c1c'],
};

/** My Publications / My Forums / My Favourites — the account menu lists from the original portal. */
export const MyActivityPage = () => {
  const { kind = 'publications' } = useParams();
  const { manifest } = useSite();
  const config = KINDS[kind] ?? KINDS.publications;
  const { data, error, loading } = useApi(config.endpoint);
  const to = (path) => withSitePrefix(manifest.site.pathPrefix, path);

  return (
    <Page title={config.title} crumb={config.title}>
      <Seo title={config.title} path={`/account/${kind}`} noindex />
      <div className="d-flex justify-content-center mb-4" style={{ gap: 8 }}>
        {Object.entries(KINDS).map(([key, entry]) => (
          <Link key={key} to={to(`/account/${key}`)} className={`btn btn-sm ${key === kind ? 'theme-bg text-white' : 'btn-outline-secondary bg-white'}`}>{entry.title}</Link>
        ))}
      </div>
      {error && <div role="alert" className="alert alert-danger">{error.message}</div>}
      {loading && <Loading />}
      {data && data.data.length === 0 && (
        <div className="alert alert-light border text-center">
          {config.empty} <Link to={to(config.cta[1])} className="theme-cl ft-medium">{config.cta[0]}</Link>
        </div>
      )}
      <div className="row justify-content-center">
        <div className="col-lg-9 col-12">
          {data?.data.map((row) => {
            const [label, color] = STATUS_BADGES[row.status] ?? [];
            return (
              <div key={row.id} className="jbr-wrap bg-white border rounded p-3 mb-2 d-flex align-items-start justify-content-between" style={{ gap: 12 }}>
                <div>
                  <h2 className="fs-md ft-medium mb-1" style={{ fontSize: '1rem' }}>
                    {row.status === 'approved' || row.status === 'favourite'
                      ? <Link to={to(row.path)}>{row.title}</Link>
                      : row.title}
                  </h2>
                  <p className="text-muted medium mb-0">
                    <i className="fa fa-clock mr-1" aria-hidden="true" />{formatDate(row.createdAt)}
                    {row.author && <span className="ml-2"><i className="fa fa-bank mr-1" aria-hidden="true" />{row.author}</span>}
                    {typeof row.visits === 'number' && <span className="ml-2"><i className="fa fa-eye mr-1" aria-hidden="true" />{row.visits} views</span>}
                    {row.rejectedReason && <span className="d-block text-danger mt-1">Reason: {row.rejectedReason}</span>}
                  </p>
                </div>
                {label && <span className="badge text-white" style={{ background: color, padding: '5px 10px', flexShrink: 0 }}>{label}</span>}
              </div>
            );
          })}
        </div>
      </div>
    </Page>
  );
};
