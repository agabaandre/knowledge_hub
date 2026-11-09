import crypto from 'node:crypto';
import fs from 'node:fs/promises';
import { HubStorage } from '../storage/hub-storage.js';
import { now } from '../db/helpers.js';

/** Laravel stored attachment files as bare names under uploads/publications. */
export const attachmentRelativePath = (file) => {
  const text = String(file ?? '').trim();
  if (text === '' || /^(https?:)?\/\//i.test(text)) return null; // external links are not managed files
  return text.includes('/') ? text : `uploads/publications/${text}`;
};

const sha256Of = async (absolutePath) => crypto.createHash('sha256').update(await fs.readFile(absolutePath)).digest('hex');

/**
 * Document lifecycle for publication attachments, industry-standard:
 *  - integrity: SHA-256 checksum recorded on registration, re-verifiable on demand;
 *  - audit: size, type, uploader, timestamps;
 *  - retention: a "delete after" date, enforced by the automated sweep (disposition
 *    removes the stored file and the registry row, and is logged).
 */
export class DocumentsService {
  constructor(site, logger = null) {
    this.site = site;
    this.db = site.db;
    this.storage = new HubStorage(site);
    this.logger = logger;
  }

  async #statsFor(row) {
    const relative = attachmentRelativePath(row.file);
    if (!relative || !(await this.storage.exists(relative))) return null;
    const absolute = this.storage.absolutePath(relative);
    const stat = await fs.stat(absolute);
    return { checksum: await sha256Of(absolute), size: stat.size };
  }

  /** Recompute the checksum and compare with the registry (integrity verification). */
  async verify(row) {
    const stats = await this.#statsFor(row);
    if (!stats) return { ok: false, state: 'missing', message: 'The stored file could not be found (external link or removed).' };
    if (!row.checksum_sha256) {
      await this.db('publication_attachments').where({ id: row.id }).update({ checksum_sha256: stats.checksum, size_bytes: stats.size, updated_at: now() });
      return { ok: true, state: 'registered', checksum: stats.checksum };
    }
    const match = stats.checksum === row.checksum_sha256;
    return match
      ? { ok: true, state: 'verified', checksum: stats.checksum }
      : { ok: false, state: 'mismatch', message: 'Checksum mismatch — the file changed since it was registered.', checksum: stats.checksum, expected: row.checksum_sha256 };
  }

  /** Delete the stored file (best effort) and the registry row. */
  async dispose(row) {
    const relative = attachmentRelativePath(row.file);
    if (relative) await this.storage.remove(relative).catch(() => {});
    await this.db('publication_attachments').where({ id: row.id }).del();
  }

  /**
   * Automated pass: backfill integrity metadata for unregistered files and
   * dispose of every document whose retention date has passed.
   */
  async sweep() {
    if (!(await this.db.schema.hasTable('publication_attachments'))) return { registered: 0, disposed: 0 };
    let registered = 0;
    const unregistered = await this.db('publication_attachments').whereNull('checksum_sha256').limit(200);
    for (const row of unregistered) {
      const stats = await this.#statsFor(row).catch(() => null);
      if (!stats) continue;
      await this.db('publication_attachments').where({ id: row.id }).update({ checksum_sha256: stats.checksum, size_bytes: stats.size, updated_at: now() });
      registered += 1;
    }

    const expired = await this.db('publication_attachments').whereNotNull('retention_until').where('retention_until', '<=', new Date());
    for (const row of expired) {
      await this.dispose(row);
      this.logger?.info({ site: this.site.id, attachment: row.id, file: row.file }, 'document retention: disposed of expired attachment');
    }
    return { registered, disposed: expired.length };
  }
}

/** Boot-time scheduler: sweep every enabled site now and twice a day. */
export const startRetentionSweeps = (context, intervalMs = 12 * 60 * 60 * 1000) => {
  const run = async () => {
    for (const site of context.sites.all()) {
      try {
        const result = await new DocumentsService(site, context.logger).sweep();
        if (result.registered || result.disposed) {
          context.logger.info({ site: site.id, ...result }, 'document retention sweep');
        }
      } catch (error) {
        context.logger.warn({ site: site.id, err: error.message }, 'document retention sweep failed');
      }
    }
  };
  run();
  const timer = setInterval(run, intervalMs);
  timer.unref?.();
  return timer;
};
