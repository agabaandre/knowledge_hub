/**
 * Catalogue of feature modules.
 *
 * A module is a self-contained feature with a backend part (API routes) and a
 * frontend part (pages + navigation). Each site decides which modules are on.
 * `core: true` modules cannot be switched off because the portal cannot work
 * without them.
 */
export const MODULES = Object.freeze({
  publications: {
    label: 'Publications & records search',
    core: true,
    description: 'Resource records, search, tags, thematic areas, favourites and comments.',
  },
  'health-topics': {
    label: 'Health topics & emergencies',
    core: false,
    description: 'Topic pages built from tags flagged as health topics or emergencies.',
  },
  forums: {
    label: 'Discussion forums',
    core: false,
    description: 'Public forum threads, replies and likes.',
  },
  communities: {
    label: 'Communities of practice',
    core: false,
    description: 'Member groups with their own publications, forums and events.',
  },
  events: {
    label: 'Events',
    core: false,
    description: 'Events calendar shown on the home page and in the More menu.',
  },
  courses: {
    label: 'Courses',
    core: false,
    description: 'Learning courses synced from an LMS.',
  },
  countries: {
    label: 'Member states / administrative units',
    core: false,
    description: 'Country or administrative-unit pages with linked resources.',
  },
  authors: {
    label: 'Contributors',
    core: false,
    description: 'Author and organisation profile pages.',
  },
  faqs: {
    label: 'FAQs',
    core: false,
    description: 'Frequently asked questions page.',
  },
  federation: {
    label: 'Federation (partner hubs)',
    core: false,
    description: 'Browse content syndicated from partner country hubs.',
  },
  'ai-search': {
    label: 'AI search insights',
    core: false,
    description: 'AI generated search summaries and chat.',
  },
});

export const MODULE_NAMES = Object.freeze(Object.keys(MODULES));

export const isKnownModule = (name) => Object.hasOwn(MODULES, name);

export const isCoreModule = (name) => Boolean(MODULES[name]?.core);

/**
 * Merge default flags with site + database overrides. Core modules are always on.
 * @param {...Record<string, boolean>} layers lowest priority first
 */
export const resolveModuleFlags = (...layers) => {
  const flags = {};
  for (const name of MODULE_NAMES) {
    flags[name] = true;
  }
  for (const layer of layers) {
    for (const [name, enabled] of Object.entries(layer ?? {})) {
      if (isKnownModule(name)) flags[name] = Boolean(enabled);
    }
  }
  for (const name of MODULE_NAMES) {
    if (isCoreModule(name)) flags[name] = true;
  }
  return flags;
};
