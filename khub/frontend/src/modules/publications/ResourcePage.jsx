import { useMemo, useState } from 'react';
import { Link, useParams } from 'react-router-dom';
import { publicationPath, withSitePrefix } from '@khub/shared/urls';
import { Seo } from '../../core/Seo.jsx';
import { useApi } from '../../core/hooks.js';
import { apiFetch } from '../../core/api.js';
import { formatDate, timeAgo } from '../../core/format.js';
import { useSite } from '../../core/site-context.jsx';
import { useT } from '../../core/i18n.jsx';
import { Loading } from '../../core/Page.jsx';
import { Avatar } from '../../core/Avatar.jsx';
import { CoverImage } from '../../core/ui.jsx';
import { TopSearchCard } from './TopSearchCard.jsx';
import '../../styles/resource-page.css';

/** Resource detail page — hero, dynamic tab navigation, chat-style discussion. */
export const ResourcePage = () => {
  const { key } = useParams();
  const { manifest } = useSite();
  const t = useT();
  const { data, error, loading } = useApi(`/api/publications/${encodeURIComponent(key)}`);
  const [tab, setTab] = useState('overview');
  const to = (path) => withSitePrefix(manifest.site.pathPrefix, path);

  if (loading && !data) return <Loading />;
  if (error) return <div className="container py-5"><div className="alert alert-warning">{error.message}</div></div>;
  if (!data) return null;

  const item = data.data;
  const jsonLd = {
    '@context': 'https://schema.org',
    '@type': 'CreativeWork',
    name: item.title,
    description: item.excerpt,
    url: `${manifest.site.publicUrl}${publicationPath(item)}`,
    datePublished: item.yearPublished ? String(item.yearPublished) : undefined,
    dateModified: item.updatedAt,
    author: item.author ? { '@type': 'Person', name: item.author.name } : undefined,
    publisher: { '@type': 'Organization', name: manifest.settings.site_name },
  };

  const versions = data.versions ?? [];
  const canSubmitVersion = Boolean(manifest.user) && manifest.settings.enable_version_submission !== false && !item.isVersion;
  const tabs = [
    { id: 'overview', label: t('ui_body.overview', 'Overview'), icon: 'fa-book-open' },
    { id: 'details', label: t('ui_body.details_citation', 'Details & Citation'), icon: 'fa-circle-info' },
    ...(item.attachments.length > 0 ? [{ id: 'attachments', label: t('ui_body.attachments', 'Attachments'), icon: 'fa-paperclip', count: item.attachments.length }] : []),
    ...(versions.length > 1 ? [{ id: 'versions', label: t('ui_body.versions', 'Versions'), icon: 'fa-code-branch', count: versions.length }] : []),
    { id: 'discussion', label: t('ui_body.discussion', 'Discussion'), icon: 'fa-comments', count: item.comments.length },
  ];

  return (
    <>
      <Seo title={item.title} description={item.excerpt || `${item.title} on ${manifest.settings.site_name}.`} path={to(publicationPath(item))} type="article" image={item.cover} jsonLd={jsonLd}>
        <meta name="citation_title" content={item.title} />
        {item.yearPublished && <meta name="citation_publication_date" content={String(item.yearPublished)} />}
        {item.citation.authors && <meta name="citation_author" content={item.citation.authors} />}
        {item.citation.doi && <meta name="citation_doi" content={item.citation.doi} />}
      </Seo>

      {/* Hero */}
      <header className="kh-res-hero">
        <div className="container">
          <nav aria-label="Breadcrumb">
            <ol className="breadcrumb">
              <li className="breadcrumb-item"><Link to={to('/')}>{t('frontend_nav.home', 'Home')}</Link></li>
              <li className="breadcrumb-item"><Link to={to('/records/search')}>{t('ui_body.browse_resources', 'Browse resources')}</Link></li>
              <li className="breadcrumb-item active" aria-current="page">{item.theme?.name ?? t('ui_body.resource', 'Resource')}</li>
            </ol>
          </nav>
          <h1>{item.title}</h1>
          <div className="kh-res-meta">
            {item.author?.name && <span className="kh-res-chip"><i className="fa fa-bank" aria-hidden="true" />{item.author.name}</span>}
            {item.yearPublished && <span className="kh-res-chip"><i className="fa fa-calendar" aria-hidden="true" />{item.yearPublished}</span>}
            <span className="kh-res-chip"><i className="fa fa-clock" aria-hidden="true" />{formatDate(item.createdAt)}</span>
            <span className="kh-res-chip"><i className="fa fa-eye" aria-hidden="true" />{item.visits.toLocaleString()} {t('ui_body.views', 'views')}</span>
            {item.dataCategory && <span className="kh-res-chip"><i className="fa fa-folder-open" aria-hidden="true" />{item.dataCategory}</span>}
            {item.isVersion && <span className="kh-res-chip"><i className="fa fa-code-branch" aria-hidden="true" />{t('ui_body.version', 'Version')} {item.versionNo}</span>}
          </div>
          {item.tags.length > 0 && (
            <div className="kh-res-tags">
              {item.tags.map((tag) => <Link key={tag.id} to={to(`/records/tag/${tag.slug ?? tag.id}`)}>#{tag.name}</Link>)}
            </div>
          )}
        </div>
      </header>

      {/* Overlapping body card with dynamic tab navigation */}
      <div className="gray kh-res-body">
        <div className="container">
          <article className="kh-res-card">
            <div className="kh-res-topline">
              <CoverImage className="kh-res-cover" src={item.cover} alt={item.title} />
              <div className="kh-res-actions">
                {item.file && (
                  <a className="btn theme-bg text-white" href={item.file} rel="noopener noreferrer" target="_blank">
                    <i className="fa fa-file-arrow-down mr-1" aria-hidden="true" /> {t('ui_body.open_resource', 'Open Resource')}
                  </a>
                )}
                <button type="button" className="btn btn-outline-success bg-white" onClick={() => setTab('discussion')}>
                  <i className="fa fa-comments mr-1" aria-hidden="true" /> {t('ui_body.join_discussion', 'Join the discussion')} ({item.comments.length})
                </button>
                {canSubmitVersion && (
                  <Link className="btn btn-outline-success bg-white" to={to(`/account/publish?version_of=${item.id}`)}>
                    <i className="fa fa-code-branch mr-1" aria-hidden="true" /> {t('ui_body.submit_version', 'Submit Version')}
                  </Link>
                )}
              </div>
            </div>

            <nav className="kh-res-tabs" role="tablist" aria-label="Resource sections">
              {tabs.map((entry) => (
                <button key={entry.id} type="button" role="tab" aria-selected={tab === entry.id}
                  className={`kh-res-tab ${tab === entry.id ? 'is-active' : ''}`} onClick={() => setTab(entry.id)}>
                  <i className={`fa ${entry.icon} mr-1`} aria-hidden="true" /> {entry.label}
                  {entry.count !== undefined && <span className="kh-res-tab-count">{entry.count}</span>}
                </button>
              ))}
            </nav>

            {tab === 'overview' && (
              <div className="kh-res-pane" role="tabpanel">
                {item.description
                  ? <div style={{ textAlign: 'justify', lineHeight: 1.75 }} dangerouslySetInnerHTML={{ __html: item.description }} />
                  : <p className="text-muted mb-0">{t('ui_body.no_description', 'No description was provided for this resource.')}</p>}
              </div>
            )}

            {tab === 'details' && (
              <div className="kh-res-pane" role="tabpanel">
                <dl className="kh-res-detail-grid mb-0">
                  {item.theme?.name && <div className="kh-res-detail"><dt>{t('ui_body.theme', 'Theme')}</dt><dd>{item.theme.name}</dd></div>}
                  {item.subTheme?.name && <div className="kh-res-detail"><dt>{t('ui_body.sub_theme', 'Sub theme')}</dt><dd>{item.subTheme.name}</dd></div>}
                  {item.dataCategory && <div className="kh-res-detail"><dt>{t('ui_body.category', 'Category')}</dt><dd>{item.dataCategory}</dd></div>}
                  {item.citation.authors && <div className="kh-res-detail"><dt>{t('ui_body.authors', 'Authors')}</dt><dd>{item.citation.authors}</dd></div>}
                  {item.citation.publisher && <div className="kh-res-detail"><dt>{t('ui_body.publisher', 'Publisher')}</dt><dd>{item.citation.publisher}</dd></div>}
                  {item.citation.doi && <div className="kh-res-detail"><dt>DOI</dt><dd>{item.citation.doi}</dd></div>}
                  {item.citation.issn && <div className="kh-res-detail"><dt>ISSN</dt><dd>{item.citation.issn}</dd></div>}
                  {item.citation.journal && <div className="kh-res-detail"><dt>{t('ui_body.journal', 'Journal')}</dt><dd>{item.citation.journal.name}</dd></div>}
                  {item.license && (
                    <div className="kh-res-detail"><dt>{t('ui_body.licence', 'Licence')}</dt>
                      <dd>{item.license.url ? <a href={item.license.url} target="_blank" rel="noopener noreferrer">{item.license.name}</a> : item.license.name}</dd>
                    </div>
                  )}
                  {item.citation.funder && <div className="kh-res-detail"><dt>{t('ui_body.funder', 'Funder')}</dt><dd>{item.citation.funder}</dd></div>}
                </dl>
                {item.countries.length > 0 && (
                  <>
                    <h2 className="ft-bold mt-4 mb-2" style={{ fontSize: '0.95rem', color: '#0f172a' }}>{t('ui_body.geographical_coverage', 'Geographical coverage')}</h2>
                    <div className="kh-res-countries">{item.countries.map((country) => <span key={country.id}>{country.name}</span>)}</div>
                  </>
                )}
              </div>
            )}

            {tab === 'attachments' && (
              <div className="kh-res-pane" role="tabpanel">
                <ul className="list-unstyled mb-0">
                  {item.attachments.map((file) => (
                    <li key={file.id} className="mb-2">
                      <a href={file.url} target="_blank" rel="noopener noreferrer" className="d-inline-flex align-items-center" style={{ gap: 8 }}>
                        <i className="fa fa-paperclip" aria-hidden="true" /> {file.name}
                      </a>
                    </li>
                  ))}
                </ul>
              </div>
            )}

            {tab === 'versions' && (
              <div className="kh-res-pane" role="tabpanel">
                <ul className="list-unstyled mb-0">
                  {versions.map((version) => (
                    <li key={version.id} className="d-flex align-items-center py-2" style={{ gap: 10, borderBottom: '1px solid #eef2f6' }}>
                      <span className="kh-res-chip" style={{ background: version.isOriginal ? 'rgba(0,98,57,0.1)' : '#eef2f6', color: '#0f172a', border: 0 }}>
                        <i className={`fa ${version.isOriginal ? 'fa-star' : 'fa-code-branch'}`} aria-hidden="true" />
                        {version.isOriginal ? t('ui_body.original', 'Original') : `${t('ui_body.version', 'Version')} ${version.versionNo}`}
                      </span>
                      {version.current
                        ? <span className="ft-medium" style={{ color: '#0f172a' }}>{version.title} <small className="text-muted">({t('ui_body.viewing', 'viewing')})</small></span>
                        : <Link to={to(version.path)} className="ft-medium">{version.title}</Link>}
                      <small className="text-muted" style={{ marginInlineStart: 'auto' }}>{formatDate(version.createdAt)}</small>
                    </li>
                  ))}
                </ul>
                {canSubmitVersion && (
                  <Link className="btn btn-sm theme-bg text-white mt-3" to={to(`/account/publish?version_of=${item.id}`)}>
                    <i className="fa fa-plus mr-1" aria-hidden="true" /> {t('ui_body.submit_version', 'Submit Version')}
                  </Link>
                )}
              </div>
            )}

            {tab === 'discussion' && (
              <div className="kh-res-pane" role="tabpanel">
                <Discussion item={item} />
              </div>
            )}
          </article>

          {data.related.length > 0 && (
            <section className="kh-res-related" aria-labelledby="related">
              <div className="sec_title position-relative text-center mb-3">
                <h2 id="related" className="ft-bold" style={{ fontSize: '1.3rem' }}>{t('ui_body.related_resources', 'Related Resources')}</h2>
              </div>
              <div className="row align-items-center">
                {data.related.slice(0, 6).map((related) => <TopSearchCard item={related} key={related.id} />)}
              </div>
            </section>
          )}
        </div>
      </div>
    </>
  );
};

/**
 * Threaded discussion: top-level comments with nested replies, live composers,
 * and moderation-aware posting (pending replies show a chip until approved).
 */
const Discussion = ({ item }) => {
  const { manifest } = useSite();
  const t = useT();
  const user = manifest.user;
  const to = (path) => withSitePrefix(manifest.site.pathPrefix, path);
  const [comments, setComments] = useState(item.comments);
  const [replyTo, setReplyTo] = useState(null);
  const [notice, setNotice] = useState(null);

  // Flat list -> one-level thread tree (replies sorted oldest-first under their parent).
  const threads = useMemo(() => {
    const sorted = [...comments].sort((a, b) => new Date(a.createdAt) - new Date(b.createdAt));
    const roots = sorted.filter((comment) => !comment.parentId);
    const byParent = new Map();
    for (const comment of sorted) {
      if (!comment.parentId) continue;
      if (!byParent.has(comment.parentId)) byParent.set(comment.parentId, []);
      byParent.get(comment.parentId).push(comment);
    }
    return roots.map((root) => ({ ...root, replies: byParent.get(root.id) ?? [] }));
  }, [comments]);

  const post = async (comment, parentId) => {
    const result = await apiFetch(`/api/publications/${item.id}/comments`, { method: 'POST', body: { comment, parent_id: parentId ?? undefined } });
    const resolvedParent = parentId ? (result.parentId ?? parentId) : null;
    setComments((previous) => [...previous, {
      id: `own-${Date.now()}`,
      comment,
      createdAt: new Date().toISOString(),
      parentId: resolvedParent,
      own: true,
      pending: result.status !== 'approved',
      user: { id: user.id, name: user.name, photo: user.photo },
    }]);
    if (result.status !== 'approved') {
      setNotice({ ok: t('ui_body.comment_pending', 'Thank you! Your comment is awaiting moderation and will appear publicly once approved.') });
    }
    setReplyTo(null);
  };

  const Bubble = ({ comment, isReply }) => (
    <div className={`kh-res-msg ${comment.own ? 'is-own' : ''} ${isReply ? 'is-reply' : ''}`}>
      <Avatar photo={comment.user.photo} name={comment.user.name} size={isReply ? 30 : 38} />
      <div className="kh-res-bubble">
        <div className="kh-res-bubble-head">
          <strong>{comment.user.name}</strong>
          <time dateTime={comment.createdAt} title={formatDate(comment.createdAt)}>{timeAgo(comment.createdAt)}</time>
          {comment.pending && <span className="kh-res-pending-chip">{t('ui_body.pending', 'Pending review')}</span>}
        </div>
        <p>{comment.comment}</p>
        {user && !comment.pending && (
          <button type="button" className="kh-res-reply-btn" onClick={() => setReplyTo({ id: comment.parentId ?? comment.id, name: comment.user.name })}>
            <i className="fa fa-reply" aria-hidden="true" /> {t('ui_body.reply', 'Reply')}
          </button>
        )}
      </div>
    </div>
  );

  return (
    <div className="kh-res-chat">
      {threads.length === 0 && (
        <p className="text-muted">{t('ui_body.no_comments', 'No comments yet — start the discussion below.')}</p>
      )}
      {threads.map((thread) => (
        <div className="kh-res-thread" key={thread.id}>
          <Bubble comment={thread} />
          {(thread.replies.length > 0 || replyTo?.id === thread.id) && (
            <div className="kh-res-replies">
              {thread.replies.length > 0 && (
                <p className="kh-res-replies-count">
                  <i className="fa fa-arrow-turn-down" aria-hidden="true" /> {thread.replies.length} {thread.replies.length === 1 ? t('ui_body.reply_one', 'reply') : t('ui_body.reply_many', 'replies')}
                </p>
              )}
              {thread.replies.map((reply) => <Bubble key={reply.id} comment={reply} isReply />)}
              {user && replyTo?.id === thread.id && (
                <Composer user={user} autoFocus compact
                  placeholder={`${t('ui_body.replying_to', 'Replying to')} ${replyTo.name}…`}
                  onCancel={() => setReplyTo(null)}
                  onPost={(text) => post(text, thread.id)} />
              )}
            </div>
          )}
        </div>
      ))}

      {notice?.ok && <div role="status" className="alert alert-success">{notice.ok}</div>}
      {notice?.error && <div role="alert" className="alert alert-danger">{notice.error}</div>}

      {user ? (
        <Composer user={user} placeholder={t('ui_body.write_comment', 'Share your thoughts on this resource…')}
          hint={t('ui_body.comment_hint', 'Be constructive and cite sources where possible. Ctrl+Enter to post.')}
          onPost={(text) => post(text, null)} />
      ) : (
        <div className="kh-res-composer" style={{ alignItems: 'center' }}>
          <i className="fa fa-circle-user" style={{ fontSize: 34, color: '#cbd5e1' }} aria-hidden="true" />
          <p className="mb-0">
            <Link to={to('/login')} className="ft-medium">{t('ui_body.sign_in', 'Sign in')}</Link> {t('ui_body.sign_in_to_comment', 'to join the discussion.')}
          </p>
        </div>
      )}
    </div>
  );
};

/** Comment composer, reused for new threads and inline replies. */
const Composer = ({ user, placeholder, hint, compact = false, autoFocus = false, onPost, onCancel }) => {
  const t = useT();
  const [draft, setDraft] = useState('');
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState('');

  const submit = async () => {
    const text = draft.trim();
    if (!text) return;
    setBusy(true);
    setError('');
    try {
      await onPost(text);
      setDraft('');
    } catch (err) {
      setError(err.message);
    } finally {
      setBusy(false);
    }
  };

  return (
    <div className={`kh-res-composer ${compact ? 'is-compact' : ''}`}>
      <Avatar photo={user.photo} name={user.name} size={compact ? 30 : 38} />
      <div className="kh-res-composer-box">
        <textarea value={draft} placeholder={placeholder} autoFocus={autoFocus}
          onChange={(event) => setDraft(event.target.value)}
          onKeyDown={(event) => { if ((event.metaKey || event.ctrlKey) && event.key === 'Enter') submit(); }} />
        {error && <small className="text-danger d-block">{error}</small>}
        <div className="kh-res-composer-foot">
          <small>{hint ?? ''}</small>
          <span className="d-inline-flex" style={{ gap: 7 }}>
            {onCancel && (
              <button type="button" className="btn btn-sm btn-outline-secondary bg-white" onClick={onCancel}>{t('ui_body.cancel', 'Cancel')}</button>
            )}
            <button type="button" className={`btn ${compact ? 'btn-sm' : ''} theme-bg text-white`} disabled={busy || !draft.trim()} onClick={submit}>
              <i className={`fa ${busy ? 'fa-spinner fa-spin' : 'fa-paper-plane'} mr-1`} aria-hidden="true" /> {busy ? t('ui_body.posting', 'Posting…') : compact ? t('ui_body.reply', 'Reply') : t('ui_body.post_comment', 'Post comment')}
            </button>
          </span>
        </div>
      </div>
    </div>
  );
};
