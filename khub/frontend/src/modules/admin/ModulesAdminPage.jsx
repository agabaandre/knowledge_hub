import { useEffect, useState } from 'react';
import { Seo } from '../../core/Seo.jsx';
import { apiFetch } from '../../core/api.js';
import { useSite } from '../../core/site-context.jsx';
import { AdminShell } from './AdminShell.jsx';

/** Switch backend modules on/off for this site. Core modules are locked on. */
export const ModulesAdminPage = () => {
  const { refresh } = useSite();
  const [modules, setModules] = useState(null);
  const [busy, setBusy] = useState(null);
  const [error, setError] = useState('');

  useEffect(() => {
    apiFetch('/api/admin/modules').then((payload) => setModules(payload.modules)).catch((err) => setError(err.message));
  }, []);

  const toggle = async (module) => {
    setBusy(module.name);
    setError('');
    try {
      const payload = await apiFetch(`/api/admin/modules/${module.name}`, { method: 'PUT', body: { enabled: !module.enabled } });
      setModules(payload.modules);
      await refresh();
    } catch (err) {
      setError(err.message);
    } finally {
      setBusy(null);
    }
  };

  return (
    <AdminShell title="Modules">
      <Seo title="Modules" path="/admin/modules" noindex />
      <p className="text-muted">Turn features on or off for this site. Disabled modules disappear from navigation and their API returns 404.</p>
      {error && <div role="alert" className="alert alert-danger">{error}</div>}
      {!modules && !error && <p role="status">Loading…</p>}
      <ul className="list-group">
        {modules?.map((module) => (
          <li key={module.name} className="list-group-item d-flex justify-content-between align-items-center gap-3">
            <div>
              <div className="fw-semibold">{module.label}{module.core && <span className="badge bg-secondary ms-2">core</span>}{!module.implemented && <span className="badge bg-warning text-dark ms-2">not yet ported</span>}</div>
              <div className="small text-muted">{module.description}</div>
            </div>
            <label className="kh-switch m-0">
              <input
                type="checkbox"
                role="switch"
                checked={module.enabled}
                disabled={module.core || busy === module.name}
                onChange={() => toggle(module)}
                aria-label={`${module.enabled ? 'Disable' : 'Enable'} ${module.label}`}
              />
              <span className="kh-slider" />
            </label>
          </li>
        ))}
      </ul>
    </AdminShell>
  );
};
