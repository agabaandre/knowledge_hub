/**
 * Public URL builders. Keep the same paths as the Laravel portal so bookmarks,
 * search engine results and federation links continue to work.
 */
const keyOf = (record) => (record?.slug ? record.slug : String(record?.id ?? ''));

export const publicationPath = (publication) => `/records/resource/${keyOf(publication)}`;
export const tagPath = (tag) => `/records/tag/${keyOf(tag)}`;
export const themePath = (theme) => `/records/theme/${keyOf(theme)}`;
export const subThemePath = (subTheme) => `/records/sub-theme/${keyOf(subTheme)}`;
export const forumPath = (forum) => `/forums/${keyOf(forum)}`;
export const communityPath = (community) => `/communities/${keyOf(community)}`;
export const healthTopicPath = (tag) => `/health-topics/${keyOf(tag)}`;
export const authorPath = (author) => `/authors/publications/${keyOf(author)}`;
export const countryPath = (country) => `/countries/details/${keyOf(country)}`;
export const eventPath = (event) => `/events/${event?.id ?? ''}`;

export const recordsSearchPath = (params = {}) => {
  const query = new URLSearchParams();
  for (const [key, value] of Object.entries(params)) {
    if (value === undefined || value === null || value === '') continue;
    if (Array.isArray(value)) value.forEach((v) => query.append(key, String(v)));
    else query.set(key, String(value));
  }
  const text = query.toString();
  return text ? `/records/search?${text}` : '/records/search';
};

/** Prefix a site-relative path with the site's URL prefix (path-based multi-site). */
export const withSitePrefix = (pathPrefix, path) => {
  const prefix = pathPrefix ? `/${pathPrefix}` : '';
  return `${prefix}${path.startsWith('/') ? path : `/${path}`}`;
};
