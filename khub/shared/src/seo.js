/**
 * SEO defaults mirrored from the Laravel `SeoDefaults` and `HomePageSeo` support
 * classes so the React pages emit the same snippets search engines already index.
 */
export const DEFAULT_SEO_KEYWORDS = 'Africa CDC, public health, health research, publications, knowledge hub, Africa';

export const ROBOTS_CONTENT = 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';

export const SOCIAL_PROFILES = Object.freeze([
  'https://africacdc.org',
  'https://twitter.com/AfricaCDC',
  'https://www.facebook.com/AfricaCDC',
  'https://www.linkedin.com/company/africa-cdc',
]);

export const stripTags = (html) => String(html ?? '').replace(/<[^>]*>/g, '').replace(/\s+/g, ' ').trim();

export const truncate = (text, length = 160) => {
  const clean = stripTags(text);
  return clean.length <= length ? clean : `${clean.slice(0, length - 1).trimEnd()}…`;
};

export const homeDescription = (siteName) =>
  `Discover publications, health emergencies, flagship initiatives, health topics, forums, courses, and communities on ${siteName} — Africa CDC’s continental public health knowledge platform.`;

/** Fallback description per route path (same wording as the Laravel app). */
export const descriptionForPath = (path, siteName) => {
  const clean = path.replace(/^\/+|\/+$/g, '');
  const rules = [
    ['', homeDescription(siteName)],
    ['records/search', `Browse and search public health publications, reports, forums, and communities on ${siteName}. Filter by theme, country, author, or file type.`],
    ['records/resource', `Public health publication or resource on ${siteName}.`],
    ['records', `Search publications, communities, and forums on ${siteName}. Filter by theme, country, author, type, or keywords.`],
    ['health-topics', `Browse health topics, emergencies, and thematic areas with linked publications and resources on ${siteName}.`],
    ['forums', `Public health discussion forums on ${siteName}: ask questions, share evidence, and connect with experts.`],
    ['communities', `Join communities of practice on ${siteName}: collaborate on public health themes with peers across Africa.`],
    ['courses', `Browse online courses on public health, leadership, and technical skills on ${siteName}. Enrol and learn at your own pace.`],
    ['faqs', `Frequently asked questions about using ${siteName}, accounts, submissions, and support.`],
    ['authors', `Browse contributors, researchers, clinicians, and institutions sharing public health publications and forum expertise on ${siteName}.`],
    ['countries', `Explore public health resources and coverage by member state on ${siteName}.`],
    ['adminunits', `Browse administrative areas and linked health resources on ${siteName}.`],
    ['login', `Sign in to ${siteName} to access saved resources, discussion forums, courses, communities, and your profile.`],
    ['register', `Register for a free ${siteName} account to save favourites, join forums, enrol in courses, and collaborate in communities of practice.`],
    ['privacy', `Privacy policy and data practices for ${siteName} and the Africa CDC knowledge platform.`],
    ['federated', `Browse resources syndicated from partner country Knowledge Hubs on ${siteName}.`],
  ];
  for (const [prefix, description] of rules) {
    if (prefix === '' ? clean === '' : clean === prefix || clean.startsWith(`${prefix}/`)) return description;
  }
  const label = clean.split('/')[0].replace(/[-_]+/g, ' ');
  return `${label.charAt(0).toUpperCase()}${label.slice(1)} on ${siteName}.`;
};

/** Paths search engines must not index (mirrors robots.txt of the Laravel app). */
export const ROBOTS_DISALLOW = Object.freeze([
  '/admin/', '/account/', '/api/', '/tools/', '/permissions/', '/docs/', '/install/', '/storage/',
  '/content-request/', '/mailing_list', '/tests', '/auth/', '/locale/', '/login', '/register', '/logout',
  '/password/', '/verify', '/records/search/fragment', '/records/autocomplete',
]);
