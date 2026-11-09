import { useEffect, useState } from 'react';
import { apiFetch } from '../../core/api.js';
import { AdminShell } from './AdminShell.jsx';
import { DataTable } from '../../core/table.jsx';
import { Modal, FormField, StatusBadge, Flash } from '../../core/ui.jsx';

const BLANK = { name: '', slug: '', description: '', threshold: 1, color: '#C0C0C0', isActive: true };

const BadgePreview = ({ tier }) => (
  <span className="d-inline-flex align-items-center ft-medium rounded" style={{ gap: 5, background: tier.color, color: '#1e293b', padding: '4px 10px', fontSize: '0.8rem' }}>
    <i className="fa fa-gem" aria-hidden="true" /> {tier.name || 'Preview'}
  </span>
);

/** Contributor badge tiers — listed in a table, edited one tier at a time in a modal. */
export const BadgesPage = () => {
  const [tiers, setTiers] = useState(null);
  const [editing, setEditing] = useState(null); // null | {} (add) | tier (edit)
  const [message, setMessage] = useState(null);

  useEffect(() => {
    apiFetch('/api/admin/badges').then((payload) => setTiers(payload.tiers)).catch((err) => setMessage({ error: err.message }));
  }, []);

  /** The API replaces the whole set, so every change saves the updated array. */
  const persist = async (nextTiers, ok) => {
    const payload = await apiFetch('/api/admin/badges', {
      method: 'PUT',
      body: { tiers: nextTiers.map((tier) => ({ ...tier, threshold: Number(tier.threshold) })) },
    });
    setTiers(payload.tiers);
    setMessage({ ok });
  };

  const remove = async (tier, index) => {
    if (!window.confirm(`Delete the "${tier.name}" tier?`)) return;
    try {
      await persist(tiers.filter((_, i) => i !== index), `"${tier.name}" removed.`);
    } catch (err) {
      setMessage({ error: err.message });
    }
  };

  const columns = [
    { key: 'badge', label: 'Badge', render: (tier) => <BadgePreview tier={tier} /> },
    { key: 'slug', label: 'Slug', render: (tier) => <code style={{ fontSize: '0.78rem' }}>{tier.slug}</code> },
    { key: 'threshold', label: 'Threshold', align: 'center', render: (tier) => <strong style={{ color: '#18181b' }}>{tier.threshold}</strong> },
    {
      key: 'color',
      label: 'Colour',
      render: (tier) => (
        <span className="d-inline-flex align-items-center" style={{ gap: 7 }}>
          <span style={{ width: 16, height: 16, borderRadius: 4, background: tier.color, border: '1px solid rgba(0,0,0,0.12)' }} aria-hidden="true" />
          <span className="text-muted" style={{ fontSize: '0.8rem' }}>{tier.color}</span>
        </span>
      ),
    },
    { key: 'status', label: 'Status', align: 'center', render: (tier) => <StatusBadge tone={tier.isActive ? 'success' : 'neutral'}>{tier.isActive ? 'Active' : 'Inactive'}</StatusBadge> },
    {
      key: 'actions',
      label: 'Actions',
      align: 'end',
      render: (tier, index) => (
        <span className="kh-table-actions">
          <button type="button" className="btn btn-sm btn-outline-secondary" onClick={() => setEditing({ tier, index })} aria-label={`Edit ${tier.name}`}>
            <i className="fa fa-pen" aria-hidden="true" /> Edit
          </button>
          <button type="button" className="btn btn-sm btn-outline-danger" onClick={() => remove(tier, index)} aria-label={`Delete ${tier.name}`}>
            <i className="fa fa-trash" aria-hidden="true" />
          </button>
        </span>
      ),
    },
  ];

  return (
    <AdminShell title="Contributor badges"
      subtitle="A contributor earns the highest tier whose threshold their lifetime contributions have reached.">
      <Flash message={message} />
      {!tiers && !message?.error && <p role="status">Loading…</p>}
      {tiers && (
        <DataTable
          title="Badge tiers"
          description="Lifetime contributions = approved publications + forum threads + comments."
          columns={columns}
          rows={tiers}
          rowKey={(tier, index) => tier.id ?? `new-${index}`}
          showRowNumbers
          emptyMessage="No tiers configured yet."
          toolbar={(
            <button type="button" className="btn btn-sm theme-bg text-white" onClick={() => setEditing({ tier: null })}>
              <i className="fa fa-plus mr-1" aria-hidden="true" /> Add tier
            </button>
          )}
        />
      )}
      {editing && (
        <TierModal
          tier={editing.tier}
          onClose={() => setEditing(null)}
          onSave={async (draft) => {
            const next = editing.tier
              ? tiers.map((tier, index) => (index === editing.index ? { ...tier, ...draft } : tier))
              : [...tiers, draft];
            await persist(next, `"${draft.name}" ${editing.tier ? 'updated' : 'added'}. Contributor cards update immediately.`);
            setEditing(null);
          }}
        />
      )}
    </AdminShell>
  );
};

/** Individual add/edit form for one badge tier. */
const TierModal = ({ tier, onClose, onSave }) => {
  const [draft, setDraft] = useState(tier ?? { ...BLANK });
  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);
  const isEdit = Boolean(tier);
  const set = (key, value) => setDraft((previous) => ({ ...previous, [key]: value }));

  const save = async () => {
    setBusy(true);
    setError('');
    try {
      await onSave(draft);
    } catch (err) {
      setError(err.details ? err.details.map((d) => `${d.path}: ${d.message}`).join('; ') : err.message);
      setBusy(false);
    }
  };

  return (
    <Modal
      title={isEdit ? `Edit ${tier.name}` : 'Add badge tier'}
      icon="fa-medal"
      onClose={onClose}
      footer={(
        <>
          <button type="button" className="btn btn-sm btn-outline-secondary" onClick={onClose}>Cancel</button>
          <button type="button" className="btn btn-sm theme-bg text-white" disabled={busy || !draft.name.trim() || !draft.slug.trim()} onClick={save}>
            {busy ? 'Saving…' : isEdit ? 'Save changes' : 'Add tier'}
          </button>
        </>
      )}
    >
      {error && <div role="alert" className="alert alert-danger">{error}</div>}
      <div className="mb-3"><BadgePreview tier={draft} /></div>
      <div className="row">
        <div className="col-md-7"><FormField label="Name" required>
          <input className="form-control" value={draft.name} autoFocus onChange={(event) => set('name', event.target.value)} />
        </FormField></div>
        <div className="col-md-5"><FormField label="Slug" required>
          <input className="form-control" value={draft.slug} onChange={(event) => set('slug', event.target.value)} />
        </FormField></div>
      </div>
      <div className="row">
        <div className="col-md-6"><FormField label="Threshold" required hint="Contributions needed to earn this tier.">
          <input type="number" min={1} className="form-control" value={draft.threshold} onChange={(event) => set('threshold', event.target.value)} />
        </FormField></div>
        <div className="col-md-6"><FormField label="Colour">
          <input type="color" className="form-control" style={{ height: 40, padding: 4 }} value={draft.color} onChange={(event) => set('color', event.target.value)} />
        </FormField></div>
      </div>
      <label className="mb-0 d-flex align-items-center" style={{ gap: 8, cursor: 'pointer', fontWeight: 500 }}>
        <input type="checkbox" checked={draft.isActive} onChange={(event) => set('isActive', event.target.checked)} /> Tier is active
      </label>
    </Modal>
  );
};
