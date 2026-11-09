import { Seo } from '../../core/Seo.jsx';

/**
 * Page header inside the admin panel (the chrome itself — sidebar, topbar,
 * command palette — lives in AdminLayout). Same props as before so every
 * admin page keeps working: title, optional subtitle, optional actions.
 */
export const AdminShell = ({ title, subtitle, children, actions }) => (
  <>
    <Seo title={`Admin · ${title}`} noindex />
    <div className="kh-admin-pagehead">
      <div>
        <div className="kh-admin-crumb">Admin</div>
        <h1>{title}</h1>
        {subtitle && <p className="kh-admin-sub">{subtitle}</p>}
      </div>
      {actions && <div className="kh-admin-pagehead-actions">{actions}</div>}
    </div>
    {children}
  </>
);

export const AdminCard = ({ title, children, className = '', actions }) => (
  <div className={`kh-admin-card ${className}`}>
    {(title || actions) && (
      <div className="d-flex align-items-center justify-content-between flex-wrap mb-3" style={{ gap: 8 }}>
        {title && <h2 className="ft-bold mb-0" style={{ fontSize: '1.02rem' }}>{title}</h2>}
        {actions}
      </div>
    )}
    {children}
  </div>
);
