import { useState } from 'react';
import { Link, useParams } from 'react-router-dom';
import { Seo } from '../../core/Seo.jsx';
import { useApi } from '../../core/hooks.js';
import { apiFetch } from '../../core/api.js';
import { formatDate } from '../../core/format.js';
import { useSite } from '../../core/site-context.jsx';
import { withSitePrefix } from '@khub/shared/urls';
import { Page, Loading } from '../../core/Page.jsx';
import { Avatar } from '../../core/Avatar.jsx';
import { Modal, FormField, Flash, StatusBadge } from '../../core/ui.jsx';

export const ForumsPage = () => {
  const { manifest } = useSite();
  const user = manifest.user;
  const [term, setTerm] = useState('');
  const [query, setQuery] = useState({});
  const [creating, setCreating] = useState(false);
  const [message, setMessage] = useState(null);
  const { data, error, loading } = useApi('/api/forums', { ...query, per_page: 12 });
  const to = (path) => withSitePrefix(manifest.site.pathPrefix, path);

  return (
    <Page title="Public Discussion Forums" crumb="Forums" lead="Join discussions on pressing health topics and challenges.">
      <Seo title="Discussion forums" path="/forums" description={`Public health discussion forums on ${manifest.settings.site_name}: ask questions, share evidence, and connect with experts.`} />
      <Flash message={message} />
      {user && (
        <p className="text-right mb-2">
          <button type="button" className="btn btn-outline-success bg-white" onClick={() => setCreating(true)}>
            <i className="fa fa-plus mr-1" aria-hidden="true" /> Start a discussion
          </button>
        </p>
      )}
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
                {forum.isPinned && <span className="badge badge-success mr-2"><i className="fa fa-thumbtack mr-1" aria-hidden="true" />Pinned</span>}
                {forum.isLocked && <span className="badge badge-secondary mr-2"><i className="fa fa-lock mr-1" aria-hidden="true" />Locked</span>}
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
      {creating && (
        <StartDiscussionModal
          onClose={() => setCreating(false)}
          onCreated={(result) => { setCreating(false); setMessage({ ok: result.message }); }}
        />
      )}
    </Page>
  );
};

/** Individual form: start a discussion thread (goes to the approvals inbox). */
const StartDiscussionModal = ({ onClose, onCreated }) => {
  const [draft, setDraft] = useState({ title: '', description: '', tags: '' });
  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);

  const create = async () => {
    setBusy(true);
    setError('');
    try {
      const result = await apiFetch('/api/forums', {
        method: 'POST',
        body: {
          title: draft.title.trim(),
          description: draft.description.trim(),
          tags: draft.tags.split(',').map((tag) => tag.trim()).filter(Boolean).slice(0, 10),
        },
      });
      onCreated(result);
    } catch (err) {
      setError(err.details ? err.details.map((detail) => detail.message).join(' ') : err.message);
      setBusy(false);
    }
  };

  return (
    <Modal title="Start a discussion" icon="fa-comments" onClose={onClose}
      footer={(
        <>
          <button type="button" className="btn btn-sm btn-outline-secondary" onClick={onClose}>Cancel</button>
          <button type="button" className="btn btn-sm theme-bg text-white" disabled={busy || draft.title.trim().length < 5 || draft.description.trim().length < 20} onClick={create}>
            {busy ? 'Submitting…' : 'Submit for review'}
          </button>
        </>
      )}>
      {error && <div role="alert" className="alert alert-danger">{error}</div>}
      <FormField label="Title" required>
        <input className="form-control" value={draft.title} autoFocus maxLength={255} onChange={(event) => setDraft({ ...draft, title: event.target.value })} />
      </FormField>
      <FormField label="Your question or topic" required hint="At least 20 characters — give peers enough context to respond.">
        <textarea className="form-control" rows={5} value={draft.description} onChange={(event) => setDraft({ ...draft, description: event.target.value })} />
      </FormField>
      <FormField label="Tags" hint="Comma-separated, e.g. surveillance, data-sharing">
        <input className="form-control" value={draft.tags} onChange={(event) => setDraft({ ...draft, tags: event.target.value })} />
      </FormField>
    </Modal>
  );
};

