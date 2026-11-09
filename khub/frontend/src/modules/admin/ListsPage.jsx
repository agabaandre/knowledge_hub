import { useEffect, useMemo, useState } from 'react';
import { apiFetch } from '../../core/api.js';
import { usePermission } from '../../core/site-context.jsx';
import { AdminShell } from './AdminShell.jsx';
import { SearchSelect } from '../../core/SearchSelect.jsx';
import { DataTable } from '../../core/table.jsx';
import { Modal, FormField, StatusBadge, Flash } from '../../core/ui.jsx';

/** Dropdown Lists: taxonomy tables, edited through modal forms (no inline editing). */
export const ListsPage = () => {
  const canEdit = usePermission('admin.lists.edit');
  const [lists, setLists] = useState(null);
  const [active, setActive] = useState(null);
  const [items, setItems] = useState(null);
  const [parents, setParents] = useState([]);
  const [editing, setEditing] = useState(null); // null | {} (add) | item (edit)
  const [term, setTerm] = useState('');
  const [message, setMessage] = useState(null);

  useEffect(() => {
    apiFetch('/api/admin/lists').then((payload) => {
      setLists(payload.lists);
      setActive(payload.lists[0]);
    }).catch((err) => setMessage({ error: err.message }));
  }, []);

  const loadItems = async (list) => {
    setItems(null);
    const payload = await apiFetch(`/api/admin/lists/${list.key}`);
    setItems(payload.items);
    if (list.parentList) {
      const parentPayload = await apiFetch(`/api/admin/lists/${list.parentList}`);
      setParents(parentPayload.items);
    } else setParents([]);
  };
  useEffect(() => { if (active) loadItems(active).catch((err) => setMessage({ error: err.message })); }, [active]); // eslint-disable-line

  const refresh = () => loadItems(active).catch((err) => setMessage({ error: err.message }));
  const parentName = (id) => parents.find((parent) => parent.id === id)?.name ?? '—';

  const remove = async (item) => {
    if (!window.confirm(`Delete "${item.name}"?`)) return;
    try {
      await apiFetch(`/api/admin/lists/${active.key}/${item.id}`, { method: 'DELETE' });
      setMessage({ ok: `"${item.name}" deleted.` });
      await refresh();
    } catch (err) {
      setMessage({ error: err.message });
    }
  };

  const filtered = useMemo(() => {
    if (!items) return items;
    const clean = term.trim().toLowerCase();
    if (!clean) return items;
    return items.filter((item) =>
      `${item.name} ${item.extra ?? ''} ${item.desc ?? ''} ${parentName(item.parentId ?? -1)}`.toLowerCase().includes(clean));
  }, [items, term, parents]); // eslint-disable-line

  const columns = active ? [
    { key: 'name', label: active.key === 'faqs' ? 'Question' : 'Name', render: (item) => <span className="ft-medium" style={{ color: '#18181b' }}>{item.name}</span> },
    ...(active.hasExtra ? [{
      key: 'extra',
      label: active.extraLabel,
      render: (item) => <span className="text-muted" style={{ display: 'inline-block', maxWidth: 420, overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap', verticalAlign: 'bottom' }}>{item.extra || '—'}</span>,
    }] : []),
    ...(active.hasDesc ? [{
      key: 'desc',
      label: active.descLabel,
      render: (item) => <span className="text-muted" style={{ display: 'inline-block', maxWidth: 360, overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap', verticalAlign: 'bottom' }}>{item.desc || '—'}</span>,
    }] : []),
    ...(active.parentList ? [{ key: 'parent', label: 'Parent', render: (item) => parentName(item.parentId) }] : []),
    ...(active.hasFlag ? [{
      key: 'flag',
      label: active.flagLabel,
      align: 'center',
      render: (item) => <StatusBadge tone={item.flag ? 'success' : 'neutral'}>{item.flag ? 'Yes' : 'No'}</StatusBadge>,
    }] : []),
    ...(canEdit ? [{
      key: 'actions',
      label: 'Actions',
      align: 'end',
      render: (item) => (
        <span className="kh-table-actions">
          <button type="button" className="btn btn-sm btn-outline-secondary" onClick={() => setEditing(item)} aria-label={`Edit ${item.name}`}>
            <i className="fa fa-pen" aria-hidden="true" /> Edit
          </button>
          <button type="button" className="btn btn-sm btn-outline-danger" onClick={() => remove(item)} aria-label={`Delete ${item.name}`}>
            <i className="fa fa-trash" aria-hidden="true" />
          </button>
        </span>
      ),
    }] : []),
  ] : [];

  return (
    <AdminShell title="Dropdown lists" subtitle="The taxonomy tables behind every select on the portal.">
      <Flash message={message} />
      {!lists && !message?.error && <p role="status">Loading…</p>}
      {lists && (
        <div className="row">
          <div className="col-lg-3 col-md-4 mb-3">
            <div className="kh-admin-card" style={{ padding: 8 }}>
              {lists.map((list) => (
                <button key={list.key} type="button" onClick={() => { setActive(list); setTerm(''); setMessage(null); }}
                  className={`btn w-100 text-left mb-1 ${active?.key === list.key ? 'theme-bg text-white' : 'btn-light'}`}>
                  <span className="ft-medium">{list.label}</span>
                  <small className={`d-block ${active?.key === list.key ? 'text-white-50' : 'text-muted'}`}>{list.count} item(s)</small>
                </button>
              ))}
            </div>
          </div>
          <div className="col-lg-9 col-md-8">
            {active && items && (
              <DataTable
                title={active.label}
                description={`Values offered wherever the portal asks for ${active.label.toLowerCase()}.`}
                columns={columns}
                rows={filtered}
                showRowNumbers
                clientPerPage={12}
                emptyMessage={term ? `No ${active.label.toLowerCase()} match “${term}”.` : `No ${active.label.toLowerCase()} yet.`}
                toolbar={(
                  <>
                    <input className="form-control form-control-sm" style={{ width: 190 }} type="search"
                      placeholder={`Search ${active.label.toLowerCase()}…`} value={term}
                      onChange={(event) => setTerm(event.target.value)} aria-label={`Search ${active.label}`} />
                    <a className="btn btn-sm btn-outline-secondary"
                      href={`/api/admin/lists/${active.key}/export${term.trim() ? `?term=${encodeURIComponent(term.trim())}` : ''}`}
                      download title="Download this list as CSV">
                      <i className="fa fa-file-csv mr-1" aria-hidden="true" /> Export
                    </a>
                    {canEdit && (
                      <button type="button" className="btn btn-sm theme-bg text-white" onClick={() => setEditing({})}>
                        <i className="fa fa-plus mr-1" aria-hidden="true" /> Add {active.label.replace(/s$/, '')}
                      </button>
                    )}
                  </>
                )}
              />
            )}
            {active && !items && <p role="status">Loading…</p>}
          </div>
        </div>
      )}
      {editing !== null && active && (
        <ListItemModal
          list={active}
          item={editing.id ? editing : null}
          parents={parents}
          onClose={() => setEditing(null)}
          onSaved={async (saved) => {
            setEditing(null);
            setMessage({ ok: saved });
            await refresh();
            const payload = await apiFetch('/api/admin/lists').catch(() => null);
            if (payload) setLists(payload.lists);
          }}
        />
      )}
    </AdminShell>
  );
};

/** One add/edit form per list entry, in a dialog. */
const ListItemModal = ({ list, item, parents, onClose, onSaved }) => {
  const [draft, setDraft] = useState({
    name: item?.name ?? '',
    extra: item?.extra ?? '',
    desc: item?.desc ?? '',
    flag: item?.flag ?? false,
    parent_id: item?.parentId ?? null,
  });
  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);
  const isEdit = Boolean(item);

  const save = async () => {
    setBusy(true);
    setError('');
    try {
      const body = { name: draft.name, extra: draft.extra, desc: list.hasDesc ? draft.desc : undefined, flag: list.hasFlag ? draft.flag : undefined, parent_id: draft.parent_id ?? undefined };
      if (isEdit) await apiFetch(`/api/admin/lists/${list.key}/${item.id}`, { method: 'PUT', body });
      else await apiFetch(`/api/admin/lists/${list.key}`, { method: 'POST', body });
      onSaved(`"${draft.name}" ${isEdit ? 'updated' : 'added'}.`);
    } catch (err) {
      setError(err.details ? err.details.map((detail) => detail.message).join(' ') : err.message);
      setBusy(false);
    }
  };

  return (
    <Modal
      title={`${isEdit ? 'Edit' : 'Add'} ${list.label.replace(/s$/, '')}`}
      icon={isEdit ? 'fa-pen' : 'fa-plus'}
      onClose={onClose}
      footer={(
        <>
          <button type="button" className="btn btn-sm btn-outline-secondary" onClick={onClose}>Cancel</button>
          <button type="button" className="btn btn-sm theme-bg text-white" disabled={busy || !draft.name.trim()} onClick={save}>
            {busy ? 'Saving…' : isEdit ? 'Save changes' : 'Add entry'}
          </button>
        </>
      )}
    >
      {error && <div role="alert" className="alert alert-danger">{error}</div>}
      <FormField label={list.key === 'faqs' ? 'Question' : 'Name'} required>
        <input className="form-control" value={draft.name} autoFocus
          onChange={(event) => setDraft({ ...draft, name: event.target.value })} />
      </FormField>
      {list.hasExtra && (
        <FormField label={list.extraLabel} required={Boolean(list.requireExtra)}>
          {list.extraType === 'textarea'
            ? <textarea className="form-control" rows={5} value={draft.extra} onChange={(event) => setDraft({ ...draft, extra: event.target.value })} />
            : <input className="form-control" value={draft.extra} onChange={(event) => setDraft({ ...draft, extra: event.target.value })} />}
        </FormField>
      )}
      {list.hasDesc && (
        <FormField label={list.descLabel}>
          <textarea className="form-control" rows={3} value={draft.desc} onChange={(event) => setDraft({ ...draft, desc: event.target.value })} />
        </FormField>
      )}
      {list.parentList && (
        <FormField label="Parent" required>
          <SearchSelect options={parents} value={draft.parent_id ?? ''} placeholder="Select parent"
            onChange={(next) => setDraft({ ...draft, parent_id: next ? Number(next) : null })} />
        </FormField>
      )}
      {list.hasFlag && (
        <label className="mb-0 d-flex align-items-center" style={{ gap: 8, cursor: 'pointer', fontWeight: 500 }}>
          <input type="checkbox" checked={draft.flag} onChange={(event) => setDraft({ ...draft, flag: event.target.checked })} /> {list.flagLabel}
        </label>
      )}
    </Modal>
  );
};
