import { useEffect, useState } from 'react';
import { Link, NavLink, Outlet, useLocation, useNavigate } from 'react-router-dom';
import { useSite, useModule } from './site-context.jsx';
import { apiFetch } from './api.js';
import { withSitePrefix } from '@khub/shared/urls';
import { adminNavItemsFor } from '@khub/shared/admin-nav';
import { mediaUrl } from './format.js';
import { useT } from './i18n.jsx';
import { LanguageSwitcher } from './LanguageSwitcher.jsx';
import { Avatar } from './Avatar.jsx';

const PORTRAIT_QUERY = '(max-width: 991px)';

/** Dropdown menu item using the theme's .nav-dropdown markup (jQuery replaced by React state). */
const MenuDropdown = ({ id, icon, iconNode, label, open, setOpen, children }) => (
  <li className={`categories ${open === id ? 'kh-open' : ''}`}>
    <a
      href="#"
      role="button"
      aria-expanded={open === id}
      onClick={(event) => {
        event.preventDefault();
        setOpen(open === id ? null : id);
      }}
    >
      {iconNode ?? <i className={`fa ${icon} mr-1`} aria-hidden="true" />} {label}
      <span className="submenu-indicator"><span className="submenu-indicator-chevron" /></span>
    </a>
    <ul className="nav-dropdown nav-submenu">{children}</ul>
  </li>
);