export const ForumPage = () => {
  const { key } = useParams();
  const { manifest } = useSite();
  const user = manifest.user;
  const { data, error, loading } = useApi(`/api/forums/${encodeURIComponent(key)}`);
  const [liked, setLiked] = useState(null); // null until first toggle; initial value comes from the API
  const [likeCount, setLikeCount] = useState(null);
  const [replyTo, setReplyTo] = useState(null); // comment id the inline composer attaches to
  const [message, setMessage] = useState('');
  const to = (path) => withSitePrefix(manifest.site.pathPrefix, path);

  if (loading && !data) return <Loading />;
  if (error) return <Page title="Forums"><div className="alert alert-warning">{error.message}</div></Page>;
  if (!data) return null;
  const forum = data.data;
  const isLiked = liked ?? forum.isLiked;
  const likes = likeCount ?? forum.likeCount;

  const toggleLike = async () => {
    try {
      const result = await apiFetch(`/api/forums/${forum.id}/like`, { method: 'POST' });
      setLiked(result.liked);
      setLikeCount(result.likeCount);
    } catch (err) {
      setMessage(err.message);
    }
  };

  const postReply = async (comment, parentId) => {
    const result = await apiFetch(`/api/forums/${forum.id}/comments`, { method: 'POST', body: { comment, parent_id: parentId } });
    setMessage(result.status === 'approved' ? 'Reply posted — it will appear after the next page load.' : 'Thanks — your reply will appear after moderation.');
    setReplyTo(null);
  };

  const Comment = ({ comment, depth = 0 }) => (
    <li className="mb-3" style={{ marginInlineStart: depth * 28 }}>
      <div className="d-flex" style={{ gap: 10 }}>
        <Avatar photo={comment.author.photo} name={comment.author.name} size={depth ? 28 : 34} />
        <div style={{ flex: 1, minWidth: 0 }}>
          <p className="mb-1">
            <strong style={{ color: '#0f172a' }}>{comment.author.name}</strong>
            <span className="text-muted small ml-2">{formatDate(comment.createdAt)}</span>
          </p>
          <p className="mb-1">{comment.comment}</p>
          <p className="mb-0 d-flex align-items-center" style={{ gap: 12 }}>
            <CommentLike comment={comment} disabled={!user} onError={setMessage} />
            {user && !forum.isLocked && (
              <button type="button" className="btn btn-link p-0 theme-cl" style={{ fontSize: '0.8rem', fontWeight: 600, textDecoration: 'none' }}
                onClick={() => setReplyTo(comment.id)}>
                <i className="fa fa-reply mr-1" aria-hidden="true" /> Reply
              </button>
            )}
          </p>
          {replyTo === comment.id && (
            <ReplyComposer placeholder={`Replying to ${comment.author.name}…`}
              onCancel={() => setReplyTo(null)} onPost={(text) => postReply(text, comment.id)} />
          )}
        </div>
      </div>
      {comment.replies.length > 0 && (
        <ul className="list-unstyled mt-2 mb-0">
          {comment.replies.map((child) => <Comment key={child.id} comment={child} depth={depth + 1} />)}
        </ul>
      )}
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
          <h1 className="ft-bold mb-2" style={{ fontSize: '1.35rem', color: '#0f172a' }}>
            {forum.isPinned && <span className="badge badge-success mr-2" style={{ verticalAlign: 'middle' }}><i className="fa fa-thumbtack mr-1" aria-hidden="true" />Pinned</span>}
            {forum.isLocked && <span className="badge badge-secondary mr-2" style={{ verticalAlign: 'middle' }}><i className="fa fa-lock mr-1" aria-hidden="true" />Locked</span>}
            {forum.title}
          </h1>
          <p className="text-muted medium d-flex align-items-center flex-wrap" style={{ gap: 6 }}>
            <Avatar photo={forum.author?.photo} name={forum.author?.name} size={26} />
            <span>{forum.author?.name}</span>
            <span className="mx-1">·</span>{formatDate(forum.createdAt)}
            <span className="mx-1">·</span><i className="fa fa-eye mr-1" aria-hidden="true" />{forum.views} views
          </p>
          <div style={{ textAlign: 'justify' }} dangerouslySetInnerHTML={{ __html: forum.description ?? '' }} />
          <div className="d-flex align-items-center flex-wrap mt-3" style={{ gap: 8 }}>
            <button type="button" disabled={!user} title={user ? '' : 'Sign in to like'} onClick={toggleLike}
              className={`btn btn-sm ${isLiked ? 'theme-bg text-white' : 'btn-outline-success bg-white'}`}>
              <i className="fa fa-thumbs-up mr-1" aria-hidden="true" /> {isLiked ? 'Liked' : 'Like'} ({likes})
            </button>
            {forum.tags.map((tag) => (
              <span key={tag} className="badge badge-light" style={{ fontSize: '0.78rem', padding: '6px 11px' }}>#{tag}</span>
            ))}
          </div>
        </div>

        <div className="bg-white rounded border p-4">
          <h2 className="ft-bold mb-3" style={{ fontSize: '1.1rem' }}>Replies ({forum.comments.length})</h2>
          {message && <p role="status" className="alert alert-light border py-2">{message}</p>}
          <ul className="list-unstyled mb-4">{forum.comments.map((comment) => <Comment key={comment.id} comment={comment} />)}</ul>
          {forum.isLocked && <div className="alert alert-light border mb-0"><i className="fa fa-lock mr-1" aria-hidden="true" /> This discussion is locked — new replies are closed.</div>}
          {user && !forum.isLocked && (
            <ReplyComposer placeholder="Join the discussion…" onPost={(text) => postReply(text, undefined)} />
          )}
          {!user && !forum.isLocked && (
            <p className="mb-0"><Link to={to('/login')} className="theme-cl">Log in</Link> to join this discussion.</p>
          )}
        </div>
      </div>
    </div>
  );
};

