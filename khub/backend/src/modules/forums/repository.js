import { countOf, now } from '../../core/db/helpers.js';
import { likePattern } from '../../core/db/helpers.js';

const LIST_COLUMNS = ['forums.id', 'forums.slug', 'forums.forum_title', 'forums.forum_image', 'forums.forum_description', 'forums.created_at', 'forums.views', 'forums.created_by', 'forums.is_pinned', 'forums.is_locked', 'users.name as author_name', 'users.photo', 'users.is_photo_external'];

export class ForumsRepository {
  constructor(db) {
    this.db = db;
  }

  #publicQuery() {
    return this.db('forums').leftJoin('users', 'users.id', 'forums.created_by').where('forums.is_approved', true).where('forums.is_rejected', false).whereNull('forums.deleted_at');
  }

  async list({ term, page, per_page: perPage }) {
    const query = this.#publicQuery();
    if (term) query.where((builder) => builder.whereILike('forums.forum_title', likePattern(term)).orWhereILike('forums.forum_description', likePattern(term)));
    const total = await countOf(query);
    const rows = await query
      .select(LIST_COLUMNS)
      .select(this.db('forum_comments').count('*').whereRaw('forum_comments.forum_id = forums.id').as('comment_count'))
      .select(this.db('forum_likes').count('*').whereRaw('forum_likes.forum_id = forums.id').as('like_count'))
      .orderBy([{ column: 'forums.is_pinned', order: 'desc' }, { column: 'forums.created_at', order: 'desc' }])
      .limit(perPage)
      .offset((page - 1) * perPage);
    return { rows, total };
  }

  findByKey({ id, slug }) {
    const query = this.#publicQuery()
      .select(LIST_COLUMNS)
      .select(this.db('forum_likes').count('*').whereRaw('forum_likes.forum_id = forums.id').as('like_count'));
    return (id ? query.where('forums.id', id) : query.where('forums.slug', slug)).first();
  }

  incrementViews(id) {
    return this.db('forums').where({ id }).increment('views', 1);
  }

  commentsOf(forumId) {
    return this.db('forum_comments')
      .leftJoin('users', 'users.id', 'forum_comments.created_by')
      .where({ forum_id: forumId, 'forum_comments.status': 'approved' })
      .select('forum_comments.id', 'forum_comments.comment', 'forum_comments.created_at', 'forum_comments.parent_id', 'forum_comments.created_by', 'users.name as author_name', 'users.photo', 'users.is_photo_external')
      .select(this.db('forum_comment_likes').count('*').whereRaw('forum_comment_likes.forum_comment_id = forum_comments.id').as('like_count'))
      .orderBy('forum_comments.created_at', 'asc');
  }

  tagsOf(forumId) {
    return this.db('forum_tags').where({ forum_id: forumId }).pluck('tag');
  }

  addComment(forumId, userId, comment, parentId, autoApprove) {
    return this.db('forum_comments').insert({ forum_id: forumId, created_by: userId, comment, parent_id: parentId ?? null, status: autoApprove ? 'approved' : 'pending', created_at: now() });
  }

  /** Start a discussion thread — goes to the approvals inbox like Laravel. */
  async create({ title, description, tags, userId, slug }) {
    const [inserted] = await this.db('forums').insert({
      forum_title: title,
      forum_description: description,
      slug,
      created_by: userId,
      is_approved: false,
      is_rejected: false,
      status: 'pending',
      views: 0,
      is_pinned: false,
      is_locked: false,
      created_at: now(),
    }).returning('id');
    const id = typeof inserted === 'object' ? inserted.id : inserted;
    for (const tag of tags) {
      try {
        await this.db('forum_tags').insert({ forum_id: id, tag });
      } catch {
        // Legacy table without auto-increment id (Laravel-era schema).
        const max = await this.db('forum_tags').max({ max: 'id' }).first();
        await this.db('forum_tags').insert({ id: Number(max?.max ?? 0) + 1, forum_id: id, tag });
      }
    }
    return id;
  }

  async isLikedBy(forumId, userId) {
    if (!userId) return false;
    return Boolean(await this.db('forum_likes').where({ forum_id: forumId, user_id: userId }).first('id'));
  }

  async toggleCommentLike(commentId, userId) {
    const existing = await this.db('forum_comment_likes').where({ forum_comment_id: commentId, user_id: userId }).first();
    if (existing) await this.db('forum_comment_likes').where({ id: existing.id }).del();
    else await this.db('forum_comment_likes').insert({ forum_comment_id: commentId, user_id: userId, created_at: now(), updated_at: now() });
    const row = await this.db('forum_comment_likes').where({ forum_comment_id: commentId }).count({ total: '*' }).first();
    return { liked: !existing, likeCount: Number(row.total) };
  }

  async toggleLike(forumId, userId) {
    const existing = await this.db('forum_likes').where({ forum_id: forumId, user_id: userId }).first();
    if (existing) await this.db('forum_likes').where({ id: existing.id }).del();
    else await this.db('forum_likes').insert({ forum_id: forumId, user_id: userId, created_at: now(), updated_at: now() });
    const row = await this.db('forum_likes').where({ forum_id: forumId }).count({ total: '*' }).first();
    return { liked: !existing, likeCount: Number(row.total) };
  }
}
