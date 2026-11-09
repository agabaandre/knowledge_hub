import { Link } from 'react-router-dom';
import { Seo } from '../../core/Seo.jsx';
import { useSite } from '../../core/site-context.jsx';
import { withSitePrefix } from '@khub/shared/urls';

export const NotFoundPage = () => {
  const { manifest } = useSite();
  return (
    <div className="gray py-5">
      <Seo title="Page not found" noindex />
      <div className="container text-center py-5">
        <h1 className="ft-bold" style={{ fontSize: '2rem' }}>Page not found</h1>
        <p className="text-muted">The page you are looking for does not exist or is no longer available.</p>
        <Link className="btn theme-bg text-white" to={withSitePrefix(manifest?.site?.pathPrefix, '/')}>Back to Home</Link>
      </div>
    </div>
  );
};
