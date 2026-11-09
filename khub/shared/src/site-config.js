/**
 * Shape and defaults of a `sites/<name>/site.json` file.
 *
 * A site is one hub instance (continental portal, a country hub, ...). The
 * folder only carries configuration and customisation; data (uploads, SQL
 * backups) always lives under HUB_DATA_ROOT/{storage id}.
 */
export const SITE_CONFIG_DEFAULTS = Object.freeze({
  /** false = folder is ignored (no routes, no storage folders, no DB connection) */
  enabled: true,
  name: 'Knowledge Hub',
  /** URL path prefix for path-based resolution, e.g. "nigeria" → khub.org/nigeria */
  pathPrefix: '',
  /** Hostnames for host-based resolution, e.g. ["nigeria.khub.org"] */
  hosts: [],
  /** Storage identifier under HUB_DATA_ROOT; derived from APP_URL + pathPrefix when empty */
  storageId: '',
  /** Optional dedicated database; falls back to the global DB_* connection */
  database: null,
  /** Module toggles; core modules are always on. Admin overrides in DB win. */
  modules: {},
  /** Branding fallbacks used before the `setting` table is read */
  branding: {
    primaryColor: '#006239',
    secondaryColor: '#413C3C',
    logo: '',
    favicon: '',
  },
  /** Locales offered in the language switcher; first one is the default */
  locales: ['en'],
  /** Feature switches that are not modules */
  features: {
    statesEnabled: false,
    adminUnitsEnabled: true,
    seoFriendlyUrls: true,
  },
});

const isPlainObject = (value) => value !== null && typeof value === 'object' && !Array.isArray(value);

const deepMerge = (base, override) => {
  const result = { ...base };
  for (const [key, value] of Object.entries(override ?? {})) {
    result[key] = isPlainObject(value) && isPlainObject(base[key]) ? deepMerge(base[key], value) : value;
  }
  return result;
};

/**
 * Validate the raw JSON of a site.json and return a complete config.
 * Throws with a readable message so a broken site file fails at boot, not at request time.
 */
export const normalizeSiteConfig = (raw, folderName) => {
  if (!isPlainObject(raw)) throw new Error(`sites/${folderName}/site.json must contain a JSON object`);

  const config = deepMerge(SITE_CONFIG_DEFAULTS, raw);
  config.id = folderName;

  if (typeof config.pathPrefix !== 'string') throw new Error(`sites/${folderName}: pathPrefix must be a string`);
  config.pathPrefix = config.pathPrefix.replace(/^\/+|\/+$/g, '');
  if (config.pathPrefix && !/^[a-z0-9-]+$/.test(config.pathPrefix)) {
    throw new Error(`sites/${folderName}: pathPrefix may only contain a-z, 0-9 and "-"`);
  }

  if (!Array.isArray(config.hosts) || config.hosts.some((h) => typeof h !== 'string')) {
    throw new Error(`sites/${folderName}: hosts must be an array of strings`);
  }
  config.hosts = config.hosts.map((h) => h.toLowerCase());

  if (!Array.isArray(config.locales) || config.locales.length === 0) {
    throw new Error(`sites/${folderName}: locales must be a non-empty array`);
  }

  if (config.database !== null && !isPlainObject(config.database)) {
    throw new Error(`sites/${folderName}: database must be null or an object`);
  }

  return config;
};
