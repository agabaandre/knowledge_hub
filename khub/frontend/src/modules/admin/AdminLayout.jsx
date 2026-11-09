import { useEffect, useMemo, useRef, useState } from 'react';
import { Link, NavLink, Outlet, Navigate, useNavigate, useLocation } from 'react-router-dom';
import { adminNavFor } from '@khub/shared/admin-nav';
import { withSitePrefix } from '@khub/shared/urls';
import { useSite } from '../../core/site-context.jsx';
import { useApi } from '../../core/hooks.js';
import { useT } from '../../core/i18n.jsx';
import { LanguageSwitcher } from '../../core/LanguageSwitcher.jsx';
import { Avatar } from '../../core/Avatar.jsx';
import { apiFetch } from '../../core/api.js';
import { mediaUrl } from '../../core/format.js';
import '../../styles/admin.css';

/**
 * Dedicated admin panel chrome: dark sidebar + topbar, no public-site
 * navigation. The menu comes from the shared ADMIN_NAV definition (the same
 * one the backend serves at /api/admin/nav), filtered by the signed-in user's
 * permissions, so backend and frontend can never disagree about who sees what.
 */
export const AdminLayout = () => {
  const { manifest } = useSite();
  const t = useT();
  const navigate = useNavigate();
  const location = useLocation();
  const [sidebarOpen, setSidebarOpen] = useState(false);
  const [userMenu, setUserMenu] = useState(false);
  const [palette, setPalette] = useState(false);
  const userRef = useRef(null);

  const user = manifest?.user;
  const prefix = manifest?.site?.pathPrefix ?? '';
  const to = (path) => withSitePrefix(prefix, path);
  // Server-driven navigation: the backend filters the shared ADMIN_NAV by the
  // session's permissions. The same shared definition is the local fallback.
  const serverNav = useApi('/api/admin/nav', undefined, { enabled: Boolean(user) });
  const groups = useMemo(
    () => serverNav.data?.groups ?? adminNavFor(user?.permissions ?? []),
    [serverNav.data, user],
  );
  const logo = mediaUrl(manifest?.settings?.logo);

  // Close transient surfaces on navigation.
  useEffect(() => { setSidebarOpen(false); setUserMenu(false); setPalette(false); }, [location.pathname]);

  // Click-away for the user menu.
  useEffect(() => {
    const onClick = (event) => { if (userRef.current && !userRef.current.contains(event.target)) setUserMenu(false); };
    document.addEventListener('click', onClick);
    return () => document.removeEventListener('click', onClick);
  }, []);

  // ⌘K / Ctrl+K opens the command palette.
  useEffect(() => {
    const onKey = (event) => {
      if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') { event.preventDefault(); setPalette((value) => !value); }
      if (event.key === 'Escape') setPalette(false);
    };
    document.addEventListener('keydown', onKey);
    return () => document.removeEventListener('keydown', onKey);
  }, []);

  if (!manifest) return null;
  if (!user) return <Navigate to={to('/login')} replace />;
  if ((user.permissions?.length ?? 0) === 0) return <Navigate to={to('/')} replace />;

  const logout = async () => {
    await apiFetch('/api/auth/logout', { method: 'POST' });
    window.location.assign(to('/'));
  };

  return (
    <div className="kh-admin">
      <aside className={`kh-admin-sidebar ${sidebarOpen ? 'is-open' : ''}`} aria-label="Admin navigation">
        <Link to={to('/admin')} className="kh-admin-brand">
          {logo ? (
            <img src={logo} alt="" className="kh-admin-brand-img"
              onError={(event) => { event.currentTarget.style.display = 'none'; }} />
          ) : (
            <span className="kh-admin-brand-logo" aria-hidden="true"><i className="fa fa-staff-snake" /></span>
          )}
          <span>
            <span className="kh-admin-brand-name">{manifest.site?.name ?? 'Knowledge Hub'}</span>
            <span className="kh-admin-brand-sub">{t('ui_body.administration', 'Administration')}</span>
          </span>
        </Link>
        <nav className="kh-admin-nav">
          {groups.map((group) => (
            <div className="kh-admin-nav-group" key={group.id}>
              <h2>{group.label}</h2>
              <ul>
                {group.items.map((item) => (
                  <li key={item.path}>
                    <NavLink to={to(item.path)} end={Boolean(item.exact)}
                      className={({ isActive }) => `kh-admin-navlink ${isActive ? 'is-active' : ''}`}>
                      <i className={`fa ${item.icon}`} aria-hidden="true" /> {item.label}
                    </NavLink>
                  </li>
                ))}
              </ul>
            </div>
          ))}
        </nav>
        <div className="kh-admin-sidebar-foot">
          <a href={to('/')}><i className="fa fa-arrow-up-right-from-square" aria-hidden="true" /> {t('ui_body.view_site', 'View public site')}</a>
        </div>
      </aside>
      {sidebarOpen && <div className="kh-admin-backdrop" onClick={() => setSidebarOpen(false)} aria-hidden="true" />}

      <div className="kh-admin-main">
        <header className="kh-admin-topbar">
          <button type="button" className="kh-admin-burger" aria-label="Toggle navigation" onClick={() => setSidebarOpen((value) => !value)}>
            <i className="fa fa-bars" aria-hidden="true" />
          </button>
          <button type="button" className="kh-admin-search" onClick={() => setPalette(true)}>
            <i className="fa fa-magnifying-glass" aria-hidden="true" />
            <span>{t('ui_body.search_admin', 'Jump to…')}</span>
            <kbd>⌘K</kbd>
          </button>
          <span className="kh-admin-topbar-spacer" />
          <a className="kh-admin-viewsite" href={to('/')}>
            <i className="fa fa-globe" aria-hidden="true" /> {t('ui_body.view_site_short', 'View site')}
          </a>
          <LanguageSwitcher />
          <div className="kh-admin-user" ref={userRef}>
            <button type="button" className="kh-admin-user-btn" aria-haspopup="menu" aria-expanded={userMenu} onClick={() => setUserMenu((value) => !value)}>
              <Avatar photo={user.photo} name={user.name} size={32} />
              <span className="text-start">
                <span className="kh-admin-user-name">{user.name}</span>
                <span className="kh-admin-user-role">{user.access?.label ?? user.roles?.[0] ?? ''}</span>
              </span>
              <i className="fa fa-chevron-down" style={{ fontSize: 10, color: '#94a3b8' }} aria-hidden="true" />
            </button>
            {userMenu && (
              <div className="kh-admin-user-menu" role="menu">
                <Link to={to('/account')} role="menuitem"><i className="fa fa-user" aria-hidden="true" /> {t('ui_body.my_profile', 'My profile')}</Link>
                <Link to={to('/account/publish')} role="menuitem"><i className="fa fa-pen-to-square" aria-hidden="true" /> {t('frontend_nav.resource_publication', 'Publish a resource')}</Link>
                <hr />
                <button type="button" role="menuitem" className="is-danger" onClick={logout}>
                  <i className="fa fa-right-from-bracket" aria-hidden="true" /> {t('ui_body.logout', 'Sign out')}
                </button>
              </div>
            )}
          </div>
        </header>
        <main className="kh-admin-content">
          <Outlet />
        </main>
      </div>

      {palette && <CommandPalette groups={groups} onClose={() => setPalette(false)} onGo={(path) => { setPalette(false); navigate(to(path)); }} />}
    </div>
  );
};

