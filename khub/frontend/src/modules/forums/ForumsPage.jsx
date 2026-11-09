import { useState } from 'react';
import { Link, useParams } from 'react-router-dom';
import { Seo } from '../../core/Seo.jsx';
import { useApi } from '../../core/hooks.js';
import { apiFetch } from '../../core/api.js';
import { formatDate } from '../../core/format.js';
import { useSite } from '../../core/site-context.jsx';
import { withSitePrefix } from '@khub/shared/urls';
import { Page, Loading } from '../../core/Page.jsx';

export const ForumsPage = () => {
  const { manifest } = useSite();
  const [term, setTerm] = useState('');
  const [query, setQuery] = useState({});
  const { data, error, loading } = useApi('/api/forums', { ...query, per_page: 12 });
  const to = (path) => withSitePrefix(manifest.site.pathPrefix, path);

  return (
    <Page title="Public Discussion Forums" crumb="Forums" lead="Join discussions on pressing health topics and challenges.">
      <Seo title="Discussion forums" path="/forums" description={`Public health discussion forums on ${manifest.settings.site_name}: ask questions, share evidence, and connect with experts.`} />
      <form className="row no-gutters bg-white rounded border mb-4" role="search"
        onSubmit={(event) => { event.preventDefault(); setQuery({ term: term.trim() }); }}>
        <div className="col-md-9 col-12">
          <label htmlFor="forum-term" className="sr-only">Search forums</label>
          <input id="forum-term" className="form-control border-0 main-search" type="search" value={term} onChange={(event) => setTerm(event.target.value)} placeholder="Search discussions…" />
        </div>
        <div className="col-md-3 col-12">
          <button className="btn theme-bg text-white full-width" type="submit" style={{ minHeight: 54, width: '100%' }}>
            <i className="fa fa-magnifying-glass mr-1" aria-hidden="true" /> Search
          </button>
        </div>
      </form>
      {error && <div className="alert alert-danger" role="alert">{error.message}</div>}
      {loading && <Loading />}
      <div className="row">
        {data?.data.map((forum) => (
          <div className="col-12" key={forum.id}>
            <div className="jbr-wrap text-left border rounded bg-white p-3 mb-3">
              <h2 className="fs-md ft-medium mb-1" style={{ fontSize: '1.05rem' }}>
                <Link to={to(forum.path)}>{forum.title}</Link>
              </h2>
              <p className="text-muted medium mb-2">
                <i className="fa fa-user mr-1" aria-hidden="true" />{forum.author?.name}
                <span className="mx-2">·</span><i className="fa fa-clock mr-1" aria-hidden="true" />{formatDate(forum.createdAt)}
                <span className="mx-2">·</span><i className="fa fa-comments mr-1" aria-hidden="true" />{forum.commentCount} replies
                <span className="mx-2">·</span><i className="fa fa-eye mr-1" aria-hidden="true" />{forum.views} views
              </p>
              {forum.excerpt && <p className="mb-0">{forum.excerpt}</p>}
            </div>
          </div>
        ))}
      </div>
      {data && data.data.length === 0 && !loading && <div className="alert alert-light border">No discussions found.</div>}
    </Page>
  );
};

export const ForumPage = () => {
  const { key } = useParams();
  const { manifest } = useSite();
  const user = manifest.user;
  const { data, error, loading } = useApi(`/api/forums/${encodeURIComponent(key)}`);
  const [reply, setReply] = useState('');
  const [message, setMessage] = useState('');
  const to = (path) => withSitePrefix(manifest.site.pathPrefix, path);

  if (loading && !data) return <Loading />;
  if (error) return <Page title="Forums"><div className="alert alert-warning">{error.message}</div></Page>;
  if (!data) return null;
  const forum = data.data;

  const submit = async (event) => {
    event.preventDefault();
    try {
      const result = await apiFetch(`/api/forums/${forum.id}/comments`, { method: 'POST', body: { comment: reply } });
      setMessage(result.status === 'approved' ? 'Reply posted. It will appear after the next page load.' : 'Thanks — your reply will appear after moderation.');
      setReply('');
    } catch (err) {
      setMessage(err.message);
    }
  };

  const renderComment = (comment, depth = 0) => (
    <li key={comment.id} className="app-comment mb-2" style={{ marginLeft: depth * 24 }}>
      <strong>{comment.author.name}</strong>
      <span className="text-muted small ml-2">{formatDate(comment.createdAt)}</span>
      <p className="mb-1">{comment.comment}</p>
      {comment.replies.length > 0 && <ul className="list-unstyled">{comment.replies.map((child) => renderComment(child, depth + 1))}</ul>}
    </li>
  );

  return (
    <div className="gray py-4">
      <Seo title={forum.title} path={to(forum.path)} type="article" description={forum.excerpt} />
      <div className="container">
        <nav className="mb-3" aria-label="Breadcrumb">
          <ol className="breadcrumb mb-0" style={{ background: 'transparent', padding: 0, fontSize: '0.875rem' }}>
            <li className="breadcrumb-item"><Link to={to('/')}>Home</Link></li>
            <li className="breadcrumb-item"><Link to={to('/forums')}>Forums</Link></li>
            <li className="breadcrumb-item active" aria-current="page">Discussion</li>
          </ol>
        </nav>
        <div className="bg-white rounded border p-4 mb-3">
          <h1 className="ft-bold mb-2" style={{ fontSize: '1.35rem', color: '#0f172a' }}>{forum.title}</h1>
          <p className="text-muted medium">
            <i className="fa fa-user mr-1" aria-hidden="true" />{forum.author?.name}
            <span className="mx-2">·</span>{formatDate(forum.createdAt)}
            <span className="mx-2">·</span>{forum.views} views
          </p>
          <div style={{ textAlign: 'justify' }} dangerouslySetInnerHTML={{ __html: forum.description ?? '' }} />
        </div>

        <div className="bg-white rounded border p-4">
          <h2 className="ft-bold mb-3" style={{ fontSize: '1.1rem' }}>Replies ({forum.comments.length})</h2>
          <ul className="list-unstyled mb-4">{forum.comments.map((comment) => renderComment(comment))}</ul>
          {user ? (
            <form onSubmit={submit}>
              <div className="form-group">
                <label htmlFor="reply">Your reply</label>
                <textarea id="reply" className="form-control" rows={4} value={reply} onChange={(event) => setReply(event.target.value)} required minLength={2} />
              </div>
              <button className="btn theme-bg text-white" type="submit">Post Reply</button>
              {message && <p role="status" className="medium mt-2 mb-0">{message}</p>}
            </form>
          ) : (
            <p className="mb-0"><Link to={to('/login')} className="theme-cl">Log in</Link> to join this discussion.</p>
          )}
        </div>
      </div>
    </div>
  );
};
