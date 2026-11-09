import { Link } from 'react-router-dom';
import { Seo } from '../../core/Seo.jsx';
import { useApi } from '../../core/hooks.js';
import { useSite } from '../../core/site-context.jsx';
import { withSitePrefix } from '@khub/shared/urls';
import { Page, Loading } from '../../core/Page.jsx';

export const CommunitiesPage = () => {
  const { manifest } = useSite();
  const { data, error, loading } = useApi('/api/communities', { per_page: 24 });
  const to = (path) => withSitePrefix(manifest.site.pathPrefix, path);

  return (
    <Page title="Communities of Practice" crumb="Communities" lead="Collaborate on public health themes with peers across Africa.">
      <Seo title="Communities of practice" path="/communities" description={`Join communities of practice on ${manifest.settings.site_name}: collaborate on public health themes with peers across Africa.`} />
      {error && <div className="alert alert-danger" role="alert">{error.message}</div>}
      {loading && <Loading />}
      <div className="row">
        {data?.data.map((community) => (
          <div className="col-lg-4 col-md-6 col-12 mb-3" key={community.id}>
            <div className="jbr-wrap border rounded bg-white p-3 h-100">
              <h2 className="fs-md ft-medium mb-1" style={{ fontSize: '1.02rem' }}>
                <Link to={to(community.path)}>{community.name}</Link>
              </h2>
              <p className="text-muted medium mb-2">
                {[community.organisation, community.country].filter(Boolean).join(' · ')}
              </p>
              {community.excerpt && <p className="medium mb-2">{community.excerpt}</p>}
              <p className="medium mb-0"><i className="fa fa-users mr-1" aria-hidden="true" />{community.memberCount} members</p>
            </div>
          </div>
        ))}
      </div>
      {data && data.data.length === 0 && !loading && <div className="alert alert-light border">No public communities yet.</div>}
    </Page>
  );
};
