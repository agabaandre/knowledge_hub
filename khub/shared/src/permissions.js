/**
 * Admin permission catalogue — every admin page, link, and edit action is one
 * named permission. Permissions attach to roles (Spatie-style tables:
 * permissions / role_has_permissions) and any permission can be revoked from
 * any role on the Roles admin page. Access *levels* (overall/rcc/country/…)
 * still control geographic scope; permissions control which functions a role
 * may use at all.
 */
export const PERMISSION_GROUPS = Object.freeze([
  {
    group: 'Dashboard & monitoring',
    permissions: [
      { name: 'admin.dashboard.view', label: 'View the admin dashboard' },
      { name: 'admin.analytics.view', label: 'View analytics & insights' },
      { name: 'admin.metrics.view', label: 'View system metrics' },
      { name: 'admin.logs.view', label: 'View user & site access logs' },
      { name: 'admin.security.manage', label: 'Block and unblock suspicious IPs' },
    ],
  },
  {
    group: 'Moderation',
    permissions: [
      { name: 'admin.approvals.view', label: 'View the approvals inbox' },
      { name: 'admin.approvals.review', label: 'Approve or reject content' },
    ],
  },
  {
    group: 'Publications',
    permissions: [
      { name: 'admin.publications.view', label: 'View & search all resources' },
      { name: 'admin.publications.edit', label: 'Manage resources (feature, activate, delete)' },
      { name: 'admin.documents.view', label: 'View the document registry' },
      { name: 'admin.documents.manage', label: 'Manage documents (retention, verify, delete)' },
      { name: 'admin.communities.view', label: 'View communities of practice' },
      { name: 'admin.communities.manage', label: 'Manage communities & memberships' },
      { name: 'admin.forums.view', label: 'View forum management' },
      { name: 'admin.forums.manage', label: 'Manage forums (pin, lock, trash)' },
    ],
  },
  {
    group: 'System configuration',
    permissions: [
      { name: 'admin.settings.view', label: 'View system configurations' },
      { name: 'admin.settings.edit', label: 'Edit system configurations' },
      { name: 'admin.themes.manage', label: 'Upload, activate and delete themes' },
      { name: 'admin.ai.manage', label: 'Configure AI providers' },
      { name: 'admin.modules.manage', label: 'Enable or disable modules' },
      { name: 'admin.badges.manage', label: 'Configure contributor badges' },
      { name: 'admin.languages.manage', label: 'Manage site languages' },
      { name: 'admin.storage.view', label: 'View storage management' },
      { name: 'admin.storage.manage', label: 'Edit storage & backup settings' },
      { name: 'admin.maps.view', label: 'View maps management' },
      { name: 'admin.sites.view', label: 'View hub sites' },
      { name: 'admin.sites.manage', label: 'Create and manage hub sites' },
      { name: 'admin.federation.view', label: 'View federated hubs' },
      { name: 'admin.federation.manage', label: 'Connect and sync federated hubs' },
    ],
  },
  {
    group: 'Users & access',
    permissions: [
      { name: 'admin.users.view', label: 'View users' },
      { name: 'admin.users.edit', label: 'Edit users (status, role, country)' },
      { name: 'admin.roles.view', label: 'View roles & permissions' },
      { name: 'admin.roles.edit', label: 'Edit roles & revoke permissions' },
    ],
  },
  {
    group: 'Content lists',
    permissions: [
      { name: 'admin.lists.view', label: 'View dropdown lists (taxonomy)' },
      { name: 'admin.lists.edit', label: 'Edit dropdown lists (taxonomy)' },
      { name: 'admin.mailing.view', label: 'View the mailing list' },
      { name: 'admin.mailing.edit', label: 'Remove mailing list subscribers' },
    ],
  },
]);

export const ALL_PERMISSIONS = Object.freeze(PERMISSION_GROUPS.flatMap((group) => group.permissions.map((permission) => permission.name)));

/** Default grants when a role has no explicit assignments yet (first seed). */
export const DEFAULT_ROLE_GRANTS = Object.freeze({
  Admin: [...ALL_PERMISSIONS],
  // (new catalogue entries are granted to Admin by the permission-sync migration)
  'RCC Admin': ['admin.dashboard.view', 'admin.approvals.view', 'admin.approvals.review'],
  'MS Manager': ['admin.dashboard.view', 'admin.approvals.view', 'admin.approvals.review'],
  Reviewer: ['admin.approvals.view', 'admin.approvals.review'],
});

export const isKnownPermission = (name) => ALL_PERMISSIONS.includes(name);
