import { Navigate } from 'react-router-dom';
import { useSite } from '../../core/site-context.jsx';
import { withSitePrefix } from '@khub/shared/urls';

/**
 * Client-side guard only: the backend re-checks every admin API call.
 * `level="moderator"` admits moderators and above (the approvals inbox);
 * the default admits overall administrators only.
 */
export const AdminGate = ({ children, permission, level }) => {
  const { manifest } = useSite();
  if (!manifest) return null;
  if (!manifest.user) return <Navigate to={withSitePrefix(manifest.site.pathPrefix, '/login')} replace />;
  const allowed = permission
    ? manifest.user.permissions?.includes(permission)
    : level === 'moderator' ? manifest.user.access?.canModerate : manifest.user.isAdmin;
  if (!allowed) return <Navigate to={withSitePrefix(manifest.site.pathPrefix, '/')} replace />;
  return children;
};
