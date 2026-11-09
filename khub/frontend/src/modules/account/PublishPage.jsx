import { useEffect, useMemo, useState } from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import { PUBLISH_WIZARD, validatePublishSubmission } from '@khub/shared/publish-wizard';
import { useApi } from '../../core/hooks.js';
import { apiFetch } from '../../core/api.js';
import { useSite, useModule } from '../../core/site-context.jsx';
import { useI18n } from '../../core/i18n.jsx';
import { withSitePrefix } from '@khub/shared/urls';
import { Seo } from '../../core/Seo.jsx';
import { Loading } from '../../core/Page.jsx';
import { WizardField } from './PublishWizardFields.jsx';

const DEFAULTS = { upload_type: 'attachment', show_disclaimer: true, tags: [], countries: [], rccs: [] };

/** SmartWizard-style step header: numbered circles joined by a line. */
const Stepper = ({ active }) => (
  <div className="d-flex justify-content-center align-items-start position-relative my-4" style={{ gap: '28%' }}>
    <div className="position-absolute" style={{ top: 17, left: '15%', right: '15%', height: 3, background: '#e2e8f0' }} aria-hidden="true" />
    {PUBLISH_WIZARD.steps.map((step, index) => {
      const state = index === active ? 'active' : index < active ? 'done' : 'todo';
      const color = state === 'todo' ? '#9aa4b2' : 'var(--theme-color-primary, #006239)';
      return (
        <div key={step.id} className="text-center position-relative" style={{ zIndex: 1 }}>
          <span className="d-inline-flex align-items-center justify-content-center rounded-circle" aria-hidden="true"
            style={{ width: 36, height: 36, background: state === 'todo' ? '#cbd5e1' : 'var(--theme-color-primary, #006239)', color: '#fff', border: '4px solid #fff', boxShadow: '0 0 0 1px #e2e8f0' }}>
            {state === 'done' ? <i className="fa fa-check" /> : index + 1}
          </span>
          <p className="ft-medium mb-0 mt-1" style={{ color }}>{step.label}</p>
          <p className="mb-0" style={{ color: state === 'todo' ? '#9aa4b2' : '#475569' }}>{step.title}</p>
        </div>
      );
    })}
  </div>
);

/**
 * Publish a resource — the two-step wizard from the original portal. The step
 * and field definitions come from @khub/shared/publish-wizard; the backend
 * validates submissions against the identical rules.
 */
