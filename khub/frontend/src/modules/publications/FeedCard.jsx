import { Link } from 'react-router-dom';
import { useSite } from '../../core/site-context.jsx';
import { withSitePrefix } from '@khub/shared/urls';
import { formatDate } from '../../core/format.js';
import { CoverImage } from '../../core/ui.jsx';


/**
 * Search-results card. Mirrors partials/publications/publication_feed_card.blade.php
 * (classes come from the extracted publication-card.css).
 */
export const FeedCard = ({ item }) => {
  const { manifest } = useSite();
  const href = withSitePrefix(manifest.site.pathPrefix, item.path);

  return (
    <div className="card col-lg-12 community-pub-card pub-card-file-type-corner-wrap mb-2">
      <div className="card-body text-left p-0">
        {item.fileType?.name && (
          <span className="pub-card-file-type-corner" title={item.fileType.name}>
            <i className={`fa ${item.fileType.icon?.startsWith('fa-') ? item.fileType.icon : 'fa-file'}`} aria-hidden="true" />
          </span>
        )}
        <div className="community-pub-intro">
          <div className="community-pub-intro__media">
            <Link to={href} className="community-pub-intro__image-link" aria-label={item.title}>
              <CoverImage
                src={item.cover}
                alt={item.title}
                className="community-pub-intro__image"
                loading="lazy"
              />
            </Link>
          </div>
          <div className="community-pub-intro__text">
            <h5 className="community-pub-title"><Link to={href}>{item.title}</Link></h5>
            {item.excerpt && (
              <p className="community-pub-description"><Link to={href}>{item.excerpt}</Link></p>
            )}
          </div>
        </div>
        <div className="community-pub-body">
          <div className="community-pub-meta-list">
            {item.author?.name && (
              <div className="community-pub-meta-row">
                <i className="fa fa-bank" aria-hidden="true" />
                <span><strong>Source:</strong> {item.author.name}</span>
              </div>
            )}
            {item.theme?.name && (
              <div className="community-pub-meta-row">
                <i className="fa fa-briefcase" aria-hidden="true" />
                <span><strong>Theme:</strong> {item.theme.name}</span>
              </div>
            )}
            {item.subTheme?.name && (
              <div className="community-pub-meta-row">
                <i className="fa fa-archive" aria-hidden="true" />
                <span><strong>Sub Theme:</strong> {item.subTheme.name}</span>
              </div>
            )}
            {item.dataCategory && (
              <div className="community-pub-meta-row">
                <i className="fa fa-file" aria-hidden="true" />
                <span><strong>Category:</strong> {item.dataCategory}</span>
              </div>
            )}
            {item.associatedAuthors && (
              <div className="community-pub-meta-row">
                <i className="fa fa-users" aria-hidden="true" />
                <span><strong>Associated Authors:</strong> {item.associatedAuthors}</span>
              </div>
            )}
          </div>
          <div className="community-pub-stats">
            {item.yearPublished && (
              <span className="community-pub-stat"><i className="fa fa-calendar" aria-hidden="true" /> {item.yearPublished}</span>
            )}
            {item.createdAt && (
              <span className="community-pub-stat"><i className="fa fa-clock" aria-hidden="true" /> {formatDate(item.createdAt)}</span>
            )}
            <span className="community-pub-stat community-pub-stat--views">
              <i className="fa fa-eye" aria-hidden="true" /> {item.visits.toLocaleString()} {item.visits === 1 ? 'view' : 'views'}
            </span>
            <span className="community-pub-stat community-pub-stat--comments">
              <i className="fa fa-comments" aria-hidden="true" /> {item.commentCount} {item.commentCount === 1 ? 'Comment' : 'Comments'}
            </span>
            {item.likeCount > 0 && (
              <span className="community-pub-stat"><i className="fa fa-heart" aria-hidden="true" /> {item.likeCount} {item.likeCount === 1 ? 'Like' : 'Likes'}</span>
            )}
          </div>
        </div>
      </div>
    </div>
  );
};