/** Like toggle for one reply (count updates in place). */
const CommentLike = ({ comment, disabled, onError }) => {
  const [state, setState] = useState({ liked: false, count: comment.likeCount });
  const toggle = async () => {
    try {
      const result = await apiFetch(`/api/forums/comments/${comment.id}/like`, { method: 'POST' });
      setState({ liked: result.liked, count: result.likeCount });
    } catch (err) {
      onError(err.message);
    }
  };
  return (
    <button type="button" className="btn btn-link p-0" disabled={disabled} onClick={toggle}
      style={{ fontSize: '0.8rem', fontWeight: 600, textDecoration: 'none', color: state.liked ? 'var(--theme-color-primary, #006239)' : '#94a3b8' }}>
      <i className="fa fa-thumbs-up mr-1" aria-hidden="true" /> {state.count}
    </button>
  );
};

/** Inline reply composer, reused for top-level and per-comment replies. */
const ReplyComposer = ({ placeholder, onPost, onCancel }) => {
  const [text, setText] = useState('');
  const [busy, setBusy] = useState(false);
  const submit = async () => {
    if (text.trim().length < 2) return;
    setBusy(true);
    try {
      await onPost(text.trim());
      setText('');
    } finally {
      setBusy(false);
    }
  };
  return (
    <div className="mt-2">
      <textarea className="form-control mb-2" rows={onCancel ? 2 : 4} value={text} placeholder={placeholder} autoFocus={Boolean(onCancel)}
        onChange={(event) => setText(event.target.value)} />
      <span className="d-inline-flex" style={{ gap: 6 }}>
        {onCancel && <button type="button" className="btn btn-sm btn-outline-secondary bg-white" onClick={onCancel}>Cancel</button>}
        <button type="button" className={`btn ${onCancel ? 'btn-sm' : ''} theme-bg text-white`} disabled={busy || text.trim().length < 2} onClick={submit}>
          <i className="fa fa-paper-plane mr-1" aria-hidden="true" /> {busy ? 'Posting…' : 'Post Reply'}
        </button>
      </span>
    </div>
  );
};
