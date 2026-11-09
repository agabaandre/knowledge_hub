import { useEffect } from 'react';
import { Link } from 'react-router-dom';
import { formatNumber } from './format.js';
import { Avatar } from './Avatar.jsx';

/**
 * Reusable UI primitives for the admin panel (and anywhere else). They pair
 * with src/styles/admin.css; keep presentational logic here so pages stay thin.
 */

/** KPI tile: value, label, icon, optional link. */
export const StatCard = ({ value, label, icon, link, linkLabel = 'View' }) => (
  <div className="kh-admin-stat">
    <span className="kh-admin-stat-icon"><i className={`fa ${icon}`} aria-hidden="true" /></span>
    <span className="kh-admin-stat-value">{formatNumber(value)}</span>
    <span className="kh-admin-stat-label">{label}</span>
    {link && (
      <Link to={link} className="kh-admin-stat-link">
        {linkLabel} <i className="fa fa-arrow-right" aria-hidden="true" style={{ fontSize: '0.72rem' }} />
      </Link>
    )}
  </div>
);

/** Soft, tinted status pill. tone: success | warning | danger | neutral */
export const StatusBadge = ({ tone = 'neutral', children }) => {
  const cls = { success: 'badge-success', warning: 'badge-warning', danger: 'badge-danger', neutral: 'badge-secondary' }[tone] ?? 'badge-secondary';
  return <span className={`badge ${cls}`}>{children}</span>;
};

/** Centered empty state with an icon. */
export const EmptyState = ({ icon = 'fa-inbox', title, hint }) => (
  <div className="kh-admin-empty" role="status">
    <i className={`fa ${icon}`} aria-hidden="true" />
    <p className="mb-0 ft-medium" style={{ color: '#475569' }}>{title}</p>
    {hint && <small>{hint}</small>}
  </div>
);

/** Shimmering placeholder lines while content loads. */
export const Skeleton = ({ lines = 3, height = 14 }) => (
  <div role="status" aria-label="Loading">
    {Array.from({ length: lines }, (_, index) => (
      <div key={index} className="kh-admin-skeleton mb-2" style={{ height, width: `${100 - (index % 3) * 14}%` }} />
    ))}
  </div>
);

/* ---------- Modal & form primitives (edit/add happens in dialogs, not inline) ---------- */

/**
 * Accessible modal dialog: backdrop click + Escape close it, content clicks
 * don't. Pass `footer` for action buttons (right-aligned).
 */
export const Modal = ({ title, icon, onClose, children, footer, wide = false }) => {
  useEffect(() => {
    const onKey = (event) => { if (event.key === 'Escape') onClose(); };
    document.addEventListener('keydown', onKey);
    document.body.style.overflow = 'hidden';
    return () => { document.removeEventListener('keydown', onKey); document.body.style.overflow = ''; };
  }, [onClose]);

  return (
    <div className="kh-admin-modal-backdrop" onClick={onClose} role="presentation">
      <div className={`kh-admin-modal ${wide ? 'is-wide' : ''}`} role="dialog" aria-modal="true" aria-label={title}
        onClick={(event) => event.stopPropagation()}>
        <div className="kh-admin-modal-head">
          <h2>{icon && <i className={`fa ${icon}`} aria-hidden="true" />} {title}</h2>
          <button type="button" className="kh-admin-modal-close" aria-label="Close" onClick={onClose}>
            <i className="fa fa-xmark" aria-hidden="true" />
          </button>
        </div>
        <div className="kh-admin-modal-body">{children}</div>
        {footer && <div className="kh-admin-modal-foot">{footer}</div>}
      </div>
    </div>
  );
};

/** Labelled form row for modal forms. */
export const FormField = ({ label, required, error, hint, children }) => (
  <div className="form-group">
    <label className="ft-medium d-block">{label} {required && <span className="text-danger">*</span>}</label>
    {children}
    {hint && <small className="text-muted d-block">{hint}</small>}
    {error && <small className="text-danger d-block">{error}</small>}
  </div>
);

/* ---------- Shared presentation helpers (one source for repeated patterns) ---------- */

/** Publication moderation status → badge tone, shared by every page that shows one. */
export const STATUS_TONE = Object.freeze({ approved: 'success', pending: 'warning', rejected: 'danger', inactive: 'neutral' });

/** Default artwork used wherever a publication has no cover. */
export const FALLBACK_COVER = '/assets/images/cover.png';

/** Publication cover with the shared fallback + error handling. Extra props go to the <img>. */
export const CoverImage = ({ src, alt = '', ...rest }) => (
  <img
    src={src || FALLBACK_COVER}
    alt={alt}
    {...rest}
    onError={(event) => { event.currentTarget.onerror = null; event.currentTarget.src = FALLBACK_COVER; }}
  />
);

/** The ok/error alert pair every admin page shows. message: {ok} | {error} | null */
export const Flash = ({ message }) => {
  if (!message) return null;
  return (
    <>
      {message.error && <div role="alert" className="alert alert-danger">{message.error}</div>}
      {message.ok && <div role="status" className="alert alert-success">{message.ok}</div>}
    </>
  );
};

/** Avatar + name + secondary line, used in user tables, modals, and detail cards. */
export const UserChip = ({ photo, name, sub, size = 34 }) => (
  <span className="d-flex align-items-center" style={{ gap: 10 }}>
    <Avatar photo={photo} name={name} size={size} />
    <span style={{ minWidth: 0 }}>
      <span className="ft-medium d-block" style={{ color: '#18181b' }}>{name}</span>
      {sub && <small className="text-muted d-block" style={{ overflowWrap: 'anywhere' }}>{sub}</small>}
    </span>
  </span>
);

/** Toggle switch (the portal's kh-switch styling), for boolean row actions. */
export const Switch = ({ checked, disabled, onChange, label }) => (
  <label className="kh-switch mb-0">
    <input type="checkbox" checked={Boolean(checked)} disabled={disabled} aria-label={label}
      onChange={(event) => onChange(event.target.checked)} />
    <span className="kh-slider" />
  </label>
);
