import { Link } from 'react-router-dom';
import { useSite } from '../../core/site-context.jsx';
import { withSitePrefix } from '@khub/shared/urls';
import { formatDate } from '../../core/format.js';
import { CoverImage } from '../../core/ui.jsx';


/**
 * Two-per-row card used by "Top Searches" and "Featured" on the home page.
 * Same markup/classes as home/partials/top_searches_list_items.blade.php.
 */
export const TopSearchCard = ({ item }) => {
  const { manifest } = useSite();
  const href = withSitePrefix(manifest.site.pathPrefix, item.path);

  return (
    <div className="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-xs-12">
      <div className="jbr-wrap text-left border rounded pub-card-file-type-corner-wrap">
        {item.fileType?.name && (
          <span className="pub-card-file-type-corner" title={item.fileType.name}>
            <i className={`fa ${item.fileType.icon?.startsWith('fa-') ? item.fileType.icon : 'fa-file'}`} aria-hidden="true" />
          </span>
        )}
        <div className="cats-box mlb-res rounded bg-white d-flex align-items-center px-2 py-2 top-searches-card">
          <div className="cats-box-image top-searches-image" style={{ width: 150, height: 150, flexShrink: 0, marginRight: '0.5rem', overflow: 'hidden', display: 'flex', alignItems: 'center' }}>
            <CoverImage
              src={item.cover}
              alt={`${item.title} - ${item.author?.name ?? 'Africa CDC'}`}
              title={item.title}
              loading="lazy"
              style={{ width: '100%', height: '100%', objectFit: 'contain', background: 'transparent' }}
            />
          </div>
          <div className="cats-box-caption top-searches-content" style={{ flex: 1, overflow: 'hidden' }}>
            <h4 className="fs-md mb-0 ft-medium">
              <Link to={href} title={item.title}>{item.title}</Link>
            </h4>
            <div className="d-block mb-2 position-relative">
              {item.author?.name && (
                <span className="text-muted medium d-block">Source: <i className="fa fa-bank mr-1" aria-hidden="true" />{item.author.name}</span>
              )}
              {item.theme?.name && <span className="muted medium theme-cl d-block"><i className="fa fa-briefcase mr-1" aria-hidden="true" />Theme: {item.theme.name}</span>}
              {item.subTheme?.name && <span className="muted medium theme-cl d-block"><i className="fa fa-archive mr-1" aria-hidden="true" />Sub Theme: {item.subTheme.name}</span>}
              {item.dataCategory && <span className="muted medium text-muted d-block"><i className="fa fa-file mr-1" aria-hidden="true" />Category: {item.dataCategory}</span>}
              {item.likeCount > 0 && (
                <span className="d-block"><i className="fa fa-heart theme-text mr-1" aria-hidden="true" />{item.likeCount} Like{item.likeCount === 1 ? '' : 's'}</span>
              )}
              <span className="text-muted medium d-block mt-1">
                {item.yearPublished && <span className="mr-2"><i className="fa fa-calendar mr-1" aria-hidden="true" />{item.yearPublished}</span>}
                {item.createdAt && <span className="mr-2"><i className="fa fa-clock mr-1" aria-hidden="true" />{formatDate(item.createdAt)}</span>}
                <span className="mr-2"><i className="fa fa-eye mr-1" aria-hidden="true" />{item.visits.toLocaleString()} Views</span>
                <span><i className="fa fa-comments mr-1" aria-hidden="true" />{item.commentCount} Comments</span>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};
