import { Link } from 'react-router-dom';
import { Seo } from '../../core/Seo.jsx';
import { useApi } from '../../core/hooks.js';
import { useSite } from '../../core/site-context.jsx';
import { useT } from '../../core/i18n.jsx';
import { withSitePrefix, recordsSearchPath, themePath } from '@khub/shared/urls';
import { homeDescription } from '@khub/shared/seo';
import { TopSearchCard } from '../publications/TopSearchCard.jsx';

const KEY_SECTIONS = [
  { module: 'health-topics', path: '/health-topics', icon: 'fa-shield-alt', count: 'healthTopics', title: 'Health Topics', desc: 'Explore diseases, conditions, and key health issues.' },
  { module: 'authors', path: '/authors', icon: 'fa-pen-nib', count: 'authors', title: 'Resource Contributing Sources', desc: 'Organizations and authors that provide verified resources.' },
  { module: 'countries', path: '/countries', icon: 'fa-map-marker-alt', count: 'geographical', title: 'Geographical Coverage', desc: 'View data and documents by regions and countries.' },
  { module: 'forums', path: '/forums', icon: 'fa-comments', count: 'forums', title: 'Public Discussion Forums', desc: 'Join discussions on pressing health topics and challenges.' },
];

/** Home page: spotlight search, health themes, key sections, top searches — same sections as the Laravel portal. */
export const HomePage = () => {
  const { manifest } = useSite();
  const { data, error, loading } = useApi('/api/publications/home');
  const modules = manifest.modules ?? {};
  const settings = manifest.settings;
  const siteName = settings.site_name;
  const to = (path) => withSitePrefix(manifest.site.pathPrefix, path);
  const t = useT();

  const jsonLd = [
    { '@context': 'https://schema.org', '@type': 'Organization', name: siteName, url: manifest.site.publicUrl, logo: settings.logo, sameAs: ['https://africacdc.org', 'https://twitter.com/AfricaCDC', 'https://www.facebook.com/AfricaCDC', 'https://www.linkedin.com/company/africa-cdc'] },
    { '@context': 'https://schema.org', '@type': 'WebSite', name: siteName, url: manifest.site.publicUrl, potentialAction: { '@type': 'SearchAction', target: `${manifest.site.publicUrl}/records/search?term={search_term_string}`, 'query-input': 'required name=search_term_string' } },
  ];

  const stageStyle = settings.spotlight_banner
    ? { backgroundImage: `linear-gradient(135deg, rgba(135, 200, 161, 0.35) 0%, rgba(35, 132, 68, 0.35) 100%), url('${settings.spotlight_banner}')`, backgroundSize: 'cover', backgroundPosition: 'center' }
    : { backgroundImage: 'linear-gradient(135deg, rgba(135, 200, 161, 0.45) 0%, rgba(35, 132, 68, 0.45) 100%)' };

  return (
    <>
      <Seo title={siteName} path={to('/')} description={homeDescription(siteName)} jsonLd={jsonLd} />

      {/* Spotlight: banner background + unified search (markup mirrors home spotlight partial) */}
      <div className="spotlight home-spotlight home-spotlight--has-banner px-3 py-3">
        <div className="home-spotlight__stage" style={stageStyle} aria-hidden="true" />
        <h1 className="sr-only">{siteName}{settings.slogan ? ` — ${settings.slogan}` : ''}</h1>
        <div className="search-container" style={{ maxWidth: 1200, margin: '0 auto', width: '100%', padding: '0 15px' }}>
          <form action={to('/records/search')} method="get" className="filters" role="search" aria-label="Search records">
            <div className="row no-gutters bg-white rounded search-form" id="simple_search" style={{ borderRadius: '0.375rem' }}>
              <div className="col-xl-8 col-lg-8 col-md-8 col-sm-12 col-12">
                <div className="form-group mb-0 position-relative main_search">
                  <label htmlFor="main-search" className="sr-only">{t('home_sections.search_keywords_label', 'Search Keywords')}</label>
                  <input type="text" id="main-search" name="term" className="form-control left-ico term main-search" placeholder={t('home_sections.search_placeholder_keywords', 'Type Keywords')} />
                </div>
              </div>
              <div className="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12 bg-show">
                <div className="form-group mb-0 position-relative">
                  <button className="btn full-width text-white fs-md search-btn-unified" type="submit"
                    style={{ background: 'var(--theme-color-primary, #006239)', border: 'none', borderRadius: '0 0.375rem 0.375rem 0', height: '100%', minHeight: 62, display: 'flex', alignItems: 'center', justifyContent: 'center', gap: '0.5rem', width: '100%' }}>
                    <i className="fa fa-magnifying-glass" aria-hidden="true" />
                    <span>{t('home_sections.search', 'Search')}</span>
                  </button>
                </div>
              </div>
            </div>
          </form>

          {/* Health themes grid inside the spotlight, as on the live portal */}
          {(data?.themes?.length ?? 0) > 0 && (
            <section className="theme-grid">
              <div className="themes-container">
                <h3 className="themes-section-title">{settings.section_title_health_themes || t('home_sections.health_themes', 'Choose a Health Theme to Explore')}</h3>
                <div className="themes-grid">
                  {data.themes.map((theme) => (
                    <div className="theme-item" key={theme.id}>
                      <Link to={to(theme.slug ? themePath(theme) : recordsSearchPath({ theme: theme.id }))} className="theme-link">
                        <div className="theme-card">
                          <i className={`fa ${theme.icon?.startsWith('fa-') ? theme.icon : 'fa-heartbeat'} theme-icon`} aria-hidden="true" />
                          <p className="theme-title" title={theme.name}>{theme.name}</p>
                        </div>
                      </Link>
                    </div>
                  ))}
                </div>
              </div>
            </section>
          )}
        </div>
      </div>

      {error && <div className="container py-4"><div className="alert alert-warning">{error.message}</div></div>}
      {loading && !data && <div className="spinner-ring" role="status" aria-label="Loading" />}

      {/* Explore Key Sections */}
      {data && (
        <section className="py-5 kh-key-sections" id="categorization" style={{ marginBottom: 0 }}>
          <div className="container">
            <div className="row justify-content-center">
              <div className="col-lg-9 col-md-10">
                <div className="sec_title text-center">
                  <h2>{t('home_sections.explore_key_sections', 'Explore Key Sections')}</h2>
                  <p className="mb-0">{t('home_sections.explore_key_sections_subtitle', 'Browse the main entry points to health knowledge, sources, geography, and community discussions.')}</p>
                </div>
              </div>
            </div>
            <div className="kh-key-sections-grid">
              {KEY_SECTIONS.filter((section) => modules[section.module]).map((section) => (
                <Link to={to(section.path)} className="kh-key-section-card" key={section.path}>
                  <div className="kh-key-section-card__bg-icon" aria-hidden="true"><i className={`fa ${section.icon}`} /></div>
                  <div className="kh-key-section-card__content">
                    <div className="kh-key-section-card__head">
                      <h3 className="kh-key-section-card__title">{section.title}</h3>
                      <span className="kh-key-section-card__badge">{data.sectionCounts?.[section.count] ?? 0}</span>
                    </div>
                    <p className="kh-key-section-card__desc">{section.desc}</p>
                    <span className="kh-key-section-card__cta">
                      <span>{t('home_sections.explore', 'Explore')}</span> <i className="fa fa-arrow-right" aria-hidden="true" />
                    </span>
                  </div>
                </Link>
              ))}
            </div>
          </div>
        </section>
      )}

      {/* Top Searches */}
      {(data?.topSearches?.length ?? 0) > 0 && (
        <section className="middle gray" style={{ paddingTop: 0, paddingBottom: 20 }}>
          <div className="container">
            <div className="row justify-content-center">
              <div className="col-12">
                <div className="sec_title position-relative text-center mb-3">
                  <h2 className="ft-bold">{settings.section_title_top_searches || t('home_sections.top_searches', 'Top Searches')}</h2>
                </div>
              </div>
            </div>
            <div className="row align-items-center">
              {data.topSearches.map((item) => <TopSearchCard item={item} key={item.id} />)}
            </div>
            <p className="text-center mb-0">
              <Link to={to('/records/search')} className="btn theme-bg text-white">{t('frontend_nav.view_all_resources', 'Browse all resources')}</Link>
            </p>
          </div>
        </section>
      )}

      {/* Featured resources, when enabled in Admin → Configure */}
      {(data?.featured?.length ?? 0) > 0 && (
        <section className="middle" style={{ paddingTop: 10, paddingBottom: 30 }}>
          <div className="container">
            <div className="sec_title position-relative text-center mb-3">
              <h2 className="ft-bold">{settings.section_title_recommended || t('frontend_nav.featured_resources', 'Featured Resources')}</h2>
            </div>
            <div className="row align-items-center">
              {data.featured.map((item) => <TopSearchCard item={item} key={item.id} />)}
            </div>
          </div>
        </section>
      )}
    </>
  );
};
