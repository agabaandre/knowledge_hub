/**
 * Admin panel navigation — the single source of truth shared by the backend
 * (GET /api/admin/nav returns it filtered by the caller's permissions) and the
 * frontend (sidebar, command palette, and the public header's Admin menu all
 * render from it). Every item is gated by one permission from
 * ./permissions.js, so revoking a permission removes the link everywhere.
 */
export const ADMIN_NAV = Object.freeze([
  {
    id: 'overview',
    label: 'Overview',
    items: [
      { path: '/admin', label: 'Dashboard', icon: 'fa-gauge-high', description: 'Live overview of content, people, and activity', permission: 'admin.dashboard.view', exact: true },
      { path: '/admin/metrics', label: 'System Metrics', icon: 'fa-server', description: 'Server health, request latency, and resource usage', permission: 'admin.metrics.view' },
      { path: '/admin/logs', label: 'Access Logs', icon: 'fa-clock-rotate-left', description: 'Sign-ins, registrations, and search activity', permission: 'admin.logs.view' },
    ],
  },
  {
    id: 'content',
    label: 'Content',
    items: [
      { path: '/admin/approvals', label: 'Approvals', icon: 'fa-inbox', description: 'Review pending publications, forums, and comments', permission: 'admin.approvals.view' },
      { path: '/admin/publications', label: 'Manage Resources', icon: 'fa-file-pen', description: 'Search, feature, activate, or delete resources', permission: 'admin.publications.view' },
      { path: '/admin/documents', label: 'Documents', icon: 'fa-folder-open', description: 'Attachment registry: integrity, versions, retention', permission: 'admin.documents.view' },
      { path: '/admin/lists', label: 'Dropdown Lists', icon: 'fa-list-check', description: 'Taxonomies behind every select on the portal', permission: 'admin.lists.view' },
      { path: '/admin/badges', label: 'Contributor Badges', icon: 'fa-medal', description: 'Contributor tiers and award thresholds', permission: 'admin.badges.manage' },
    ],
  },
  {
    id: 'people',
    label: 'People',
    items: [
      { path: '/admin/users', label: 'Manage Users', icon: 'fa-user-group', description: 'Accounts, statuses, roles, and country scopes', permission: 'admin.users.view' },
      { path: '/admin/roles', label: 'Roles & Permissions', icon: 'fa-user-shield', description: 'Grant or revoke any permission on any role', permission: 'admin.roles.view' },
      { path: '/admin/mailing', label: 'Mailing List', icon: 'fa-envelope-open-text', description: 'Newsletter subscribers and opted-in users', permission: 'admin.mailing.view' },
    ],
  },
  {
    id: 'system',
    label: 'System',
    items: [
      { path: '/admin/configure', label: 'Configurations', icon: 'fa-sliders', description: 'System configurations and portal settings', permission: 'admin.settings.view' },
      { path: '/admin/languages', label: 'Site Languages', icon: 'fa-language', description: 'AU official languages available on the site', permission: 'admin.languages.manage' },
      { path: '/admin/translations', label: 'Language Management', icon: 'fa-earth-africa', description: 'Edit every UI string per language, with AI fast-translate', permission: 'admin.languages.manage' },
      { path: '/admin/themes', label: 'Themes', icon: 'fa-palette', description: 'Upload, activate, and manage portal themes', permission: 'admin.themes.manage' },
      { path: '/admin/ai', label: 'AI Integration', icon: 'fa-robot', description: 'Providers, models, and per-feature fallbacks', permission: 'admin.ai.manage' },
      { path: '/admin/modules', label: 'Modules', icon: 'fa-cubes', description: 'Turn portal modules on or off', permission: 'admin.modules.manage' },
    ],
  },
]);

/** Groups filtered down to what `permissions` may see; empty groups removed. */
export const adminNavFor = (permissions = []) => ADMIN_NAV
  .map((group) => ({ ...group, items: group.items.filter((item) => permissions.includes(item.permission)) }))
  .filter((group) => group.items.length > 0);

/** Flat list of visible items, for menus and the command palette. */
export const adminNavItemsFor = (permissions = []) => adminNavFor(permissions).flatMap((group) =>
  group.items.map((item) => ({ ...item, group: group.label })));
