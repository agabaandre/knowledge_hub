import { countOf, randomOrder, now } from '../../core/db/helpers.js';
import { applySearchTerm } from './search-query.js';

const CARD_COLUMNS = [
  'publication.id', 'publication.slug', 'publication.title', 'publication.description', 'publication.year_published',
  'publication.cover', 'publication.cover_is_exteranl', 'publication.is_featured', 'publication.is_video', 'publication.visits',
  'publication.created_at', 'publication.author_id', 'author.name as author_name',
  'publication.file_type_id', 'file_type.name as file_type_name', 'file_type.icon as file_type_icon',
  'publication.sub_thematic_area_id', 'sub_thematic_area.description as sub_theme_name',
  'thematic_area.id as thematic_area_id', 'thematic_area.description as theme_name',
  'publication.associated_authors', 'data_categories.category_name as data_category_name',
];

const SORTS = {
  newest: [['publication.created_at', 'desc']],
  oldest: [['publication.created_at', 'asc']],
  popular: [['publication.visits', 'desc'], ['publication.created_at', 'desc']],
  title: [['publication.title', 'asc']],
};

/**
 * Read side of publications: public, approved, non-version records only.
 * Every query starts from `#publicQuery()` so access rules live in one place.
 */
export class PublicationsRepository {
  constructor(db) {
    this.db = db;
  }

