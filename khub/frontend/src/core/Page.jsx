import { Link } from 'react-router-dom';
import { useSite } from './site-context.jsx';
import { withSitePrefix } from '@khub/shared/urls';
import { useT } from './i18n.jsx';

/**
 * Standard inner-page shell: grey band, breadcrumb, centred section title —
 * the pattern the Laravel inner pages share.
 */
export const Page = ({ title, crumb, children, lead }) => {
  const { manifest } = useSite();
  const t = useT();
  return (
    <div className="gray py-4">
      <div className="container">
        <nav className="mb-3" aria-label="Breadcrumb">
          <ol className="breadcrumb mb-0" style={{ background: 'transparent', padding: 0, fontSize: '0.875rem' }}>
            <li className="breadcrumb-item"><Link to={withSitePrefix(manifest.site.pathPrefix, '/')}>{t('frontend_nav.home', 'Home')}</Link></li>
            <li className="breadcrumb-item active" aria-current="page">{crumb ?? title}</li>
          </ol>
        </nav>
        <div className="sec_title position-relative text-center mb-4">
          <h1 className="ft-bold" style={{ fontSize: '1.6rem' }}>{title}</h1>
          {lead && <p className="mb-0 text-muted">{lead}</p>}
        </div>
        {children}
      </div>
    </div>
  );
};

export const Loading = () => <div className="spinner-ring" role="status" aria-label="Loading" />;
