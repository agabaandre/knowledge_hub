import { useState } from 'react';
import { AdminCard } from './AdminShell.jsx';
import { Modal, FormField, StatusBadge, CoverImage } from '../../core/ui.jsx';
import { Avatar } from '../../core/Avatar.jsx';
import { formatDate } from '../../core/format.js';

/**
 * Shared presentation for one publication inside the admin — used by the
 * details page AND the Manage Resources preview modal, so the two can never
 * drift apart. All moderation-aware: comments carry their status and pending
 * ones can be approved/rejected in place.
 */

export const ResourceCard = ({ item, title = 'Resource' }) => (
  <AdminCard title={title}>
    <div className="d-flex" style={{ gap: 18, flexWrap: 'wrap' }}>
      <CoverImage src={item.cover} style={{ width: 130, height: 170, objectFit: 'contain', background: '#fafafa', border: '1px solid #e4e4e7', borderRadius: 6, flexShrink: 0 }} />
      <div style={{ flex: 1, minWidth: 240 }}>
        <p className="mb-2">
          {item.author?.name && <span className="mr-3"><i className="fa fa-bank mr-1 theme-cl" aria-hidden="true" />{item.author.name}</span>}
          {item.yearPublished && <span className="mr-3"><i className="fa fa-calendar mr-1 theme-cl" aria-hidden="true" />{item.yearPublished}</span>}
          {item.dataCategory && <span className="mr-3"><i className="fa fa-folder mr-1 theme-cl" aria-hidden="true" />{item.dataCategory}</span>}
          {item.theme?.name && <span><i className="fa fa-tags mr-1 theme-cl" aria-hidden="true" />{item.theme.name}{item.subTheme?.name ? ` · ${item.subTheme.name}` : ''}</span>}
        </p>
        {item.tags.length > 0 && (
          <p className="mb-2">{item.tags.map((tag) => <StatusBadge key={tag.id} tone="neutral">{tag.name}</StatusBadge>)}</p>
        )}
        {item.countries.length > 0 && (
          <p className="mb-2 text-muted" style={{ fontSize: '0.84rem' }}>
            <i className="fa fa-earth-africa mr-1" aria-hidden="true" /> {item.countries.map((country) => country.name).join(', ')}
          </p>
        )}
        {(item.file || item.link) && (
          <a className="btn btn-sm theme-bg text-white" href={item.file || item.link} target="_blank" rel="noopener noreferrer">
            <i className="fa fa-file-arrow-down mr-1" aria-hidden="true" /> Open file / link
          </a>
        )}
      </div>
    </div>
    {item.description
      ? <div className="mt-3" style={{ textAlign: 'justify' }} dangerouslySetInnerHTML={{ __html: item.description }} />
      : <p className="mt-3 mb-0 text-muted">No description was provided.</p>}
  </AdminCard>
);

export const AttachmentsCard = ({ item }) => (
  item.attachments.length === 0 ? null : (
    <AdminCard title={`Attachments (${item.attachments.length})`}>
      <ul className="list-unstyled mb-0">
        {item.attachments.map((file) => (
          <li key={file.id} className="mb-1">
            <a href={file.url} target="_blank" rel="noopener noreferrer"><i className="fa fa-paperclip mr-1" aria-hidden="true" />{file.name}</a>
          </li>
        ))}
      </ul>
    </AdminCard>
  )
);

/** Order comments so each reply follows its parent (one-level threads). */
export const threadedComments = (comments) => {
  const roots = comments.filter((comment) => !comment.parentId);
  const replies = comments.filter((comment) => comment.parentId);
  const ordered = [];
  for (const root of roots) {
    ordered.push(root);
    ordered.push(...replies.filter((reply) => reply.parentId === root.id));
  }
  ordered.push(...replies.filter((reply) => !roots.some((root) => root.id === reply.parentId)));
  return ordered;
};

export const CommentsCard = ({ item, canReview, busy, onReviewComment }) => (
  <AdminCard title={`Comments (${item.comments.length})`}>
    {item.comments.length === 0 && <p className="text-muted mb-0">No comments yet.</p>}
    {threadedComments(item.comments).map((comment) => (
      <div key={comment.id} className="d-flex mb-3" style={{ gap: 10, marginInlineStart: comment.parentId ? 44 : 0 }}>
        {comment.parentId && <i className="fa fa-reply fa-flip-horizontal text-muted" style={{ marginTop: 10 }} aria-hidden="true" />}
        <Avatar photo={comment.user.photo} name={comment.user.name} size={comment.parentId ? 28 : 34} />
        <div style={{ flex: 1 }}>
          <p className="mb-1">
            <span className="ft-medium" style={{ color: '#18181b' }}>{comment.user.name}</span>
            <small className="text-muted ml-2">{formatDate(comment.createdAt)}</small>
            <StatusBadge tone={comment.status === 'approved' ? 'success' : comment.status === 'rejected' ? 'danger' : 'warning'}>{comment.status}</StatusBadge>
          </p>
          <p className="mb-1">{comment.comment}</p>
          {canReview && comment.status === 'pending' && (
            <span className="d-inline-flex" style={{ gap: 6 }}>
              <button type="button" className="btn btn-sm theme-bg text-white" disabled={busy} onClick={() => onReviewComment(comment.id, 'approve')}>Approve</button>
              <button type="button" className="btn btn-sm btn-outline-danger" disabled={busy} onClick={() => onReviewComment(comment.id, 'reject')}>Reject</button>
            </span>
          )}
        </div>
      </div>
    ))}
  </AdminCard>
);

/** Individual rejection form: a reason the submitter will see. */
export const RejectModal = ({ onClose, onReject }) => {
  const [reason, setReason] = useState('');
  return (
    <Modal title="Reject this resource" icon="fa-ban" onClose={onClose}
      footer={(
        <>
          <button type="button" className="btn btn-sm btn-outline-secondary" onClick={onClose}>Cancel</button>
          <button type="button" className="btn btn-sm btn-outline-danger" disabled={!reason.trim()} onClick={() => onReject(reason.trim())}>Reject resource</button>
        </>
      )}>
      <FormField label="Reason" required hint="Shown to the submitter so they can revise and resubmit.">
        <textarea className="form-control" rows={4} value={reason} autoFocus onChange={(event) => setReason(event.target.value)} />
      </FormField>
    </Modal>
  );
};
