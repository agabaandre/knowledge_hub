import { publicationPath, forumPath, communityPath, healthTopicPath, authorPath, countryPath, eventPath } from '@khub/shared';

const escapeXml = (text) => String(text).replace(/[<>&'"]/g, (c) => ({ '<': '&lt;', '>': '&gt;', '&': '&amp;', "'": '&apos;', '"': '&quot;' })[c]);

const toDate = (value) => {
  const date = value ? new Date(value) : new Date();
  return Number.isNaN(date.getTime()) ? new Date().toISOString() : date.toISOString();
};

const STATIC_PAGES = [
  ['/', 'daily', '1.0'], ['/records', 'daily', '0.9'], ['/publications', 'weekly', '0.8'],
  ['/forums', 'daily', '0.8', 'forums'], ['/communities', 'daily', '0.8', 'communities'],
  ['/courses', 'weekly', '0.7', 'courses'], ['/health-topics', 'weekly', '0.8', 'health-topics'],
  ['/authors', 'weekly', '0.6', 'authors'], ['/faqs', 'monthly', '0.5', 'faqs'],
  ['/privacy', 'yearly', '0.3'], ['/user_manual', 'monthly', '0.4'], ['/federated', 'weekly', '0.5', 'federation'],
  ['/browse/themes', 'weekly', '0.6'], ['/browse/subthemes', 'weekly', '0.6'],
];

const PUBLICATIONS_PER_SITEMAP = 2000;

/**
 * Sitemap index + sections, same section names and priorities as the Laravel
 * SitemapService so already-submitted sitemap URLs stay valid.
 */
export class SitemapService {
  constructor({ site, modules }) {
    this.site = site;
    this.db = site.db;
    this.modules = modules;
  }

  #url(path) {
    return `${this.site.publicUrl}${path}`;
  }

  async indexEntries() {
    const sections = ['pages'];
    const publicationCount = await this.#publicationQuery().count({ total: '*' }).first();
    const pages = Math.max(1, Math.ceil(Number(publicationCount?.total ?? 0) / PUBLICATIONS_PER_SITEMAP));
    for (let page = 1; page <= pages; page += 1) sections.push(`publications-${page}`);
    for (const [name, module] of [['forums', 'forums'], ['communities', 'communities'], ['health-topics', 'health-topics'], ['authors', 'authors'], ['events', 'events']]) {
      if (this.modules[module]) sections.push(name);
    }
    if (this.modules.countries && this.site.features.statesEnabled) sections.push('countries');
    return sections;
  }

  async renderIndex() {
    const sections = await this.indexEntries();
    const body = sections.map((name) => `  <sitemap><loc>${escapeXml(this.#url(`/sitemaps/${name}.xml`))}</loc><lastmod>${toDate()}</lastmod></sitemap>`).join('\n');
    return `<?xml version="1.0" encoding="UTF-8"?>\n<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n${body}\n</sitemapindex>\n`;
  }

  async renderSection(name) {
    const match = /^publications-(\d+)$/.exec(name);
    const entries = match ? await this.#publicationEntries(Number(match[1])) : await this.#sectionEntries(name);
    if (!entries) return null;
    return this.renderUrlSet(entries);
  }

  renderUrlSet(entries) {
    const body = entries
      .map((entry) => `  <url><loc>${escapeXml(entry.loc)}</loc><lastmod>${toDate(entry.lastmod)}</lastmod><changefreq>${entry.changefreq}</changefreq><priority>${entry.priority}</priority></url>`)
      .join('\n');
    return `<?xml version="1.0" encoding="UTF-8"?>\n<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n${body}\n</urlset>\n`;
  }

  async #sectionEntries(name) {
    const builders = {
      pages: () => this.#staticEntries(),
      forums: () => this.#rows(this.db('forums').where({ is_approved: true }).select('id', 'slug', 'created_at'), forumPath, 'weekly', '0.6', 'created_at'),
      communities: () => this.#rows(this.db('community_of_practices').where({ is_public: true }).select('id', 'slug', 'updated_at'), communityPath, 'weekly', '0.6'),
      'health-topics': () => this.#rows(this.db('tags').where({ is_health_topic: true }).select('id', 'slug'), healthTopicPath, 'weekly', '0.7'),
      authors: () => this.#rows(this.db('author').select('id', 'updated_at'), authorPath, 'monthly', '0.5'),
      events: () => this.#rows(this.db('events').where({ status: 'active' }).select('id', 'updated_at'), eventPath, 'weekly', '0.5'),
      countries: () => this.#rows(this.db('country').select('id', 'slug'), countryPath, 'weekly', '0.6'),
    };
    return builders[name] ? builders[name]() : null;
  }

  #staticEntries() {
    return STATIC_PAGES.filter(([, , , module]) => !module || this.modules[module]).map(([path, changefreq, priority]) => ({ loc: this.#url(path), lastmod: new Date(), changefreq, priority }));
  }

  #publicationQuery() {
    return this.db('publication').where({ is_active: 'Active', is_approved: true, is_version: false, is_admin_only_access: false });
  }

  async #publicationEntries(page) {
    const rows = await this.#publicationQuery().select('id', 'slug', 'updated_at').orderBy('id').limit(PUBLICATIONS_PER_SITEMAP).offset((page - 1) * PUBLICATIONS_PER_SITEMAP);
    return rows.map((row) => ({ loc: this.#url(publicationPath(row)), lastmod: row.updated_at, changefreq: 'monthly', priority: '0.7' }));
  }

  async #rows(query, pathFor, changefreq, priority, dateColumn = 'updated_at') {
    const rows = await query;
    return rows.map((row) => ({ loc: this.#url(pathFor(row)), lastmod: row[dateColumn], changefreq, priority }));
  }
}
