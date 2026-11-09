import { likePattern } from '../../core/db/helpers.js';

/**
 * Keyword matching for records search. Port of Laravel's PublicationSearchQuery:
 * match the whole phrase, or every significant word, across title, description,
 * author, tags, theme and sub-theme.
 */
const STOP_WORDS = new Set(['a', 'an', 'the', 'and', 'or', 'of', 'in', 'on', 'for', 'to', 'with', 'by', 'from', 'at', 'is', 'are', 'was', 'were', 'be', 'been', 'about', 'into', 'over', 'under', 'between']);

export const normalizeTerm = (term) =>
  String(term ?? '')
    .replace(/<[^>]*>/g, '')
    .replace(/[\u00A0\u2007\u202F]+/g, ' ')
    .replace(/\s+/g, ' ')
    .trim();

export const significantWords = (term) => {
  const words = normalizeTerm(term)
    .split(' ')
    .map((word) => word.replace(/^[.,;:!?"'()[\]{}]+|[.,;:!?"'()[\]{}]+$/g, ''))
    .filter((word) => word.length >= 2);
  const meaningful = words.filter((word) => !STOP_WORDS.has(word.toLowerCase()));
  return [...new Set(meaningful.length ? meaningful : words)];
};

const tokenMatch = (db) => (builder, token) => {
  const pattern = likePattern(token);
  builder
    .whereILike('publication.title', pattern)
    .orWhereILike('publication.description', pattern)
    .orWhereILike('publication.author_affiliation', pattern)
    .orWhereILike('publication.associated_authors', pattern)
    .orWhereIn('publication.author_id', db('author').select('id').whereILike('name', pattern))
    .orWhereIn('publication.sub_thematic_area_id', db('sub_thematic_area').select('id').whereILike('description', pattern))
    .orWhereIn(
      'publication.sub_thematic_area_id',
      db('sub_thematic_area').select('sub_thematic_area.id').join('thematic_area', 'thematic_area.id', 'sub_thematic_area.thematic_area_id').whereILike('thematic_area.description', pattern),
    )
    .orWhereIn('publication.id', db('publication_tags').select('publication_id').whereIn('tag_id', db('tags').select('id').whereILike('tag_text', pattern)));
};

/** Apply the keyword filter to a knex query on the `publication` table. */
export const applySearchTerm = (db, query, term) => {
  const phrase = normalizeTerm(term);
  if (phrase === '') return query;

  const words = significantWords(phrase);
  const match = tokenMatch(db);

  return query.where((outer) => {
    outer.where((phraseQuery) => match(phraseQuery, phrase));
    if (words.length > 1) {
      outer.orWhere((allWords) => {
        for (const word of words) allWords.where((wordQuery) => match(wordQuery, word));
      });
    }
  });
};
