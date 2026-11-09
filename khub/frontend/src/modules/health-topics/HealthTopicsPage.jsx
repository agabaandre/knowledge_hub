import { Link, useParams } from 'react-router-dom';
import { Seo } from '../../core/Seo.jsx';
import { useApi } from '../../core/hooks.js';
import { useSite } from '../../core/site-context.jsx';
import { withSitePrefix } from '@khub/shared/urls';
import { Page, Loading } from '../../core/Page.jsx';
import { TopSearchCard } from '../publications/TopSearchCard.jsx';

export const HealthTopicsPage = () => {
  const { manifest } = useSite();
  const { data, error, loading } = useApi('/api/health-topics');
  const to = (path) => withSitePrefix(manifest.site.pathPrefix, path);

  return (
    <Page title="Health Topics" lead="Explore diseases, conditions, and key health issues with linked resources.">
      <Seo title="Health topics" path="/health-topics" description={`Browse health topics, emergencies, and thematic areas with linked publications and resources on ${manifest.settings.site_name}.`} />
      {error && <div className="alert alert-danger" role="alert">{error.message}</div>}
      {loading && <Loading />}
      {(data?.emergencies?.length ?? 0) > 0 && (
        <section className="mb-4" aria-labelledby="emergencies">
          <h2 id="emergencies" className="ft-bold mb-3" style={{ fontSize: '1.15rem' }}>Health Emergencies</h2>
          <div className="d-flex flex-wrap" style={{ gap: 8 }}>
            {data.emergencies.map((topic) => (
              <Link key={topic.id} className="btn btn-sm btn-outline-danger" to={to(topic.path)}>{topic.name}</Link>
            ))}
          </div>
        </section>
      )}
      <div className="themes-grid" style={{ gridTemplateColumns: 'repeat(auto-fill, minmax(180px, 1fr))' }}>
        {data?.topics.map((topic) => (
          <div className="theme-item" key={topic.id}>
            <Link to={to(topic.path)} className="theme-link">
              <div className="theme-card" style={{ background: '#fff' }}>
                <i className="fa fa-shield-alt theme-icon" aria-hidden="true" />
                <p className="theme-title" title={topic.name}>{topic.name}</p>
                <small className="text-muted">{topic.resourceCount} resources</small>
              </div>
            </Link>
          </div>
        ))}
      </div>
    </Page>
  );
};

export const HealthTopicPage = () => {
  const { key } = useParams();
  const { manifest } = useSite();
  const { data, error, loading } = useApi(`/api/health-topics/${encodeURIComponent(key)}`);

  if (loading && !data) return <Loading />;
  if (error) return <Page title="Health Topics"><div className="alert alert-warning">{error.message}</div></Page>;
  if (!data) return null;
  const topic = data.data;

  return (
    <Page title={topic.name} crumb={topic.name} lead={topic.overview || undefined}>
      <Seo title={topic.name} path={withSitePrefix(manifest.site.pathPrefix, topic.path)} description={topic.overview || `Publications and resources on ${topic.name}.`} />
      <div className="row align-items-center">
        {data.resources.map((item) => <TopSearchCard item={item} key={item.id} />)}
      </div>
      {data.resources.length === 0 && <div className="alert alert-light border">No resources available for this topic yet.</div>}
    </Page>
  );
};
