import { useEffect, useState } from 'react';
import { Link, useNavigate, useSearchParams } from 'react-router-dom';
import { apiFetch } from '../../core/api.js';
import { useSite, usePermission } from '../../core/site-context.jsx';
import { withSitePrefix } from '@khub/shared/urls';
import { AdminShell, AdminCard } from './AdminShell.jsx';
import { StatusBadge, StatCard, Skeleton, Flash, STATUS_TONE, UserChip } from '../../core/ui.jsx';
import { ResourceCard, AttachmentsCard, CommentsCard, RejectModal } from './PublicationView.jsx';
import { formatDate } from '../../core/format.js';


/** Admin resource details — /admin/publications/details?id=N (same shape as production). */
export const PublicationAdminDetailsPage = () => {
  const [params] = useSearchParams();
  const id = Number(params.get('id'));
  const navigate = useNavigate();
  const { manifest } = useSite();
  const canEdit = usePermission('admin.publications.edit');
  const canReview = usePermission('admin.approvals.review');
  const to = (path) => withSitePrefix(manifest.site.pathPrefix, path);

  const [item, setItem] = useState(null);
  const [message, setMessage] = useState(null);
  const [busy, setBusy] = useState(false);
  const [rejecting, setRejecting] = useState(false);

  const load = () => apiFetch(`/api/admin/publications/${id}`).then((payload) => setItem(payload.data)).catch((err) => setMessage({ error: err.message }));
  useEffect(() => { if (id) load(); }, [id]); // eslint-disable-line

  const act = async (run, ok) => {
    setBusy(true);
    setMessage(null);
    try {
      await run();
      await load();
      if (ok) setMessage({ ok });
    } catch (err) {
      setMessage({ error: err.message });
    } finally {
      setBusy(false);
    }
  };

  const toggle = (field, value, ok) => act(() => apiFetch(`/api/admin/publications/${id}`, { method: 'PUT', body: { [field]: value } }), ok);
  const review = (action, reason) => act(
    () => apiFetch('/api/admin/approvals/review', { method: 'POST', body: { type: 'publication', id, action, reason } }),
    action === 'approve' ? 'Resource approved and published.' : 'Resource rejected.',
  );
  const reviewComment = (commentId, action) => act(
    () => apiFetch('/api/admin/approvals/review', { method: 'POST', body: { type: 'publication_comment', id: commentId, action } }),
    `Comment ${action}d.`,
  );
  const remove = async () => {
    if (!window.confirm(`Permanently delete "${item.title}"? This cannot be undone.`)) return;
    await act(() => apiFetch(`/api/admin/publications/${id}`, { method: 'DELETE' }));
    navigate(to('/admin/publications'));
  };

  if (!id) return <AdminShell title="Resource details"><div className="alert alert-warning">No resource id supplied.</div></AdminShell>;

  return (
    <AdminShell
      title={item ? item.title : 'Resource details'}
      subtitle={item ? `Submitted ${formatDate(item.createdAt)}${item.submittedBy ? ` by ${item.submittedBy.name}` : ''}` : undefined}
      actions={item && (
        <>
          {canReview && item.status !== 'approved' && (
            <button type="button" className="btn btn-sm theme-bg text-white" disabled={busy} onClick={() => review('approve')}>
              <i className="fa fa-check mr-1" aria-hidden="true" /> Approve
            </button>
          )}
          {canReview && item.status !== 'rejected' && (
            <button type="button" className="btn btn-sm btn-outline-danger" disabled={busy} onClick={() => setRejecting(true)}>
              <i className="fa fa-xmark mr-1" aria-hidden="true" /> Reject
            </button>
          )}
          {canEdit && (
            <>
              <button type="button" className="btn btn-sm btn-outline-secondary" disabled={busy}
                onClick={() => toggle('is_featured', !item.featured, item.featured ? 'Removed from featured.' : 'Marked as featured.')}>
                <i className={`fa ${item.featured ? 'fa-star' : 'fa-star-half-stroke'} mr-1`} aria-hidden="true" /> {item.featured ? 'Unfeature' : 'Feature'}
              </button>
              <button type="button" className="btn btn-sm btn-outline-secondary" disabled={busy}
                onClick={() => toggle('is_active', !item.active, item.active ? 'Resource deactivated.' : 'Resource activated.')}>
                <i className={`fa ${item.active ? 'fa-eye-slash' : 'fa-eye'} mr-1`} aria-hidden="true" /> {item.active ? 'Deactivate' : 'Activate'}
              </button>
              <button type="button" className="btn btn-sm btn-outline-danger" disabled={busy} onClick={remove} aria-label="Delete resource">
                <i className="fa fa-trash" aria-hidden="true" />
              </button>
            </>
          )}
          <a className="btn btn-sm btn-outline-secondary" href={to(item.path)} target="_blank" rel="noopener noreferrer">
            <i className="fa fa-arrow-up-right-from-square mr-1" aria-hidden="true" /> Public page
          </a>
        </>
      )}
    >
      <p className="mb-3">
        <Link to={to('/admin/publications')} className="ft-medium" style={{ fontSize: '0.84rem' }}>
          <i className="fa fa-arrow-left mr-1" aria-hidden="true" /> Back to Manage resources
        </Link>
      </p>
      <Flash message={message} />
      {!item && !message?.error && <AdminCard><Skeleton lines={5} /></AdminCard>}
      {item && (
        <>
          <div className="row">
            <div className="col-md-3 col-6 mb-3"><StatCard value={item.visits} label="Views" icon="fa-eye" /></div>
            <div className="col-md-3 col-6 mb-3"><StatCard value={item.comments.length} label="Comments" icon="fa-comments" /></div>
            <div className="col-md-3 col-6 mb-3">
              <div className="kh-admin-stat">
                <span className="kh-admin-stat-icon"><i className="fa fa-flag" aria-hidden="true" /></span>
                <span className="kh-admin-stat-value" style={{ fontSize: '1.2rem', textTransform: 'capitalize' }}>
                  <StatusBadge tone={STATUS_TONE[item.status]}>{item.status}</StatusBadge>
                </span>
                <span className="kh-admin-stat-label">Moderation status</span>
              </div>
            </div>
            <div className="col-md-3 col-6 mb-3">
              <div className="kh-admin-stat">
                <span className="kh-admin-stat-icon"><i className="fa fa-toggle-on" aria-hidden="true" /></span>
                <span className="kh-admin-stat-value" style={{ fontSize: '1.2rem' }}>
                  <StatusBadge tone={item.active ? 'success' : 'neutral'}>{item.active ? 'Active' : 'Inactive'}</StatusBadge>
                  {item.featured && <StatusBadge tone="warning">Featured</StatusBadge>}
                </span>
                <span className="kh-admin-stat-label">Visibility</span>
              </div>
            </div>
          </div>

          {item.status === 'rejected' && item.rejectedReason && (
            <div className="alert alert-warning"><strong>Rejection reason:</strong> {item.rejectedReason}</div>
          )}

          <div className="row">
            <div className="col-lg-8">
              <ResourceCard item={item} />
              <AttachmentsCard item={item} />
              <CommentsCard item={item} canReview={canReview} busy={busy} onReviewComment={reviewComment} />
            </div>

            <div className="col-lg-4">
              {item.submittedBy && (
                <AdminCard title="Submitted by">
                  <UserChip photo={item.submittedBy.photo} name={item.submittedBy.name} sub={item.submittedBy.email} size={40} />
                </AdminCard>
              )}
              {item.versions?.length > 1 && (
                <AdminCard title={`Versions (${item.versions.length})`}>
                  <ul className="list-unstyled mb-0" style={{ lineHeight: 1.9 }}>
                    {item.versions.map((version) => (
                      <li key={version.id} className="d-flex align-items-center" style={{ gap: 8 }}>
                        <StatusBadge tone={STATUS_TONE[version.status] ?? 'neutral'}>{version.isOriginal ? 'Original' : `v${version.versionNo}`}</StatusBadge>
                        {version.current
                          ? <span className="ft-medium" style={{ color: '#18181b' }}>{version.title}</span>
                          : <Link to={to(`/admin/publications/details?id=${version.id}`)}>{version.title}</Link>}
                      </li>
                    ))}
                  </ul>
                </AdminCard>
              )}
              <AdminCard title="Classification">
                <ul className="list-unstyled mb-0" style={{ lineHeight: 2 }}>
                  {item.theme?.name && <li><strong>Theme:</strong> {item.theme.name}</li>}
                  {item.subTheme?.name && <li><strong>Sub theme:</strong> {item.subTheme.name}</li>}
                  {item.dataCategory && <li><strong>Category:</strong> {item.dataCategory}</li>}
                  {item.fileType?.name && <li><strong>File type:</strong> {item.fileType.name}</li>}
                  {item.license && <li><strong>Licence:</strong> {item.license.name}</li>}
                  {item.countries.length > 0 && <li><strong>Countries:</strong> {item.countries.map((country) => country.name).join(', ')}</li>}
                  {item.adminOnly && <li><StatusBadge tone="warning">Admin-only access</StatusBadge></li>}
                </ul>
              </AdminCard>
              {(item.citation.doi || item.citation.publisher || item.citation.authors || item.citation.journal) && (
                <AdminCard title="Citation">
                  <ul className="list-unstyled mb-0" style={{ lineHeight: 2 }}>
                    {item.citation.authors && <li><strong>Authors:</strong> {item.citation.authors}</li>}
                    {item.citation.publisher && <li><strong>Publisher:</strong> {item.citation.publisher}</li>}
                    {item.citation.doi && <li><strong>DOI:</strong> {item.citation.doi}</li>}
                    {item.citation.issn && <li><strong>ISSN:</strong> {item.citation.issn}</li>}
                    {item.citation.journal && <li><strong>Journal:</strong> {item.citation.journal.name} {item.citation.journal.volume && `vol. ${item.citation.journal.volume}`}</li>}
                  </ul>
                </AdminCard>
              )}
            </div>
          </div>
        </>
      )}
      {rejecting && (
        <RejectModal onClose={() => setRejecting(false)} onReject={async (reason) => { setRejecting(false); await review('reject', reason); }} />
      )}
    </AdminShell>
  );
};
