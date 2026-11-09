import { Link, useParams } from 'react-router-dom';
import { Seo } from '../../core/Seo.jsx';
import { useApi } from '../../core/hooks.js';
import { useSite } from '../../core/site-context.jsx';
import { withSitePrefix } from '@khub/shared/urls';
import { Page, Loading } from '../../core/Page.jsx';
import { TopSearchCard } from '../publications/TopSearchCard.jsx';

export const CountriesPage = () => {
  const { manifest } = useSite();
  const { data, error, loading } = useApi('/api/countries');
  const to = (path) => withSitePrefix(manifest.site.pathPrefix, path);
  const label = manifest.site.features?.statesEnabled ? 'Member States' : 'Geographical Coverage';

  return (
    <Page title={label} lead="View data and documents by regions and countries.">
      <Seo title={label} path="/countries" description={`Explore public health resources and coverage by member state on ${manifest.settings.site_name}.`} />
      {error && <div className="alert alert-danger" role="alert">{error.message}</div>}
      {loading && <Loading />}
      <div className="row">
        {data?.data.map((country) => (
          <div className="col-lg-3 col-md-4 col-6 mb-3" key={country.id}>
            <Link to={to(country.path)} className="theme-link">
              <div className="theme-card bg-white border rounded p-3 text-center h-100">
                {/^https?:/i.test(country.flag ?? '') ? (
                  <img src={country.flag} alt="" width="32" height="24" loading="lazy" className="mb-2" style={{ objectFit: 'cover' }} />
                ) : (
                  <i className="fa fa-flag theme-icon mb-2" aria-hidden="true" />
                )}
                <p className="theme-title mb-1">{country.name}</p>
                <small className="text-muted">{country.publicationCount} resources</small>
              </div>
            </Link>
          </div>
        ))}
      </div>
    </Page>
  );
};

export const CountryPage = () => {
  const { key } = useParams();
  const { manifest } = useSite();
  const { data, error, loading } = useApi(`/api/countries/${encodeURIComponent(key)}`);

  if (loading && !data) return <Loading />;
  if (error) return <Page title="Member States"><div className="alert alert-warning">{error.message}</div></Page>;
  if (!data) return null;
  const country = data.data;

  return (
    <Page title={country.name} crumb={country.name} lead={country.region ? `${country.region} · ${country.publicationCount} resources` : undefined}>
      <Seo title={country.name} path={withSitePrefix(manifest.site.pathPrefix, country.path)} description={`Public health resources and coverage for ${country.name}.`} />
      <div className="row align-items-center">
        {data.publications.data.map((item) => <TopSearchCard item={item} key={item.id} />)}
      </div>
      {data.publications.data.length === 0 && <div className="alert alert-light border">No resources linked to {country.name} yet.</div>}
    </Page>
  );
};
