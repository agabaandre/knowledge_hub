import { useState } from 'react';
import { Link, useNavigate, useParams } from 'react-router-dom';
import { Seo } from '../../core/Seo.jsx';
import { useApi } from '../../core/hooks.js';
import { apiFetch } from '../../core/api.js';
import { useSite } from '../../core/site-context.jsx';
import { withSitePrefix } from '@khub/shared/urls';
import { Page, Loading } from '../../core/Page.jsx';
import { Avatar } from '../../core/Avatar.jsx';
import { Modal, FormField, Flash } from '../../core/ui.jsx';
import { TopSearchCard } from '../publications/TopSearchCard.jsx';
import { formatDate } from '../../core/format.js';

/** Communities of Practice — browse, search, join, and start communities. */
export const CommunitiesPage = () => {
  const { manifest } = useSite();
  const user = manifest.user;
  const [term, setTerm] = useState('');
  const [query, setQuery] = useState({});
  const [creating, setCreating] = useState(false);
  const [message, setMessage] = useState(null);
  const { data, error, loading, reload } = useApi('/api/communities', { ...query, per_page: 24 });
  const to = (path) => withSitePrefix(manifest.site.pathPrefix, path);
  const navigate = useNavigate();

  return (
    <Page title="Communities of Practice" crumb="Communities" lead="Collaborate on public health themes with peers across Africa.">
      <Seo title="Communities of practice" path="/communities" description={`Join communities of practice on ${manifest.settings.site_name}: collaborate on public health themes with peers across Africa.`} />
      <Flash message={message} />
      <div className="d-flex align-items-center flex-wrap mb-4" style={{ gap: 10 }}>
        <form className="d-flex bg-white rounded border flex-grow-1" role="search" style={{ minWidth: 260 }}
          onSubmit={(event) => { event.preventDefault(); setQuery({ term: term.trim() }); }}>
          <input className="form-control border-0" type="search" value={term} placeholder="Search communities…"
            onChange={(event) => setTerm(event.target.value)} aria-label="Search communities" />
          <button className="btn theme-bg text-white px-4" type="submit"><i className="fa fa-magnifying-glass" aria-hidden="true" /></button>
        </form>
        {user && (
          <button type="button" className="btn btn-outline-success bg-white" onClick={() => setCreating(true)}>
            <i className="fa fa-plus mr-1" aria-hidden="true" /> Start a community
          </button>
        )}
      </div>
      {error && <div className="alert alert-danger" role="alert">{error.message}</div>}
      {loading && <Loading />}
      <div className="row">
        {data?.data.map((community) => (
          <div className="col-lg-4 col-md-6 col-12 mb-3" key={community.id}>
            <div className="jbr-wrap border rounded bg-white p-3 h-100 d-flex flex-column">
              <h2 className="fs-md ft-medium mb-1" style={{ fontSize: '1.02rem' }}>
                <Link to={to(community.path)}>{community.name}</Link>
              </h2>
              <p className="text-muted medium mb-2">
                {[community.organisation, community.country].filter(Boolean).join(' · ')}
              </p>
              {community.excerpt && <p className="medium mb-2">{community.excerpt}</p>}
              <p className="medium mb-2 mt-auto"><i className="fa fa-users mr-1 theme-cl" aria-hidden="true" />{community.memberCount} members</p>
              <Link to={to(community.path)} className="btn btn-sm theme-bg text-white align-self-start">
                <i className="fa fa-arrow-right mr-1" aria-hidden="true" /> Open community
              </Link>
            </div>
          </div>
        ))}
      </div>
      {data && data.data.length === 0 && !loading && <div className="alert alert-light border">No public communities match.</div>}
      {creating && (
        <CreateCommunityModal
          onClose={() => setCreating(false)}
          onCreated={(payload) => { setCreating(false); navigate(to(payload.path)); }}
        />
      )}
    </Page>
  );
};

/** Individual form: start a new community (creator becomes its first admin member). */
const CreateCommunityModal = ({ onClose, onCreated }) => {
  const [draft, setDraft] = useState({ name: '', description: '', organisation: '', is_public: true });
  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);

  const create = async () => {
    setBusy(true);
    setError('');
    try {
      const payload = await apiFetch('/api/communities', { method: 'POST', body: draft });
      onCreated(payload);
    } catch (err) {
      setError(err.details ? err.details.map((detail) => detail.message).join(' ') : err.message);
      setBusy(false);
    }
  };

  return (
    <Modal title="Start a community of practice" icon="fa-people-group" onClose={onClose}
      footer={(
        <>
          <button type="button" className="btn btn-sm btn-outline-secondary" onClick={onClose}>Cancel</button>
          <button type="button" className="btn btn-sm theme-bg text-white" disabled={busy || draft.name.trim().length < 3 || draft.description.trim().length < 10} onClick={create}>
            {busy ? 'Creating…' : 'Create community'}
          </button>
        </>
      )}>
      {error && <div role="alert" className="alert alert-danger">{error}</div>}
      <FormField label="Name" required>
        <input className="form-control" value={draft.name} autoFocus onChange={(event) => setDraft({ ...draft, name: event.target.value })} />
      </FormField>
      <FormField label="Description" required hint="What is this community about, and who should join?">
        <textarea className="form-control" rows={4} value={draft.description} onChange={(event) => setDraft({ ...draft, description: event.target.value })} />
      </FormField>
      <FormField label="Organisation">
        <input className="form-control" value={draft.organisation} onChange={(event) => setDraft({ ...draft, organisation: event.target.value })} />
      </FormField>
      <label className="mb-0 d-flex align-items-center" style={{ gap: 8, cursor: 'pointer', fontWeight: 500 }}>
        <input type="checkbox" checked={draft.is_public} onChange={(event) => setDraft({ ...draft, is_public: event.target.checked })} />
        Public community — anyone can join instantly (private ones need member approval)
      </label>
    </Modal>
  );
};