export const Layout = () => {
  const { manifest, refresh } = useSite();
  const navigate = useNavigate();
  const location = useLocation();
  const [portrait, setPortrait] = useState(() => window.matchMedia(PORTRAIT_QUERY).matches);
  const [menuOpen, setMenuOpen] = useState(false);
  const [openDropdown, setOpenDropdown] = useState(null);
  const t = useT();

  const site = manifest?.site;
  const settings = manifest?.settings ?? {};
  const user = manifest?.user;
  const to = (path) => withSitePrefix(site?.pathPrefix, path);

  const forums = useModule('forums');
  const communities = useModule('communities');
  const healthTopics = useModule('health-topics');
  const events = useModule('events');
  const courses = useModule('courses');
  const faqs = useModule('faqs');
  const countries = useModule('countries');
  const authors = useModule('authors');

  useEffect(() => {
    const media = window.matchMedia(PORTRAIT_QUERY);
    const onChange = () => setPortrait(media.matches);
    media.addEventListener('change', onChange);
    return () => media.removeEventListener('change', onChange);
  }, []);

  // Close menus on navigation, and return to the top like a classic page load.
  useEffect(() => {
    setMenuOpen(false);
    setOpenDropdown(null);
    window.scrollTo(0, 0);
  }, [location.pathname, location.search]);

  const logout = async () => {
    await apiFetch('/api/auth/logout', { method: 'POST' });
    await refresh();
    navigate(to('/'));
  };

  return (
    <div id="main-wrapper">
      {/* Brand bar: logo · centred title + slogan · languages (same as the Laravel header) */}
      <div id="langauge-container" style={{ marginBottom: -4 }}>
        <div className="p-3 bg-light">
          <div className="container" style={{ minWidth: '90%' }}>
            <div className="row align-items-center">
              <div className="col-lg-3 col-md-3 d-none d-md-block">
                <Link className="nav-brand" to={to('/')}>
                  {settings.logo && (
                    <img src={mediaUrl(settings.logo)} className="logo" alt=""
                      style={{ maxHeight: 70, width: 'auto', marginBottom: -16 }}
                      onError={(event) => { event.currentTarget.style.display = 'none'; }} />
                  )}
                </Link>
              </div>
              <div className="col-lg-5 col-md-5 col-12 text-center">
                <div id="khub-site-branding">
                  <h3 style={{ color: 'black', fontWeight: 'bold', marginBottom: 7 }}>{settings.site_name}</h3>
                  {settings.slogan && (
                    <h6 className="slogan fw-bold" style={{ fontSize: 14, marginBottom: 7 }}>{settings.slogan}</h6>
                  )}
                </div>
              </div>
              <div className="col-lg-4 col-md-4 d-none d-md-flex align-items-center justify-content-end">
                <LanguageSwitcher />
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Green navigation bar */}
      <div className="header modern-header justify-content-center" style={{ display: 'flex', justifyContent: 'center' }}>
        <div className="menu-container">
          <nav id="navigation" className={`navigation ${portrait ? 'navigation-portrait' : 'navigation-landscape'}`}>
            <div className="nav-header">
              <Link className="nav-brand" to={to('/')} aria-label="Home" />
              <button
                type="button"
                className="nav-toggle"
                aria-label={menuOpen ? 'Close menu' : 'Open menu'}
                aria-expanded={menuOpen}
                onClick={() => setMenuOpen((value) => !value)}
                style={{ background: 'transparent', border: 0 }}
              />
              <div className="mobile_nav">
                <ul>
                  <li>
                    <Link to={to(user ? '/account' : '/login')} className="theme-cl fs-lg" aria-label="Account">
                      <i className="fa fa-user" aria-hidden="true" />
                    </Link>
                  </li>
                </ul>
              </div>
            </div>
            <div className={`nav-menus-wrapper ${portrait && menuOpen ? 'nav-menus-wrapper-open' : ''}`} id="khub-nav-menus">
              {portrait && (
                <button type="button" className="nav-menus-wrapper-close-button" onClick={() => setMenuOpen(false)}>✕</button>
              )}
              <ul className="nav-menu">
                <li className={location.pathname === to('/') || location.pathname === '/' ? 'active' : ''}>
                  <NavLink to={to('/')} end><i className="fa fa-home mr-1" aria-hidden="true" /> {t('frontend_nav.home', 'Home')}</NavLink>
                </li>
                <MenuDropdown id="browse" icon="fa-compass" label={t('frontend_nav.browse', 'Browse')} open={openDropdown} setOpen={setOpenDropdown}>
                  <li><Link to={to('/records/search')}>{t('frontend_nav.records', 'Browse Resources')}</Link></li>
                  {healthTopics && <li><Link to={to('/health-topics')}>{t('frontend_nav.health_topics', 'Health Topics')}</Link></li>}
                  {countries && <li><Link to={to('/countries')}>{site?.features?.statesEnabled ? t('frontend_nav.member_states', 'Member States') : t('frontend_nav.administrative_units', 'Administrative Units')}</Link></li>}
                  {authors && <li><Link to={to('/authors')}>Contributing Sources</Link></li>}
                </MenuDropdown>
                {(forums || communities) && (
                  <MenuDropdown id="discussions" icon="fa-comments" label={t('frontend_nav.discussions', 'Discussions')} open={openDropdown} setOpen={setOpenDropdown}>
                    {forums && <li><Link to={to('/forums')}>{t('frontend_nav.forums', 'Forums')}</Link></li>}
                    {communities && <li><Link to={to('/communities')}>{t('frontend_nav.communities', 'Communities')}</Link></li>}
                  </MenuDropdown>
                )}
                {(courses || events) && (
                  <MenuDropdown id="learning" icon="fa-graduation-cap" label={t('frontend_nav.learning', 'Learning')} open={openDropdown} setOpen={setOpenDropdown}>
                    {courses && <li><Link to={to('/courses')}>{t('frontend_nav.courses', 'Courses')}</Link></li>}
                    {events && <li><Link to={to('/events')}>{t('frontend_nav.events', 'Events')}</Link></li>}
                  </MenuDropdown>
                )}
                <MenuDropdown id="support" icon="fa-life-ring" label={t('frontend_nav.support', 'Support')} open={openDropdown} setOpen={setOpenDropdown}>
                  {faqs && <li><Link to={to('/faqs')}>{t('frontend_nav.faqs', 'FAQs')}</Link></li>}
                  <li><a href={`${site?.publicUrl ?? ''}/user_manual`}>{t('ui_body.footer_user_guide', 'User Guide')}</a></li>
                </MenuDropdown>
                {user && (
                  <MenuDropdown id="create" icon="fa-plus-circle" label={t('frontend_nav.create', 'Create')} open={openDropdown} setOpen={setOpenDropdown}>
                    <li><Link to={to('/account/publish')}>{t('frontend_nav.resource_publication', 'Resource Publication')}</Link></li>
                  </MenuDropdown>
                )}
                {(user?.permissions?.length ?? 0) > 0 && (
                  <MenuDropdown id="admin" icon="fa-gauge" label="Admin" open={openDropdown} setOpen={setOpenDropdown}>
                    {/* Shared ADMIN_NAV definition — same source the admin panel sidebar and the backend use. */}
                    {adminNavItemsFor(user.permissions).map((item) => (
                      <li key={item.path}><Link to={to(item.path)}>{item.label}</Link></li>
                    ))}
                  </MenuDropdown>
                )}
                {user && !user.isAdmin && user.access?.canModerate && (
                  <li>
                    <Link to={to('/admin/approvals')}>
                      <i className="fa fa-inbox mr-1" aria-hidden="true" /> Moderation
                    </Link>
                  </li>
                )}
              </ul>
              <ul className="nav-menu nav-menu-social align-to-right">
                {user ? (
                  <MenuDropdown id="account" icon="fa-user" iconNode={<Avatar photo={user.photo} name={user.name} size={26} />}
                    label={user.name?.split(' ')[0] ?? t('ui_body.account', 'Account')} open={openDropdown} setOpen={setOpenDropdown}>
                    <li className="kh-account-header" aria-hidden="true">
                      <span className="d-flex align-items-center" style={{ gap: 10 }}>
                        <Avatar photo={user.photo} name={user.name} size={40} />
                        <span>
                          <span className="ft-medium d-block">{user.name}</span>
                          <span className="text-muted d-block">{user.email}</span>
                          <span className="theme-cl" style={{ fontSize: '0.75rem' }}>
                            {user.access?.label}{user.access?.canModerate && user.access?.scopeLabel ? ` · ${user.access.scopeLabel}` : ''}
                          </span>
                        </span>
                      </span>
                    </li>
                    {user.isAdmin && <li><Link to={to('/admin')}><i className="fa fa-table-cells mr-1" aria-hidden="true" /> {t('ui_body.admin_panel', 'Admin Panel')}</Link></li>}
                    {!user.isAdmin && user.access?.canModerate && <li><Link to={to('/admin/approvals')}><i className="fa fa-inbox mr-1" aria-hidden="true" /> Moderation</Link></li>}
                    <li><Link to={to('/account')}><i className="fa fa-user mr-1" aria-hidden="true" /> {t('ui_body.my_profile', 'My Profile')}</Link></li>
                    <li><Link to={to('/account/publications')}><i className="fa fa-list mr-1" aria-hidden="true" /> {t('ui_body.publications', 'Publications')}</Link></li>
                    <li><Link to={to('/account/forums')}><i className="fa fa-comments mr-1" aria-hidden="true" /> {t('ui_body.my_forums', 'My Forums')}</Link></li>
                    <li><Link to={to('/communities')}><i className="fa fa-users mr-1" aria-hidden="true" /> {t('ui_body.my_communities', 'My Communities')}</Link></li>
                    <li><Link to={to('/account/favourites')}><i className="fa fa-star mr-1" aria-hidden="true" /> {t('ui_body.my_favourites', 'My Favourites')}</Link></li>
                    <li><Link to={to('/account/publish')}><i className="fa fa-plus mr-1" aria-hidden="true" /> {t('ui_body.publish_a_resource', 'Publish a resource')}</Link></li>
                    <li>
                      <button type="button" className="kh-dropdown-action" onClick={logout}>
                        <i className="fa fa-arrow-right-from-bracket mr-1" aria-hidden="true" /> {t('ui_body.log_out', 'Log out')}
                      </button>
                    </li>
                  </MenuDropdown>
                ) : (
                  <li>
                    <Link to={to('/login')} className="ft-medium text-bold">
                      <span className="kh-nav-item-stack">
                        <i className="fas fa-user kh-nav-icon" aria-hidden="true" />
                        <span className="kh-nav-label">{t('ui_body.account', 'Account')}</span>
                      </span>
                    </Link>
                  </li>
                )}
              </ul>
            </div>
          </nav>
        </div>
      </div>
      <div className="clearfix" />

      <main id="khub-page-content" className="khub-page-content kh-page">
        <Outlet />
      </main>

      {/* Footer: same dark 4-column layout as the Laravel portal */}
      <footer className="dark-footer skin-dark-footer justify-content-center">
        <div className="footer-middle py-0">
          <div className="container">
            <div className="row">
              <div className="col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-4">
                <div className="footer_widget">
                  <h4 className="widget_title">{t('ui_body.footer_address', 'Address')}</h4>
                  {settings.logo && (
                    <img src={settings.logo} className="logo img-footer mb-1" alt="" style={{ maxHeight: 70, width: 'auto' }}
                      onError={(event) => { event.currentTarget.style.display = 'none'; }} />
                  )}
                  <div className="address mt-0">
                    {settings.address && <a className="d-block" href={`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(settings.address)}`} target="_blank" rel="noopener noreferrer">{settings.address}</a>}
                    {settings.phone && <a className="d-block" href={`tel:${settings.phone}`}>{settings.phone}</a>}
                    {settings.email && <a className="d-block" href={`mailto:${settings.email}`}>{settings.email}</a>}
                  </div>
                </div>
              </div>
              <nav className="col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-4" aria-label="Footer navigation">
                <div className="footer_widget">
                  <h4 className="widget_title">{t('ui_body.footer_navigate', 'Navigate')}</h4>
                  <ul className="footer-menu list-unstyled">
                    <li><Link to={to('/')} className="text-decoration-none">{t('ui_body.footer_home', 'Home')}</Link></li>
                    {!user && <li><Link to={to('/login')} className="text-decoration-none">{t('ui_body.footer_login', 'Login')}</Link></li>}
                    {forums && <li><Link to={to('/forums')} className="text-decoration-none">{t('ui_body.footer_discussion_forums', 'Discussion Forums')}</Link></li>}
                    {courses && <li><Link to={to('/courses')} className="text-decoration-none">{t('frontend_nav.courses', 'Courses')}</Link></li>}
                  </ul>
                </div>
              </nav>
              <nav className="col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-4" aria-label="About this portal">
                <div className="footer_widget">
                  <h4 className="widget_title">{t('ui_body.footer_about_portal', 'About This Portal')}</h4>
                  <ul className="footer-menu list-unstyled">
                    {faqs && <li><Link to={to('/faqs')} className="text-decoration-none">{t('ui_body.footer_faq', 'Frequently asked questions')}</Link></li>}
                    <li><a href="https://africacdc.org" target="_blank" rel="noopener noreferrer" className="text-decoration-none">{t('ui_body.footer_africa_cdc_website', 'Africa CDC Website')}</a></li>
                    <li><a href={`${site?.publicUrl ?? ''}/privacy`} className="text-decoration-none">{t('ui_body.footer_privacy_policy', 'Privacy Policy')}</a></li>
                    <li><a href={`${site?.publicUrl ?? ''}/user_manual`} className="text-decoration-none">{t('ui_body.footer_user_guide', 'User Guide')}</a></li>
                  </ul>
                </div>
              </nav>
              <div className="col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-4">
                <div className="footer_widget">
                  <h4 className="widget_title">{t('ui_body.footer_find_us', 'Find us On')}</h4>
                  <ul className="footer-menu list-inline mb-3">
                    {settings.facebook && <li className="list-inline-item"><a href={settings.facebook} target="_blank" rel="noopener noreferrer" title="Facebook"><i className="fa-brands fa-facebook" /></a></li>}
                    {settings.twitter && <li className="list-inline-item"><a href={settings.twitter} target="_blank" rel="noopener noreferrer" title="Twitter"><i className="fa-brands fa-x-twitter" /></a></li>}
                    {settings.linkedin && <li className="list-inline-item"><a href={settings.linkedin} target="_blank" rel="noopener noreferrer" title="LinkedIn"><i className="fa-brands fa-linkedin-in" /></a></li>}
                    <li className="list-inline-item">
                      <a href="https://play.google.com/store/apps/details?id=com.africacdc.khubmobile&hl=en" target="_blank" rel="noopener noreferrer" title="Get the Mobile App">
                        <i className="fa-brands fa-google-play" />
                      </a>
                    </li>
                  </ul>
                  <p className="text-white small mb-3">{t('ui_body.footer_download_app', 'Download our mobile app for a better experience')}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div className="footer-bottom py-2">
          <div className="container">
            <div className="row align-items-center">
              <div className="col-lg-12 col-md-12 text-center">
                <p className="mb-0">{t('ui_body.footer_copyright', '© :year Africa CDC. All rights reserved.', { year: new Date().getFullYear() })}</p>
              </div>
            </div>
          </div>
        </div>
      </footer>

      <a
        id="back2Top"
        className="top-scroll"
        title="Back to top"
        href="#top"
        onClick={(event) => { event.preventDefault(); window.scrollTo({ top: 0, behavior: 'smooth' }); }}
      >
        <i className="fa fa-arrow-up" aria-hidden="true" />
      </a>
    </div>
  );
};
