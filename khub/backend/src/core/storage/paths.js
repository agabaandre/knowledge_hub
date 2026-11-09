import path from 'node:path';

/**
 * Per-site data layout, identical to the Laravel hub:
 *
 *   {HUB_DATA_ROOT}/{storageId}/files/uploads/publications
 *   {HUB_DATA_ROOT}/{storageId}/files/uploads/forums
 *   {HUB_DATA_ROOT}/{storageId}/backups/sql
 */
export const CONTENT_PREFIXES = Object.freeze({
  publications: 'uploads/publications',
  publication_summaries: 'uploads/publications/summaries',
  forums: 'uploads/forums',
  forum_attachments: 'uploads/forum',
  users: 'uploads/users',
  config: 'uploads/config',
});

export const sitePaths = (hubDataRoot, storageId) => {
  const siteRoot = path.join(hubDataRoot, storageId);
  return Object.freeze({
    siteRoot,
    files: path.join(siteRoot, 'files'),
    sqlBackups: path.join(siteRoot, 'backups', 'sql'),
    envBackups: path.join(siteRoot, 'backups', 'env'),
  });
};

/** Reject traversal and normalise separators; returns a clean relative path. */
export const cleanRelativePath = (relative) => {
  const normalized = String(relative ?? '')
    .replace(/\\/g, '/')
    .split('/')
    .filter((segment) => segment !== '' && segment !== '.' && segment !== '..')
    .join('/');
  return normalized;
};