/** ⌘K palette: fuzzy jump to any admin page the user may see. */
const CommandPalette = ({ groups, onClose, onGo }) => {
  const [term, setTerm] = useState('');
  const [highlight, setHighlight] = useState(0);
  const inputRef = useRef(null);
  const items = useMemo(
    () => (groups ?? []).flatMap((group) => group.items.map((item) => ({ ...item, group: group.label }))),
    [groups],
  );
  const filtered = useMemo(() => {
    const clean = term.trim().toLowerCase();
    if (!clean) return items;
    return items.filter((item) => `${item.label} ${item.group} ${item.description ?? ''}`.toLowerCase().includes(clean));
  }, [items, term]);

  useEffect(() => { inputRef.current?.focus(); }, []);
  useEffect(() => { setHighlight(0); }, [term]);

  const onKeyDown = (event) => {
    if (event.key === 'ArrowDown') { event.preventDefault(); setHighlight((value) => Math.min(value + 1, filtered.length - 1)); }
    if (event.key === 'ArrowUp') { event.preventDefault(); setHighlight((value) => Math.max(value - 1, 0)); }
    if (event.key === 'Enter' && filtered[highlight]) { event.preventDefault(); onGo(filtered[highlight].path); }
  };

  return (
    <div className="kh-admin-palette-backdrop" onClick={onClose} role="presentation">
      <div className="kh-admin-palette" role="dialog" aria-modal="true" aria-label="Jump to admin page" onClick={(event) => event.stopPropagation()}>
        <div className="kh-admin-palette-input">
          <i className="fa fa-magnifying-glass" aria-hidden="true" />
          <input ref={inputRef} value={term} placeholder="Search admin pages…" onChange={(event) => setTerm(event.target.value)} onKeyDown={onKeyDown} />
          <kbd style={{ fontSize: '0.68rem', color: '#9aa7b4' }}>esc</kbd>
        </div>
        <ul className="kh-admin-palette-list">
          {filtered.map((item, index) => (
            <li key={item.path}>
              <button type="button" className={index === highlight ? 'is-highlighted' : ''}
                onMouseEnter={() => setHighlight(index)} onClick={() => onGo(item.path)}>
                <i className={`fa ${item.icon}`} aria-hidden="true" />
                <span className="kh-admin-palette-text">
                  <span className="kh-admin-palette-label">{item.label}</span>
                  {item.description && <span className="kh-admin-palette-desc">{item.description}</span>}
                </span>
                <span className="kh-admin-palette-group">{item.group}</span>
              </button>
            </li>
          ))}
        </ul>
        {filtered.length === 0 && <p className="kh-admin-palette-empty">No admin page matches “{term}”.</p>}
      </div>
    </div>
  );
};
