import { useEffect, useState } from 'react';
import { Link, useLocation, useParams, useSearchParams } from 'react-router-dom';
import { Seo } from '../../core/Seo.jsx';
import { useApi } from '../../core/hooks.js';
import { useSite } from '../../core/site-context.jsx';
import { useT } from '../../core/i18n.jsx';
import { withSitePrefix } from '@khub/shared/urls';
import { FeedCard } from './FeedCard.jsx';
import { AiInsights } from './AiInsights.jsx';
import { SearchSelect } from '../../core/SearchSelect.jsx';

const SORTS = [['', 'Relevance'], ['newest', 'Newest'], ['oldest', 'Oldest'], ['popular', 'Most viewed'], ['title', 'Title']];

const SidebarBox = ({ title, children }) => (
  <div className="sidebar-content mb-3" style={{ background: '#fff', border: '1px solid #e2e8f0', borderRadius: '0.25rem', padding: 18, boxShadow: '0 2px 8px rgba(0,0,0,.04)' }}>
    <h6 className="fw-bold mb-2" style={{ color: '#0f172a' }}>{title}</h6>
    {children}
  </div>
);

/** Records search: layout and styling follow publications/search.blade.php (results left, refine sidebar right). */
export const SearchPage = () => {
  const { manifest } = useSite();
  const [params, setParams] = useSearchParams();
  const [term, setTerm] = useState(params.get('term') ?? '');
  const to = (path) => withSitePrefix(manifest.site.pathPrefix, path);
  const t = useT();

  // SEO routes /records/tag/{slug} and /records/theme/{slug} carry the filter in the path.
  const { key: pathKey } = useParams();
  const { pathname } = useLocation();
  const pathTag = pathKey && pathname.includes('/records/tag/') ? pathKey : '';
  const pathTheme = pathKey && pathname.includes('/records/theme/') ? pathKey : '';
  const taxonomy = useApi(
    pathTag || pathTheme ? `/api/publications/taxonomy/${pathTag ? 'tag' : 'theme'}/${encodeURIComponent(pathKey)}` : null,
    undefined,
    { enabled: Boolean(pathTag || pathTheme) },
  );
  const taxonomyName = taxonomy.data?.data?.name;

  const query = {
    term: params.get('term') ?? '',
    sort: params.get('sort') ?? '',
    tag: pathTag || (params.get('tag') ?? ''),
    theme: pathTheme ? (taxonomy.data?.data?.id ?? '') : (params.get('theme') ?? ''),
    category: params.get('category') ?? '',
    year: params.get('year') ?? '',
    file_type_id: params.getAll('file_type_id'),
    page: params.get('page') ?? 1,
    per_page: 10,
  };
  const lookups = useApi('/api/publications/lookups');
  const waitingForTheme = Boolean(pathTheme) && !taxonomy.data;
  const results = useApi('/api/publications/search', query, { enabled: !waitingForTheme });

  useEffect(() => setTerm(params.get('term') ?? ''), [params]);

  const update = (changes) => {
    const next = new URLSearchParams(params);
    for (const [key, value] of Object.entries(changes)) {
      next.delete(key);
      [].concat(value ?? []).filter(Boolean).forEach((item) => next.append(key, String(item)));
    }
    if (!('page' in changes)) next.delete('page');
    setParams(next);
  };

  const data = results.data;
  const count = data?.meta?.total ?? 0;
  const pageTitle = taxonomyName
    ? `Resources: ${taxonomyName}`
    : query.term ? `Search: ${query.term}` : 'Browse public health resources';

  return (
    <div className="gray py-4">
      <Seo title={pageTitle} path={to('/records/search')}
        description={`Browse and search public health publications, reports, forums, and communities on ${manifest.settings.site_name}. Filter by theme, country, author, or file type.`} />
      <div className="container">
        <nav className="mb-3" aria-label="Breadcrumb">
          <ol className="breadcrumb mb-0" style={{ background: 'transparent', padding: 0, fontSize: '0.875rem' }}>
            <li className="breadcrumb-item"><Link to={to('/')}>{t('frontend_nav.home', 'Home')}</Link></li>
            <li className="breadcrumb-item active" aria-current="page">Browse resources</li>
          </ol>
        </nav>
        <div className="row">
          <div className="col-lg-8">
            <div className="records-search-main position-relative">
              <h1 className="mb-3" style={{ fontSize: '1.35rem', fontWeight: 700, color: '#0f172a', lineHeight: 1.35 }}>{pageTitle}</h1>

              <form className="row no-gutters bg-white rounded border mb-3" role="search"
                onSubmit={(event) => { event.preventDefault(); update({ term: term.trim() }); }}>
                <div className="col-md-8 col-12">
                  <label htmlFor="records-term" className="sr-only">Keywords</label>
                  <input id="records-term" type="search" name="term" className="form-control border-0 main-search" placeholder={t('home_sections.search_placeholder_keywords', 'Type Keywords')} value={term} onChange={(event) => setTerm(event.target.value)} />
                </div>
                <div className="col-md-4 col-12">
                  <button type="submit" className="btn theme-bg text-white full-width" style={{ minHeight: 54, width: '100%', borderRadius: '0 0.25rem 0.25rem 0' }}>
                    <i className="fa fa-magnifying-glass mr-1" aria-hidden="true" /> {t('home_sections.search', 'Search')}
                  </button>
                </div>
              </form>

              <div className="sidebar-content mb-3" style={{ background: '#fff', border: '1px solid #e2e8f0', borderRadius: '0.25rem', padding: 18, boxShadow: '0 2px 8px rgba(0,0,0,.04)' }} aria-live="polite">
                <div className="d-flex align-items-center justify-content-between flex-wrap">
                  <div className="d-flex align-items-center">
                    <p className="mb-0 mr-3 fw-semibold" style={{ color: 'var(--theme-color-primary, #006239)' }}>{t('publications.search.results_summary', 'Results summary')}</p>
                    <span className="fw-bold" style={{ color: '#1e293b', fontSize: '1rem' }}>
                      {results.loading ? 'Searching…' : `${count.toLocaleString()} result${count === 1 ? '' : 's'} found`}
                    </span>
                  </div>
                  <div>
                    <label className="sr-only" htmlFor="records-sort">Sort results</label>
                    <select id="records-sort" className="custom-select" style={{ width: 'auto' }} value={query.sort} onChange={(event) => update({ sort: event.target.value })}>
                      {SORTS.map(([value, label]) => <option key={value} value={value}>{label}</option>)}
                    </select>
                  </div>
                </div>
              </div>

              <AiInsights term={query.term} />

              {results.error && <div className="alert alert-danger" role="alert">{results.error.message}</div>}

              <div className="publication-feed-card-scope" aria-busy={results.loading}>
                {data?.data.map((item) => <FeedCard item={item} key={item.id} />)}
              </div>
              {data && data.data.length === 0 && !results.loading && (
                <div className="alert alert-light border">No resources matched this search. Try fewer keywords or clear the filters.</div>
              )}

              {data && data.meta.lastPage > 1 && (
                <nav className="d-flex justify-content-between align-items-center mt-3" aria-label="Pagination">
                  <button type="button" className="btn btn-outline-secondary" disabled={data.meta.page <= 1} onClick={() => update({ page: data.meta.page - 1 })}>
                    <i className="fa fa-angle-left mr-1" aria-hidden="true" /> {t('pagination.previous', 'Previous').replace('\u00ab', '').trim()}
                  </button>
                  <span>Page {data.meta.page} of {data.meta.lastPage}</span>
                  <button type="button" className="btn btn-outline-secondary" disabled={data.meta.page >= data.meta.lastPage} onClick={() => update({ page: data.meta.page + 1 })}>
                    {t('pagination.next', 'Next').replace('\u00bb', '').trim()} <i className="fa fa-angle-right ml-1" aria-hidden="true" />
                  </button>
                </nav>
              )}
            </div>
          </div>

          <aside className="col-lg-4 records-search-sidebar" aria-label="Refine results">
            {(lookups.data?.popularTags?.length ?? 0) > 0 && (
              <SidebarBox title={t('ui_body.footer_popular_tags', 'Popular Tags')}>
                <div className="d-flex flex-wrap" style={{ gap: 6 }}>
                  {lookups.data.popularTags.slice(0, 15).map((tag) => {
                    const active = query.tag === String(tag.id) || query.tag === tag.slug;
                    return (
                      <button key={tag.id} type="button"
                        className={`btn btn-sm ${active ? 'theme-bg text-white' : 'btn-outline-secondary'}`}
                        onClick={() => update({ tag: active ? '' : (tag.slug ?? tag.id) })}>
                        {tag.name}
                      </button>
                    );
                  })}
                </div>
              </SidebarBox>
            )}

            {(lookups.data?.fileTypes?.length ?? 0) > 0 && (
              <SidebarBox title={t('home_sections.file_type', 'File type')}>
                <ul className="list-unstyled mb-0">
                  {lookups.data.fileTypes.map((type) => {
                    const checked = query.file_type_id.includes(String(type.id));
                    return (
                      <li key={type.id}>
                        <label className="d-flex align-items-center" style={{ gap: 8, fontWeight: 400, cursor: 'pointer' }}>
                          <input type="checkbox" checked={checked}
                            onChange={() => update({ file_type_id: checked ? query.file_type_id.filter((id) => id !== String(type.id)) : [...query.file_type_id, type.id] })} />
                          {type.name}
                        </label>
                      </li>
                    );
                  })}
                </ul>
              </SidebarBox>
            )}

            {(lookups.data?.themes?.length ?? 0) > 0 && (
              <SidebarBox title={t('home_sections.thematic_area', 'Health theme')}>
                <SearchSelect id="filter-theme" options={[{ id: '', name: 'All themes' }, ...lookups.data.themes.map((theme) => ({ id: theme.id, name: theme.name }))]}
                  value={query.theme} placeholder="All themes" onChange={(next) => update({ theme: next })} />
              </SidebarBox>
            )}

            {(lookups.data?.years?.length ?? 0) > 0 && (
              <SidebarBox title="Year published">
                <SearchSelect id="filter-year" options={[{ id: '', name: 'Any year' }, ...lookups.data.years.map((year) => ({ id: year, name: String(year) }))]}
                  value={query.year} placeholder="Any year" onChange={(next) => update({ year: next })} />
              </SidebarBox>
            )}
          </aside>
        </div>
      </div>
    </div>
  );
};