export const PublishPage = () => {
  const { manifest } = useSite();
  const lookups = useApi('/api/publications/lookups');
  const config = useApi('/api/account/publish-config');
  const [active, setActive] = useState(0);
  const [data, setData] = useState(DEFAULTS);
  const [stepErrors, setStepErrors] = useState({});
  const [touchedSubmit, setTouchedSubmit] = useState(false);
  const [message, setMessage] = useState(null);
  const [busy, setBusy] = useState(false);
  const [aiState, setAiState] = useState({ busy: false, error: '' });
  const [searchParams] = useSearchParams();
  const versionOf = Number(searchParams.get('version_of')) || null;
  const [parent, setParent] = useState(null);
  const aiEnabled = useModule('ai-search');
  const { locale } = useI18n();
  const to = (path) => withSitePrefix(manifest.site.pathPrefix, path);
  const isAdmin = Boolean(config.data?.isAdmin);

  useEffect(() => { window.scrollTo(0, 0); }, [active]);

  const optionsFor = useMemo(() => {
    const themes = lookups.data?.themes ?? [];
    const regionsAll = (data.rccs ?? []).map(String);
    const selectedRegions = regionsAll.filter((value) => value !== 'all').map(Number);
    return {
      dataCategories: lookups.data?.dataCategories ?? [],
      fileCategories: lookups.data?.fileCategories ?? [],
      themes: themes.map((theme) => ({ id: theme.id, name: theme.name })),
      subThemes: (themes.find((theme) => String(theme.id) === String(data.theme))?.subThemes ?? themes.flatMap((theme) => theme.subThemes)).map((sub) => ({ id: sub.id, name: sub.name })),
      regions: config.data?.regions ?? [],
      countries: (lookups.data?.countries ?? []).filter((country) => selectedRegions.length === 0 || selectedRegions.includes(country.region_id)).map((country) => ({ id: country.id, name: country.name })),
      tags: config.data?.tags ?? [],
      licenses: config.data?.licenses ?? [],
    };
  }, [lookups.data, config.data, data.theme, data.rccs]);

  const validation = useMemo(
    () => validatePublishSubmission(data, { requiredFields: config.data?.requiredFields, minWords: config.data?.minWords, isAdmin, hasFile: Boolean(data.file) }),
    [data, config.data, isAdmin],
  );

  const requiredOf = (field) => field.required === 'always' || (field.requiredKey ? Boolean(config.data?.requiredFields?.[field.requiredKey]) : false);
  const set = (name, value) => setData((previous) => ({ ...previous, [name]: value }));

  // Submitting a new version: inherit the original's classification (Laravel model).
  useEffect(() => {
    if (!versionOf) return;
    apiFetch(`/api/publications/${versionOf}`).then((payload) => {
      const original = payload.data;
      setParent(original);
      setData((previous) => ({
        ...previous,
        title: original.title,
        theme: original.theme?.id ?? previous.theme,
        sub_theme: original.subTheme?.id ?? previous.sub_theme,
        countries: original.countries.map((country) => country.id),
        tags: original.tags.map((tag) => tag.id),
      }));
    }).catch(() => setMessage({ error: 'The original resource could not be loaded.' }));
  }, [versionOf]); // eslint-disable-line

  const step = PUBLISH_WIZARD.steps[active];
  const visibleErrors = stepErrors[step.id] ?? [];
  const fieldError = (name) => ((stepErrors[step.id]?.length || touchedSubmit) ? validation.errors[name] : undefined);

  const tryAdvance = () => {
    const errors = validation.steps[step.id];
    if (errors.length > 0) {
      setStepErrors((previous) => ({ ...previous, [step.id]: errors }));
      return;
    }
    setStepErrors((previous) => ({ ...previous, [step.id]: [] }));
    setActive((index) => Math.min(index + 1, PUBLISH_WIZARD.steps.length - 1));
  };

  const submit = async () => {
    setTouchedSubmit(true);
    if (!validation.valid) {
      setStepErrors({ start: validation.steps.start, details: validation.steps.details });
      if (validation.steps.start.length > 0) setActive(0);
      return;
    }
    setBusy(true);
    setMessage(null);
    try {
      const body = new FormData();
      if (versionOf) body.set('original_id', String(versionOf));
      for (const [key, value] of Object.entries(data)) {
        if (value === undefined || value === null) continue;
        if (value instanceof File) body.set(key, value);
        else if (Array.isArray(value)) body.set(key, JSON.stringify(value));
        else body.set(key, String(value));
      }
      const response = await fetch('/api/account/publish', { method: 'POST', body, credentials: 'same-origin' });
      const payload = await response.json().catch(() => ({}));
      if (!response.ok) throw new Error(payload.details ? payload.details.map((detail) => detail.message).join(' ') : payload.error ?? `Failed (${response.status})`);
      setMessage({ ok: payload.message });
      setData(DEFAULTS);
      setActive(0);
      setTouchedSubmit(false);
      setStepErrors({});
    } catch (err) {
      setMessage({ error: err.message });
    } finally {
      setBusy(false);
    }
  };

  // AI-assisted description draft (ai-search module, provider-agnostic client).
  const draftDescription = async () => {
    setAiState({ busy: true, error: '' });
    try {
      const payload = await apiFetch('/api/ai-search/draft-description', {
        method: 'POST',
        body: { title: data.title, link: data.link || undefined, language: locale },
      });
      set('description', payload.draft);
      setAiState({ busy: false, error: '' });
    } catch (err) {
      setAiState({ busy: false, error: err.message });
    }
  };

  const assistFor = (field) => {
    if (field.name !== 'description' || !aiEnabled) return undefined;
    const ready = String(data.title ?? '').trim().length >= 5;
    return (
      <span className="d-inline-flex align-items-center" style={{ gap: 8 }}>
        {aiState.error && <small className="text-danger">{aiState.error}</small>}
        <button type="button" className="btn btn-sm btn-outline-success bg-white" disabled={aiState.busy || !ready}
          title={ready ? 'Draft a description from the title with AI' : 'Enter a title first'} onClick={draftDescription}>
          <i className={`fa ${aiState.busy ? 'fa-spinner fa-spin' : 'fa-wand-magic-sparkles'} mr-1`} aria-hidden="true" />
          {aiState.busy ? 'Drafting…' : 'AI draft'}
        </button>
      </span>
    );
  };

  const renderField = (field) => {
    if (field.adminOnly && !isAdmin) return null;
    if (field.showIf && !data[field.showIf]) return null;
    if (field.type === 'upload-type' || field.type === 'toggle') return null; // rendered in the toggles row
    const node = (
      <WizardField field={field} value={data[field.name]} error={fieldError(field.name)}
        required={requiredOf(field)} options={optionsFor[field.options] ?? []} onChange={set} assist={assistFor(field)} />
    );
    return field.half ? <div className="col-md-6" key={field.name}>{node}</div> : <div className="col-12" key={field.name}>{node}</div>;
  };

  const toggles = step.fields.filter((field) => field.type === 'toggle' && (!field.adminOnly || isAdmin) && (!field.showIf || data[field.showIf] !== undefined));

  return (
    <div className="gray pb-5">
      <Seo title="Publish a resource" path="/account/publish" noindex />

      {/* Hero band, as on the original wizard */}
      <div className="text-center py-5 mb-3" style={{ background: 'linear-gradient(135deg, rgba(135,200,161,0.45), rgba(35,132,68,0.35))' }}>
        <h1 className="ft-bold text-white mb-1" style={{ fontSize: '2rem', textShadow: '0 1px 3px rgba(0,0,0,0.25)' }}>
          <i className="fa fa-circle-plus mr-2" aria-hidden="true" />Publish a Resource
        </h1>
        <p className="text-white mb-0" style={{ textShadow: '0 1px 3px rgba(0,0,0,0.25)' }}>Share your knowledge and contribute to the {manifest.settings.site_name}</p>
      </div>

      <div className="container">
        {parent && (
          <div className="alert alert-success d-flex align-items-center mt-3 mb-0" style={{ gap: 10 }}>
            <i className="fa fa-code-branch" aria-hidden="true" />
            <span>
              Submitting a <strong>new version</strong> of “{parent.title}”. Classification is inherited from the original — attach the updated file or link.
            </span>
          </div>
        )}
        <Stepper active={active} />
        {(lookups.loading || config.loading) && <Loading />}
        {message?.error && <div role="alert" className="alert alert-danger">{message.error}</div>}
        {message?.ok && (
          <div role="status" className="alert alert-success">
            {message.ok} <Link to={to('/account/publications')} className="theme-cl ft-medium">Track its status</Link>.
          </div>
        )}

        {config.data && (
          <div className="bg-white rounded border p-4" style={{ boxShadow: '0 2px 10px rgba(15,23,42,0.05)' }}>
            {active === 0 && (
              <>
                <div className="alert mb-3" style={{ background: '#e7f1fb', borderLeft: '4px solid #2779bd', color: '#1b4965' }}>
                  <i className="fa fa-circle-info mr-1" aria-hidden="true" /> <strong>Please Note:</strong> all fields marked with a <span className="text-danger">*</span> are
                  <strong> required</strong>. Please ensure you fill in all required fields before proceeding to Step 2.
                </div>
                <div className="rounded border p-3 mb-3" style={{ background: '#f8fafc', borderLeft: '4px solid var(--theme-color-primary, #006239)' }}>
                  <p className="ft-medium mb-1">Documents</p>
                  <p className="mb-2"><strong>PDF is the recommended format</strong> for reports, strategies, manuals, and similar materials. Word, Excel, PowerPoint, and images are also supported.</p>
                  <p className="ft-medium mb-1">Videos (e.g. YouTube, Vimeo)</p>
                  <ol className="mb-0 pl-3">
                    <li>Choose <strong>External Link</strong> — do not use Attachment for a video that is already hosted online.</li>
                    <li>Paste the <strong>full video URL</strong> from your browser's address bar.</li>
                    <li>Turn on <strong>Embedded On Page</strong> so visitors can play the video on the resource page.</li>
                  </ol>
                </div>
                <div className="d-flex flex-wrap align-items-center mb-4" style={{ gap: 18 }}>
                  {['attachment', 'link'].map((type) => (
                    <label key={type} className="mb-0 d-inline-flex align-items-center ft-medium theme-cl" style={{ gap: 6, cursor: 'pointer' }}>
                      <input type="radio" name="upload_type" checked={data.upload_type === type} onChange={() => set('upload_type', type)} />
                      {type === 'attachment' ? 'Attachment' : 'External Link'}
                    </label>
                  ))}
                  {toggles.map((field) => (
                    <span key={field.name} className="theme-cl">
                      <WizardField field={field} value={data[field.name]} onChange={set} />
                    </span>
                  ))}
                </div>
              </>
            )}

            {visibleErrors.length > 0 && (
              <div role="alert" className="alert alert-danger">
                <strong>Please fix the following:</strong>
                <ul className="mb-0 pl-3">{visibleErrors.map((error) => <li key={error}>{error}</li>)}</ul>
              </div>
            )}

            {active === 0 && <h2 className="mb-3" style={{ fontSize: '1.35rem', color: '#4a2c3d' }}>What is the title of the resource you want to publish?</h2>}
            {active === 1 && <h2 className="mb-3" style={{ fontSize: '1.35rem', color: '#4a2c3d' }}>Attachments &amp; Additional Details</h2>}

            <div className="row">{step.fields.map(renderField)}</div>

            <div className="d-flex justify-content-end pt-3 mt-2" style={{ gap: 10, borderTop: '1px solid #e2e8f0' }}>
              {active > 0 && (
                <button type="button" className="btn btn-secondary px-4" onClick={() => setActive((index) => index - 1)} disabled={busy}>Previous</button>
              )}
              {active < PUBLISH_WIZARD.steps.length - 1 ? (
                <button type="button" className="btn theme-bg text-white px-4" onClick={tryAdvance}>Next</button>
              ) : (
                <button type="button" className="btn theme-bg text-white px-4" onClick={submit} disabled={busy}>
                  {busy ? 'Submitting…' : 'Submit for review'}
                </button>
              )}
            </div>
          </div>
        )}
      </div>
    </div>
  );
};