/** Community detail: about, members, shared resources, and membership actions. */
export const CommunityPage = () => {
  const { key } = useParams();
  const { manifest } = useSite();
  const user = manifest.user;
  const { data, error, loading, reload } = useApi(`/api/communities/${encodeURIComponent(key)}`);
  const [message, setMessage] = useState(null);
  const [busy, setBusy] = useState(false);
  const to = (path) => withSitePrefix(manifest.site.pathPrefix, path);

  if (loading && !data) return <Loading />;
  if (error) return <Page title="Communities"><div className="alert alert-warning">{error.message}</div></Page>;
  if (!data) return null;
  const community = data.data;

  const membershipAction = async (join) => {
    setBusy(true);
    setMessage(null);
    try {
      const result = await apiFetch(`/api/communities/${community.id}/${join ? 'join' : 'leave'}`, { method: join ? 'POST' : 'DELETE' });
      setMessage({ ok: result.status === 'pending' ? 'Request sent — a community admin will review it.' : join ? 'Welcome to the community!' : 'You left the community.' });
      reload?.();
      window.location.reload();
    } catch (err) {
      setMessage({ error: err.message });
      setBusy(false);
    }
  };

  const membershipButton = () => {
    if (!user) return <Link to={to('/login')} className="btn theme-bg text-white"><i className="fa fa-right-to-bracket mr-1" aria-hidden="true" /> Sign in to join</Link>;
    if (community.membership === 'none') {
      return (
        <button type="button" className="btn theme-bg text-white" disabled={busy} onClick={() => membershipAction(true)}>
          <i className="fa fa-user-plus mr-1" aria-hidden="true" /> {community.isPublic ? 'Join community' : 'Request to join'}
        </button>
      );
    }
    if (community.membership === 'pending') return <span className="btn btn-outline-secondary bg-white" style={{ pointerEvents: 'none' }}><i className="fa fa-hourglass-half mr-1" aria-hidden="true" /> Request pending</span>;
    return (
      <button type="button" className="btn btn-outline-danger bg-white" disabled={busy} onClick={() => membershipAction(false)}>
        <i className="fa fa-right-from-bracket mr-1" aria-hidden="true" /> Leave community
      </button>
    );
  };

  return (
    <Page title={community.name} crumb="Communities" lead={[community.organisation, community.country].filter(Boolean).join(' · ')}>
      <Seo title={community.name} path={to(community.path)} description={community.excerpt} />
      <Flash message={message} />
      <div className="row">
        <div className="col-lg-8">
          <div className="bg-white rounded border p-4 mb-3">
            <div className="d-flex align-items-start justify-content-between flex-wrap" style={{ gap: 10 }}>
              <p className="medium text-muted mb-2">
                <i className="fa fa-users mr-1 theme-cl" aria-hidden="true" />{community.memberCount} members
                <span className="mx-2">·</span>
                <i className={`fa ${community.isPublic ? 'fa-earth-africa' : 'fa-lock'} mr-1 theme-cl`} aria-hidden="true" />{community.isPublic ? 'Public' : 'Private'}
                <span className="mx-2">·</span>since {formatDate(community.createdAt)}
                {community.membership === 'admin' && <span className="ml-2 badge badge-success">community admin</span>}
              </p>
              {membershipButton()}
            </div>
            <div style={{ textAlign: 'justify' }}>{community.description}</div>
          </div>

          {community.publications.length > 0 && (
            <>
              <h2 className="ft-bold mb-3" style={{ fontSize: '1.15rem' }}>Shared resources</h2>
              <div className="row align-items-center">
                {community.publications.map((item) => <TopSearchCard item={item} key={item.id} />)}
              </div>
            </>
          )}
        </div>
        <aside className="col-lg-4" aria-label="Members">
          <div className="bg-white rounded border p-4 mb-3">
            <h2 className="ft-bold mb-3" style={{ fontSize: '1.05rem' }}>Members ({community.memberCount})</h2>
            {community.members.map((member) => (
              <div className="d-flex align-items-center mb-2" style={{ gap: 10 }} key={member.id}>
                <Avatar photo={member.photo} name={member.name} size={34} />
                <span style={{ minWidth: 0 }}>
                  <span className="ft-medium d-block" style={{ color: '#0f172a' }}>
                    {member.name} {member.isAdmin && <i className="fa fa-shield-halved theme-cl" title="Community admin" aria-hidden="true" />}
                  </span>
                  {member.organisation && <small className="text-muted">{member.organisation}</small>}
                </span>
              </div>
            ))}
          </div>
        </aside>
      </div>
    </Page>
  );
};
