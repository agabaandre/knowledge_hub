/**
 * Slug helpers compatible with the Laravel `SeoSlugger` output so existing
 * URLs keep resolving after migration.
 */
const MAX_SLUG_LENGTH = 180;

export const slugify = (text, maxLength = MAX_SLUG_LENGTH) => {
  const slug = String(text ?? '')
    .normalize('NFKD')
    .replace(/[̀-ͯ]/g, '')
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '');

  return slug.slice(0, maxLength).replace(/-+$/g, '');
};

/**
 * Laravel stored records as `{slug}` and falls back to numeric ids. Accept both.
 * @returns {{ id: number|null, slug: string|null }}
 */
export const parseRecordKey = (key) => {
  const value = String(key ?? '').trim();
  if (value === '') return { id: null, slug: null };
  if (/^\d+$/.test(value)) return { id: Number(value), slug: null };
  return { id: null, slug: value };
};
