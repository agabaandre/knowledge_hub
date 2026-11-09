import { Navigate } from 'react-router-dom';
import { useSite, useModule } from './site-context.jsx';
import { withSitePrefix } from '@khub/shared/urls';

/** Renders children only when the module is on for this site; otherwise a 404 page. */
export const ModuleGate = ({ module, children }) => {
  const { manifest } = useSite();
  const enabled = useModule(module);
  if (!manifest) return null;
  if (!enabled) return <Navigate to={withSitePrefix(manifest.site.pathPrefix, '/404')} replace />;
  return children;
};
