import fs from 'node:fs/promises';
import { createWriteStream } from 'node:fs';
import path from 'node:path';
import { pipeline } from 'node:stream/promises';
import { cleanRelativePath, CONTENT_PREFIXES } from './paths.js';

/**
 * File storage for one site. Only the "internal" (host path) driver is
 * implemented: it is what every current deployment uses. Cloud drivers
 * (S3, GCS, Azure, SharePoint, SFTP) plug in by implementing the same four
 * methods: `exists`, `absolutePath`, `write`, `remove`.
 */
export class HubStorage {
  constructor(site) {
    this.site = site;
    this.root = site.paths.files;
  }

  async ensureDirectories() {
    await fs.mkdir(this.site.paths.files, { recursive: true });
    await fs.mkdir(this.site.paths.sqlBackups, { recursive: true });
    for (const prefix of Object.values(CONTENT_PREFIXES)) {
      await fs.mkdir(path.join(this.root, prefix), { recursive: true });
    }
  }

  absolutePath(relative) {
    const clean = cleanRelativePath(relative);
    const absolute = path.resolve(this.root, clean);
    if (!absolute.startsWith(path.resolve(this.root) + path.sep)) {
      throw new Error('Path escapes the storage root');
    }
    return absolute;
  }

  /** @returns {Promise<string|null>} absolute path if a regular file exists */
  async resolveReadableFile(relative) {
    try {
      const absolute = this.absolutePath(relative);
      const stat = await fs.stat(absolute);
      return stat.isFile() ? absolute : null;
    } catch {
      return null;
    }
  }

  async exists(relative) {
    return (await this.resolveReadableFile(relative)) !== null;
  }

  /** Store a readable stream (or buffer) under the relative path. */
  async write(relative, source) {
    const absolute = this.absolutePath(relative);
    await fs.mkdir(path.dirname(absolute), { recursive: true });
    if (Buffer.isBuffer(source)) await fs.writeFile(absolute, source);
    else await pipeline(source, createWriteStream(absolute));
    return cleanRelativePath(relative);
  }

  /** File names directly inside a folder (no recursion), newest first by name. */
  async list(relative) {
    const fs = await import('node:fs/promises');
    try {
      const entries = await fs.readdir(this.absolutePath(relative), { withFileTypes: true });
      return entries.filter((entry) => entry.isFile()).map((entry) => entry.name).sort().reverse();
    } catch {
      return [];
    }
  }

  async remove(relative) {
    const absolute = this.absolutePath(relative);
    await fs.rm(absolute, { force: true });
  }

  /** Total bytes and file count under a prefix (used by the admin overview). */
  async usage(relative = '') {
    const start = relative ? this.absolutePath(relative) : this.root;
    let bytes = 0;
    let files = 0;
    const walk = async (dir) => {
      let entries;
      try {
        entries = await fs.readdir(dir, { withFileTypes: true });
      } catch {
        return;
      }
      for (const entry of entries) {
        const full = path.join(dir, entry.name);
        if (entry.isDirectory()) await walk(full);
        else if (entry.isFile()) {
          files += 1;
          bytes += (await fs.stat(full)).size;
        }
      }
    };
    await walk(start);
    return { bytes, files };
  }
}

/** Public URL of a stored file, served by the media route. */
export const mediaUrl = (site, relative) => `${site.publicUrl}/hub-media/${cleanRelativePath(relative)}`;
