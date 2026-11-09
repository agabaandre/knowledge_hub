import { useState } from 'react';

/** User photo with an initials fallback (also used when the image 404s). */
export const Avatar = ({ photo, name, size = 36 }) => {
  const [failed, setFailed] = useState(false);
  const initials = String(name ?? '')
    .split(/\s+/)
    .filter(Boolean)
    .slice(0, 2)
    .map((part) => part[0].toUpperCase())
    .join('');

  if (photo && !failed) {
    return (
      <img
        src={photo}
        alt=""
        width={size}
        height={size}
        style={{ width: size, height: size, borderRadius: '50%', objectFit: 'cover', border: '1px solid #e2e8f0', background: '#f8fafc' }}
        onError={() => setFailed(true)}
      />
    );
  }
  return (
    <span
      aria-hidden="true"
      className="d-inline-flex align-items-center justify-content-center"
      style={{
        width: size, height: size, borderRadius: '50%',
        background: 'rgba(0, 98, 57, 0.12)', color: 'var(--theme-color-primary, #006239)',
        fontWeight: 600, fontSize: size * 0.38,
      }}
    >
      {initials || <i className="fa fa-user" />}
    </span>
  );
};
