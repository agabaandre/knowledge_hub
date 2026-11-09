import { publicationPath, truncate } from '@khub/shared';
import { mediaUrl } from '../../core/storage/hub-storage.js';
import { toBool } from '../../core/db/helpers.js';

const isExternal = (value) => /^(https?:)?\/\//i.test(String(value ?? ''));

/** Laravel stored covers/files as a bare filename under uploads/publications. */
export const publicationFileUrl = (site, value) => {
  const text = String(value ?? '').trim();
  if (text === '') return '';
  if (isExternal(text)) return text;
  const relative = text.includes('/') ? text : `uploads/publications/${text}`;
  return mediaUrl(site, relative);
};

export const userPhotoUrl = (site, user) => {
  const photo = String(user?.photo ?? '').trim();
  if (photo === '' || photo === 'avatar.jpg') return '';
  if (toBool(user.is_photo_external) || isExternal(photo)) return photo;
  return mediaUrl(site, `uploads/users/${photo}`);
};

/** Compact card used by search results, home sections, topic and author pages. */
export const publicationCard = (site, row) => ({
  id: row.id,
  slug: row.slug,
  title: row.title,
  excerpt: truncate(row.description, 220),
  yearPublished: row.year_published,
  cover: toBool(row.cover_is_exteranl) && row.cover ? row.cover : publicationFileUrl(site, row.cover),
  isFeatured: toBool(row.is_featured),
  isVideo: toBool(row.is_video),
  visits: Number(row.visits ?? 0),
  likeCount: Number(row.like_count ?? 0),
  commentCount: Number(row.comment_count ?? 0),
  associatedAuthors: row.associated_authors || '',
  dataCategory: row.data_category_name || '',
  createdAt: row.created_at,
  author: row.author_id ? { id: row.author_id, name: row.author_name } : null,
  fileType: row.file_type_id ? { id: row.file_type_id, name: row.file_type_name, icon: row.file_type_icon } : null,
  subTheme: row.sub_thematic_area_id ? { id: row.sub_thematic_area_id, name: row.sub_theme_name } : null,
  theme: row.thematic_area_id ? { id: row.thematic_area_id, name: row.theme_name } : null,
  path: publicationPath(row),
});

/** Full record for the resource page. */
export const publicationDetail = (site, row, { tags, attachments, comments, countries }) => ({
  ...publicationCard(site, row),
  description: row.description,
  file: publicationFileUrl(site, row.publication),
  fileIsEmbedded: toBool(row.is_embedded),
  citation: {
    link: row.citation_link,
    authors: row.citation_authors || row.associated_authors,
    affiliation: row.author_affiliation,
    doi: row.doi,
    issn: row.issn,
    isbn: row.isbn,
    publisher: row.publisher,
    funder: row.funder,
    journal: row.journal_name ? { name: row.journal_name, volume: row.journal_volume, issue: row.journal_issue, pages: row.journal_pages } : null,
  },
  license: row.license_id ? { id: row.license_id, name: row.license_name, url: row.license_url } : null,
  showDisclaimer: toBool(row.show_disclaimer),
  isVersion: toBool(row.is_version),
  versionNo: Number(row.version_no ?? 1) || 1,
  parentId: row.parent_id ?? null,
  updatedAt: row.updated_at,
  tags: tags.map((tag) => ({ id: tag.id, slug: tag.slug, name: tag.tag_text })),
  countries: countries.map((country) => ({ id: country.id, slug: country.slug, name: country.name, isoCode: country.iso_code })),
  attachments: attachments.map((file) => ({ id: file.id, name: file.original_filename || file.description || file.file, url: publicationFileUrl(site, file.file) })),
  comments: comments.map((comment) => ({ id: comment.id, comment: comment.comment, createdAt: comment.created_at, parentId: comment.parent_id ?? null, user: { id: comment.user_id, name: comment.user_name, photo: userPhotoUrl(site, comment) } })),
});
