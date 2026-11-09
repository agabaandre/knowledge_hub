/**
 * Role-based access levels, from narrowest to widest:
 *
 *   guest      unauthenticated — public pages only
 *   member     signed-in user — comment, favourite, submit
 *   moderator  content reviewer ("Reviewer", "Moderator") — approvals for
 *              their own country when one is set on their account
 *   country    "MS Manager", "Country Admin/Moderator" — approvals for their country
 *   rcc        "RCC Admin/Moderator" — approvals for every country in their
 *              region (via their account's country → region)
 *   overall    "Admin", "Overall Administrator", "Super Admin" — everything,
 *              including items with no country attached, and the full admin portal
 *
 * Laravel sketched these tiers (access_levels: Viewer/Country/RCC/Overall) but
 * never implemented the filtering; this is the working version.
 */
export const LEVELS = Object.freeze(['guest', 'member', 'moderator', 'country', 'rcc', 'overall']);

const LEVEL_LABELS = Object.freeze({
  guest: 'Visitor',
  member: 'Member',
  moderator: 'Moderator',
  country: 'Country Moderator',
  rcc: 'RCC Moderator',
  overall: 'Overall Administrator',
});

export const levelForRoles = (roleNames) => {
  const names = roleNames.map((name) => String(name).toLowerCase());
  const has = (...needles) => names.some((name) => needles.some((needle) => name.includes(needle)));

  if (has('overall', 'super admin')) return 'overall';
  if (has('rcc')) return 'rcc';
  if (has('ms manager', 'country')) return 'country';
  // Bare "Admin" (and nothing more specific) is the overall administrator.
  if (names.includes('admin') || has('administrator')) return 'overall';
  if (has('reviewer', 'moderator')) return 'moderator';
  return 'member';
};

/**
 * @param {string[]} roleNames
 * @param {{countryId?: number|null, countryName?: string|null, regionId?: number|null, regionName?: string|null}} geo
 */
export const resolveAccess = (roleNames, geo = {}) => {
  const level = levelForRoles(roleNames);
  const access = {
    level,
    label: LEVEL_LABELS[level],
    canModerate: ['moderator', 'country', 'rcc', 'overall'].includes(level),
    isAdmin: level === 'overall',
    countryId: geo.countryId ?? null,
    countryName: geo.countryName ?? null,
    regionId: geo.regionId ?? null,
    regionName: geo.regionName ?? null,
  };

  access.scopeLabel =
    level === 'overall' ? 'All countries'
    : level === 'rcc' ? (geo.regionName ? `${geo.regionName} region` : 'No region assigned')
    : ['country', 'moderator'].includes(level) ? (geo.countryName ?? 'No country assigned')
    : '';

  return access;
};

/**
 * Country ids a moderator may review, for the approvals queries.
 * @returns {Promise<null | number[]>} null = no restriction (overall admin)
 */
export const allowedCountryIds = async (db, access) => {
  if (access.level === 'overall') return null;
  if (access.level === 'rcc') {
    if (!access.regionId) return [];
    const rows = await db('country').where('region_id', access.regionId).select('id');
    return rows.map((row) => row.id);
  }
  return access.countryId ? [access.countryId] : [];
};
