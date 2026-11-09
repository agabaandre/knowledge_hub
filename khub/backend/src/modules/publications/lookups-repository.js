import { TtlCache, HOUR } from '../../core/cache.js';

const cache = new TtlCache();

/**
 * Taxonomy lookups shared by search filters, navigation and the home page.
 * Cached per site for an hour (same TTL as the Laravel app).
 */
export class LookupsRepository {
  constructor(site) {
    this.site = site;
    this.db = site.db;
  }

  #remember(name, produce) {
    return cache.remember(`lookups:${this.site.id}:${name}`, HOUR, produce);
  }

  static forget(site) {
    cache.forgetPrefix(`lookups:${site.id}:`);
  }

  themes() {
    return this.#remember('themes', async () => {
      const themes = await this.db('thematic_area').select('id', 'description as name', 'detailed_description as description', 'icon', 'display_order').orderBy('display_order').orderBy('description');
      const subThemes = await this.db('sub_thematic_area').select('id', 'description as name', 'icon', 'thematic_area_id').orderBy('description');
      return themes.map((theme) => ({ ...theme, subThemes: subThemes.filter((sub) => sub.thematic_area_id === theme.id) }));
    });
  }

  dataCategories() {
    return this.#remember('data-categories', () =>
      this.db('data_categories').where('show_on_menu', true).select('id', 'category_name as name', 'slug', 'url_path', 'is_special', 'required_permission').orderBy('category_name'),
    );
  }

  fileCategories() {
    return this.#remember('file-categories', () => this.db('publication_categories').whereNull('parent_id').select('id', 'category_name as name').orderBy('category_name'));
  }

  fileTypes() {
    return this.#remember('file-types', () => this.db('file_type').select('id', 'name', 'icon').orderBy('name'));
  }

  countries() {
    return this.#remember('countries', () => this.db('country').select('id', 'slug', 'name', 'iso_code', 'region_id').orderBy('name'));
  }

  years() {
    return this.#remember('years', async () => {
      const rows = await this.db('publication').distinct('year_published').whereNotNull('year_published').orderBy('year_published', 'desc');
      return rows.map((row) => row.year_published);
    });
  }

  /** Tags ranked by how many public records carry them. */
  popularTags(limit = 20) {
    return this.#remember(`popular-tags:${limit}`, () =>
      this.db('tags')
        .join('publication_tags', 'publication_tags.tag_id', 'tags.id')
        .select('tags.id', 'tags.slug', 'tags.tag_text as name')
        .count({ total: 'publication_tags.id' })
        .groupBy('tags.id', 'tags.slug', 'tags.tag_text')
        .orderBy('total', 'desc')
        .limit(limit),
    );
  }

  healthEmergencies() {
    return this.#remember('health-emergencies', () => this.db('tags').where('is_health_emergency', true).select('id', 'slug', 'tag_text as name', 'overview').orderBy('tag_text'));
  }

  /** Entry-point counts for the home page "Explore Key Sections" cards (10 minute cache, as in Laravel). */
  sectionCounts() {
    return cache.remember(`lookups:${this.site.id}:section-counts`, 10 * 60 * 1000, async () => {
      const count = async (query) => Number((await query.count({ total: '*' }).first()).total);
      const [healthTopics, authors, countries, forums] = await Promise.all([
        count(this.db('tags').where('is_health_topic', true)),
        count(this.db('author')),
        count(this.db('country')),
        count(this.db('forums').where({ is_approved: true, is_rejected: false })),
      ]);
      return { healthTopics, authors, geographical: countries, forums };
    });
  }

  staticLinks() {
    return this.#remember('static-links', () => this.db('static_links').select('id', 'title', 'link', 'open_in_new_tab').orderBy('order'));
  }

  quotes() {
    return this.#remember('quotes', () => this.db('quotes').where('is_active', 1).select('id', 'quote', 'image', 'link_url'));
  }

  async tagByKey(key) {
    const query = this.db('tags').select('id', 'slug', 'tag_text as name', 'overview', 'is_health_topic', 'is_health_emergency');
    return /^\d+$/.test(key) ? query.where('id', Number(key)).first() : query.where('slug', key).first();
  }

  themeByKey(key) {
    return this.db('thematic_area').select('id', 'description as name', 'detailed_description as description', 'icon').where('id', Number(key) || 0).first();
  }

  subThemeByKey(key) {
    return this.db('sub_thematic_area').select('id', 'description as name', 'detailed_description as description', 'thematic_area_id').where('id', Number(key) || 0).first();
  }
}
