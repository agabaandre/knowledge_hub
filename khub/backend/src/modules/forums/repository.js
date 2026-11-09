import { countOf, now } from '../../core/db/helpers.js';
import { likePattern } from '../../core/db/helpers.js';

const LIST_COLUMNS = ['forums.id', 'forums.slug', 'forums.forum_title', 'forums.forum_image', 'forums.forum_description', 'forums.created_at', 'forums.views', 'forums.created_by', 'users.name as author_name', 'users.photo', 'users.is_photo_external'];

export class ForumsRepository {
  constructor(db) {
    this.db = db;
  }

  #publicQuery() {
    return this.db('forums').leftJoin('users', 'users.id', 'forums.created_by').where('forums.is_approved', true).where('forums.is_rejected', false);
  }

  async list({ term, page, per_page: perPage }) {
    const query = this.#publicQuery();
    if (term) query.where((builder) => builder.whereILike('forums.forum_title', likePattern(term)).orWhereILike('forums.forum_description', likePattern(term)));
    const total = await countOf(query);
    const rows = await query
      .select(LIST_COLUMNS)
      .select(this.db('forum_comments').count('*').whereRaw('forum_comments.forum_id = forums.id').as('comment_count'))
      .select(this.db('forum_likes').count('*').whereRaw('forum_likes.forum_id = forums.id').as('like_count'))
      .orderBy('forums.created_at', 'desc')
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

  async toggleLike(forumId, userId) {
    const existing = await this.db('forum_likes').where({ forum_id: forumId, user_id: userId }).first();
    if (existing) await this.db('forum_likes').where({ id: existing.id }).del();
    else await this.db('forum_likes').insert({ forum_id: forumId, user_id: userId, created_at: now(), updated_at: now() });
    const row = await this.db('forum_likes').where({ forum_id: forumId }).count({ total: '*' }).first();
    return { liked: !existing, likeCount: Number(row.total) };
  }
}
