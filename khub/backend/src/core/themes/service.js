import fs from 'node:fs/promises';
import path from 'node:path';
import AdmZip from 'adm-zip';
import { sanitizeSiteId } from '../site/identifier.js';
import { HttpError } from '../http/errors.js';

/**
 * Uploadable themes. A theme is a folder under
 * {HUB_DATA_ROOT}/{site}/themes/{slug}/ (data lives outside sites/):
 *   theme.json   required manifest (name, version, tokens, stylesheet)
 *   theme.css    optional stylesheet, served at /hub-theme/{slug}/theme.css
 *   assets/*     optional images/fonts referenced by the stylesheet
 *
 * The active slug is stored in `setting.site_theme`. Anything that is not an
 * uploaded slug (including Laravel's legacy "theme1." value) means "classic",
 * the built-in look. docs/THEME_GUIDELINES.md documents the package format.
 */
const ALLOWED_EXTENSIONS = new Set(['.json', '.css', '.png', '.jpg', '.jpeg', '.svg', '.gif', '.webp', '.woff', '.woff2', '.ttf', '.md', '.txt']);
const MAX_UNPACKED_BYTES = 10 * 1024 * 1024;
const TOKEN_KEYS = ['primaryColor', 'secondaryColor', 'textColor', 'linkColor', 'navStyle', 'bodyFont', 'headingFont'];

export const CLASSIC_THEME = Object.freeze({
  slug: 'classic',
  name: 'Knowledge Hub Classic',
  version: 'built-in',
  description: 'The default Africa CDC look shipped with the portal.',
  builtIn: true,
  tokens: {},
});

export class ThemeService {
  constructor(site) {
    this.site = site;
    this.root = path.join(site.paths.siteRoot, 'themes');
  }

  themeDir(slug) {
    const clean = sanitizeSiteId(slug);
    if (clean !== slug || clean === 'classic') throw new HttpError(422, `Invalid theme slug "${slug}".`);
    return path.join(this.root, clean);
  }

  async list() {
    const themes = [CLASSIC_THEME];
    let entries = [];
    try {
      entries = await fs.readdir(this.root, { withFileTypes: true });
    } catch {
      return themes;
    }
    for (const entry of entries) {
      if (!entry.isDirectory()) continue;
      const manifest = await this.#readManifest(entry.name);
      if (manifest) themes.push(manifest);
    }
    return themes;
  }

  async #readManifest(slug) {
    try {
      const raw = JSON.parse(await fs.readFile(path.join(this.root, slug, 'theme.json'), 'utf8'));
      const hasCss = await fs.stat(path.join(this.root, slug, raw.stylesheet ?? 'theme.css')).then((s) => s.isFile()).catch(() => false);
      return {
        slug,
        name: String(raw.name ?? slug),
        version: String(raw.version ?? '1.0.0'),
        description: String(raw.description ?? ''),
        author: raw.author ? String(raw.author) : undefined,
        builtIn: false,
        tokens: Object.fromEntries(TOKEN_KEYS.filter((key) => typeof raw.tokens?.[key] === 'string').map((key) => [key, raw.tokens[key]])),
        stylesheet: hasCss ? `/hub-theme/${slug}/${raw.stylesheet ?? 'theme.css'}` : null,
      };
    } catch {
      return null;
    }
  }

  /** Active theme for the manifest: classic unless setting.site_theme names an uploaded slug. */
  async active(settings) {
    const slug = sanitizeSiteId(String(settings.site_theme ?? ''));
    const manifest = slug && slug !== 'classic' ? await this.#readManifest(slug) : null;
    return manifest ?? CLASSIC_THEME;
  }

  async activate(slug) {
    if (slug !== 'classic' && !(await this.#readManifest(slug))) throw new HttpError(404, `Theme "${slug}" is not installed.`);
    await this.site.db('setting').update({ site_theme: slug === 'classic' ? '' : slug });
  }

  /** Install a zip: validated manifest, safe paths, size cap. Returns the manifest. */
  async installZip(buffer) {
    const zip = new AdmZip(buffer);
    const entries = zip.getEntries().filter((entry) => !entry.isDirectory);
    const manifestEntry = entries.find((entry) => entry.entryName.replace(/^[^/]*\//, '') === 'theme.json' || entry.entryName === 'theme.json');
    if (!manifestEntry) throw new HttpError(422, 'The zip must contain a theme.json manifest (see the theme guidelines).');
    const prefix = manifestEntry.entryName.slice(0, -'theme.json'.length);

    let raw;
    try {
      raw = JSON.parse(manifestEntry.getData().toString('utf8'));
    } catch {
      throw new HttpError(422, 'theme.json is not valid JSON.');
    }
    const slug = sanitizeSiteId(String(raw.slug ?? raw.name ?? ''));
    if (!slug || slug === 'classic' || slug === 'default') throw new HttpError(422, 'theme.json needs a usable "slug" (letters, numbers, dashes; not "classic").');

    let total = 0;
    const files = [];
    for (const entry of entries) {
      if (!entry.entryName.startsWith(prefix)) continue;
      const relative = entry.entryName.slice(prefix.length);
      const clean = relative.split('/').filter((part) => part && part !== '.' && part !== '..').join('/');
      if (clean !== relative || clean === '') throw new HttpError(422, `Unsafe path in zip: ${entry.entryName}`);
      if (!ALLOWED_EXTENSIONS.has(path.extname(clean).toLowerCase())) throw new HttpError(422, `File type not allowed in a theme: ${clean}`);
      const data = entry.getData();
      total += data.length;
      if (total > MAX_UNPACKED_BYTES) throw new HttpError(422, 'Theme unpacks to more than 10 MB.');
      files.push({ clean, data });
    }

    const dir = this.themeDir(slug);
    await fs.rm(dir, { recursive: true, force: true });
    for (const file of files) {
      const absolute = path.join(dir, file.clean);
      await fs.mkdir(path.dirname(absolute), { recursive: true });
      await fs.writeFile(absolute, file.data);
    }
    return this.#readManifest(slug);
  }

  async remove(slug, settings) {
    if (sanitizeSiteId(String(settings.site_theme ?? '')) === slug) throw new HttpError(422, 'Deactivate this theme before deleting it.');
    const dir = this.themeDir(slug);
    await fs.rm(dir, { recursive: true, force: true });
  }

  /** Absolute path of a theme file for serving; null when missing or unsafe. */
  async resolveFile(slug, relative) {
    try {
      const dir = this.themeDir(slug);
      const clean = relative.split('/').filter((part) => part && part !== '.' && part !== '..').join('/');
      if (!ALLOWED_EXTENSIONS.has(path.extname(clean).toLowerCase())) return null;
      const absolute = path.join(dir, clean);
      const stat = await fs.stat(absolute);
      return stat.isFile() ? absolute : null;
    } catch {
      return null;
    }
  }
}