  /** Card columns plus the like and approved-comment counts shown on every card. */
  #cardSelect() {
    return [
      ...CARD_COLUMNS,
      this.db('favourites').count('*').whereRaw('favourites.publication_id = publication.id').as('like_count'),
      this.db('publication_comments').count('*').whereRaw('publication_comments.publication_id = publication.id').where('publication_comments.status', 'approved').as('comment_count'),
    ];
  }

  #publicQuery({ includeAdminOnly = false, includeVersions = false } = {}) {
    const query = this.db('publication')
      .leftJoin('author', 'author.id', 'publication.author_id')
      .leftJoin('file_type', 'file_type.id', 'publication.file_type_id')
      .leftJoin('sub_thematic_area', 'sub_thematic_area.id', 'publication.sub_thematic_area_id')
      .leftJoin('thematic_area', 'thematic_area.id', 'sub_thematic_area.thematic_area_id')
      .leftJoin('data_categories', 'data_categories.id', 'publication.data_category_id')
      .where('publication.is_active', 'Active')
      .where('publication.is_approved', true)
      .whereNull('publication.deleted_at');
    // Versions never appear in lists/search, but their detail pages are viewable.
    if (!includeVersions) query.where('publication.is_version', false);
    if (!includeAdminOnly) query.where('publication.is_admin_only_access', false);
    return query;
  }

  /**
   * @param {object} filters validated search filters
   * @param {{page:number, per_page:number}} pagination
   */
  async search(filters, pagination, viewer = null) {
    const query = this.#publicQuery({ includeAdminOnly: Boolean(viewer?.isAdmin) });
    applySearchTerm(this.db, query, filters.term);

    if (filters.tag) query.whereIn('publication.id', this.#idsWithTag(filters.tag));
    if (filters.theme) query.where('thematic_area.id', filters.theme);
    if (filters.sub_theme) query.where('publication.sub_thematic_area_id', filters.sub_theme);
    if (filters.category) query.where('publication.data_category_id', filters.category);
    if (filters.file_category_id?.length) query.whereIn('publication.publication_catgory_id', filters.file_category_id);
    if (filters.file_type_id?.length) query.whereIn('publication.file_type_id', filters.file_type_id);
    if (filters.data_category_id?.length) query.whereIn('publication.data_category_id', filters.data_category_id);
    if (filters.author) query.where('publication.author_id', filters.author);
    if (filters.year) query.where('publication.year_published', filters.year);
    if (filters.country) query.whereIn('publication.id', this.db('publication_countries').select('publication_id').where('country_id', filters.country));
    if (filters.featured) query.where('publication.is_featured', true);

    const total = await countOf(query);
    const sort = SORTS[filters.sort] ?? (filters.term ? SORTS.popular : SORTS.newest);
    const rows = await query
      .select(this.#cardSelect())
      .orderBy(sort.map(([column, order]) => ({ column, order })))
      .limit(pagination.per_page)
      .offset((pagination.page - 1) * pagination.per_page);

    return { rows, total };
  }

  #idsWithTag(tagKey) {
    const tags = this.db('tags').select('id');
    if (/^\d+$/.test(String(tagKey))) tags.where('id', Number(tagKey));
    else tags.where('slug', tagKey);
    return this.db('publication_tags').select('publication_id').whereIn('tag_id', tags);
  }

  featured(limit) {
    return this.#publicQuery().where('publication.is_featured', true).select(this.#cardSelect()).orderBy('publication.created_at', 'desc').limit(limit);
  }

  mostVisited(limit, offset = 0) {
    return this.#publicQuery().select(this.#cardSelect()).orderBy([{ column: 'publication.visits', order: 'desc' }, { column: 'publication.id', order: 'desc' }]).limit(limit).offset(offset);
  }

  countPublic() {
    return countOf(this.#publicQuery());
  }

  byDataCategorySlug(slug, limit) {
    return this.#publicQuery()
      .whereIn('publication.data_category_id', this.db('data_categories').select('id').where('slug', slug))
      .select(this.#cardSelect())
      .orderBy('publication.created_at', 'desc')
      .limit(limit);
  }

  async findByKey({ id, slug }, viewer = null) {
    const query = this.#publicQuery({ includeAdminOnly: Boolean(viewer?.isAdmin), includeVersions: true })
      .leftJoin('licenses', 'licenses.id', 'publication.license_id')
      .select([...CARD_COLUMNS, 'publication.*', 'licenses.name as license_name', 'licenses.url as license_url']);
    if (id) query.where('publication.id', id);
    else query.where('publication.slug', slug);
    return query.first();
  }

  incrementVisits(id) {
    return this.db('publication').where({ id }).increment('visits', 1);
  }

  /** Public version chain of a resource: the root + its approved versions, ordered. */
  async versionChain(publication) {
    const rootId = publication.is_version && publication.parent_id ? publication.parent_id : publication.id;
    const rows = await this.db('publication')
      .where((builder) => builder.where('publication.id', rootId).orWhere('publication.parent_id', rootId))
      .where('publication.is_active', 'Active')
      .where('publication.is_approved', true)
      .whereNull('publication.deleted_at')
      .select('id', 'slug', 'title', 'is_version', 'version_no', 'parent_id', 'created_at')
      .orderBy([{ column: 'is_version', order: 'asc' }, { column: 'version_no', order: 'asc' }]);
    return rows;
  }

  /** Admin version chain: every status. */
  async versionChainForAdmin(publication) {
    const rootId = publication.is_version && publication.parent_id ? publication.parent_id : publication.id;
    return this.db('publication')
      .where((builder) => builder.where('publication.id', rootId).orWhere('publication.parent_id', rootId))
      .select('id', 'slug', 'title', 'is_version', 'version_no', 'parent_id', 'created_at', 'is_approved', 'is_rejected', 'is_active')
      .orderBy([{ column: 'is_version', order: 'asc' }, { column: 'version_no', order: 'asc' }]);
  }

  /** Next monotonic version number for a root resource (never reuses deleted numbers). */
  async nextVersionNo(rootId) {
    const row = await this.db('publication').where({ parent_id: rootId, is_version: true }).max({ max: 'version_no' }).first();
    return Math.max(1, Number(row?.max ?? 1)) + 1;
  }

  /** Admin view: one publication regardless of approval/active state. */
  findForAdmin(id) {
    return this.db('publication')
      .leftJoin('author', 'author.id', 'publication.author_id')
      .leftJoin('file_type', 'file_type.id', 'publication.file_type_id')
      .leftJoin('sub_thematic_area', 'sub_thematic_area.id', 'publication.sub_thematic_area_id')
      .leftJoin('thematic_area', 'thematic_area.id', 'sub_thematic_area.thematic_area_id')
      .leftJoin('data_categories', 'data_categories.id', 'publication.data_category_id')
      .leftJoin('licenses', 'licenses.id', 'publication.license_id')
      .leftJoin('users as submitter', 'submitter.id', 'publication.user_id')
      .where('publication.id', id)
      .select([...CARD_COLUMNS, 'publication.*', 'licenses.name as license_name', 'licenses.url as license_url',
        'submitter.name as submitter_name', 'submitter.email as submitter_email', 'submitter.photo as submitter_photo',
        'submitter.is_photo_external as submitter_photo_external'])
      .first();
  }

  /** Admin view: every comment with its moderation status. */
  allCommentsOf(publicationId) {
    return this.db('publication_comments')
      .leftJoin('users', 'users.id', 'publication_comments.user_id')
      .where({ publication_id: publicationId })
      .select('publication_comments.id', 'publication_comments.comment', 'publication_comments.created_at', 'publication_comments.status', 'publication_comments.parent_id',
        'publication_comments.user_id', 'users.name as user_name', 'users.photo', 'users.is_photo_external')
      .orderBy('publication_comments.created_at', 'desc');
  }

  tagsOf(publicationId) {
    return this.db('publication_tags').join('tags', 'tags.id', 'publication_tags.tag_id').where('publication_tags.publication_id', publicationId).select('tags.id', 'tags.slug', 'tags.tag_text');
  }

  countriesOf(publicationId) {
    return this.db('publication_countries').join('country', 'country.id', 'publication_countries.country_id').where('publication_countries.publication_id', publicationId).select('country.id', 'country.slug', 'country.name', 'country.iso_code');
  }

  attachmentsOf(publicationId) {
    return this.db('publication_attachments').where({ publication_id: publicationId }).whereNull('deleted_at').select('id', 'file', 'description', 'original_filename');
  }

  approvedCommentsOf(publicationId) {
    return this.db('publication_comments')
      .leftJoin('users', 'users.id', 'publication_comments.user_id')
      .where({ publication_id: publicationId, 'publication_comments.status': 'approved' })
      .select('publication_comments.id', 'publication_comments.comment', 'publication_comments.created_at', 'publication_comments.parent_id', 'publication_comments.user_id', 'users.name as user_name', 'users.photo', 'users.is_photo_external')
      .orderBy('publication_comments.created_at', 'asc');
  }

  /** Related records: same tags first, then same thematic area, random within each group. */
  async related(publication, tagIds, limit = 10) {
    const exclude = [publication.id];
    const picked = [];

    if (tagIds.length) {
      const byTag = await this.#publicQuery()
        .whereIn('publication.id', this.db('publication_tags').select('publication_id').whereIn('tag_id', tagIds))
        .whereNotIn('publication.id', exclude)
        .select(this.#cardSelect())
        .orderBy(randomOrder(this.db))
        .limit(limit);
      picked.push(...byTag);
    }

    if (picked.length < limit && publication.thematic_area_id) {
      const byTheme = await this.#publicQuery()
        .where('thematic_area.id', publication.thematic_area_id)
        .whereNotIn('publication.id', [...exclude, ...picked.map((row) => row.id)])
        .select(this.#cardSelect())
        .orderBy(randomOrder(this.db))
        .limit(limit - picked.length);
      picked.push(...byTheme);
    }

    return picked;
  }

  autocomplete(term, limit = 10) {
    return this.#publicQuery().whereILike('publication.title', `%${term}%`).select('publication.id', 'publication.slug', 'publication.title').orderBy('publication.visits', 'desc').limit(limit);
  }

  async toggleFavourite(publicationId, userId, wanted) {
    const existing = await this.db('favourites').where({ publication_id: publicationId, user_id: userId }).first();
    if (wanted && !existing) await this.db('favourites').insert({ publication_id: publicationId, user_id: userId });
    if (!wanted && existing) await this.db('favourites').where({ id: existing.id }).del();
    return wanted;
  }

  isFavourite(publicationId, userId) {
    return this.db('favourites').where({ publication_id: publicationId, user_id: userId }).first().then(Boolean);
  }

  addComment(publicationId, userId, comment, autoApprove, parentId = null) {
    return this.db('publication_comments').insert({ publication_id: publicationId, user_id: userId, comment, parent_id: parentId, status: autoApprove ? 'approved' : 'pending', created_at: now() });
  }
}
