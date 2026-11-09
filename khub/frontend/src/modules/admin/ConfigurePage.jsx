import { useEffect, useMemo, useState } from 'react';
import { apiFetch } from '../../core/api.js';
import { usePermission } from '../../core/site-context.jsx';
import { AdminShell, AdminCard } from './AdminShell.jsx';
import { SearchSelect } from '../../core/SearchSelect.jsx';
import { Flash, FormField } from '../../core/ui.jsx';
import { Link } from 'react-router-dom';

/**
 * System Configuration — the production tab set. Every tab renders from one
 * field catalogue through the same SettingField component; Branding adds the
 * config-gallery media pickers (choose an uploaded file or upload a new one).
 */
/** Mail drivers — same catalogue as the Laravel app (EmailDrivers::definitions). */
const MAIL_DRIVERS = [
  { id: 'http', name: 'Africa CDC Email Server (HTTP)', panel: 'http', description: 'Central Africa CDC notifications gateway — same as Staff Portal.' },
  { id: 'exchange', name: 'Microsoft Exchange / Graph', panel: 'exchange', description: 'Send via Microsoft Graph with an Entra ID app.' },
  { id: 'smtp', name: 'SMTP', panel: 'smtp', description: 'Any standard SMTP server.' },
  { id: 'zoho', name: 'Zoho Mail', panel: 'smtp', description: 'Zoho SMTP (defaults to smtp.zoho.com:587).' },
  { id: 'sendgrid', name: 'SendGrid', panel: 'api', description: 'Twilio SendGrid Web API.' },
  { id: 'mailgun', name: 'Mailgun', panel: 'api', description: 'Mailgun Messages API.' },
  { id: 'postmark', name: 'Postmark', panel: 'api', description: 'Postmark server API token.' },
  { id: 'mailjet', name: 'Mailjet', panel: 'api', description: 'Mailjet Send API v3.1.' },
  { id: 'log', name: 'Log only (development)', panel: 'log', description: 'Write messages to the application log instead of sending.' },
];
const MAIL_PANEL_FIELDS = {
  smtp: [
    { key: 'mail_host', label: 'SMTP host', type: 'text', hint: 'Zoho defaults to smtp.zoho.com when left blank.' },
    { key: 'mail_port', label: 'SMTP port', type: 'text' },
    { key: 'mail_username', label: 'Username', type: 'text' },
    { key: 'mail_password', label: 'Password', type: 'password' },
    { key: 'mail_encryption', label: 'Encryption', type: 'select', options: ['tls', 'ssl', 'none'] },
  ],
  exchange: [
    { key: 'exchange_tenant_id', label: 'Tenant ID', type: 'text' },
    { key: 'exchange_client_id', label: 'Client ID', type: 'text' },
    { key: 'exchange_client_secret', label: 'Client secret', type: 'password' },
    { key: 'exchange_auth_method', label: 'Auth method', type: 'select', options: ['client_credentials', 'authorization_code'] },
    { key: 'exchange_scope', label: 'Scope', type: 'text' },
    { key: 'exchange_redirect_uri', label: 'Redirect URI', type: 'text' },
  ],
  http: [
    { key: 'mail_http_base_url', label: 'API base URL', type: 'text' },
    { key: 'mail_http_client_id', label: 'Client ID', type: 'text' },
    { key: 'mail_http_client_secret', label: 'Client secret', type: 'password' },
  ],
  api: [
    { key: 'mail_api_key', label: 'API key', type: 'password' },
    { key: 'mail_api_secret', label: 'API secret', type: 'password', hint: 'Mailjet only; leave blank elsewhere.' },
    { key: 'mail_api_domain', label: 'Domain', type: 'text', hint: 'Mailgun sending domain.' },
    { key: 'mail_api_region', label: 'Region', type: 'select', options: ['us', 'eu'] },
    { key: 'mail_api_base_url', label: 'Base URL override', type: 'text' },
    { key: 'mail_api_message_stream', label: 'Message stream', type: 'text', hint: 'Postmark only; default "outbound".' },
  ],
  log: [],
};

const FONTS = ['univers_45_light', 'system', 'georgia', 'arial', 'helvetica'].map((value) => ({ id: value, name: value }));
const TABS = [
  {
    id: 'general', label: 'General', icon: 'fa-circle-info',
    fields: [
      { key: 'site_name', label: 'Site name', type: 'text' },
      { key: 'title', label: 'Browser title', type: 'text' },
      { key: 'slogan', label: 'Slogan', type: 'text' },
      { key: 'timezone', label: 'Timezone', type: 'text', hint: 'e.g. Africa/Addis_Ababa' },
      { key: 'site_description', label: 'Site description (SEO)', type: 'textarea' },
      { key: 'seo_keywords', label: 'SEO keywords', type: 'textarea', hint: 'Comma-separated.' },
      { key: 'use_seo_friendly_urls', label: 'SEO-friendly URLs (slugs)', type: 'boolean' },
    ],
  },
  { id: 'branding', label: 'Branding', icon: 'fa-image', fields: [
    { key: 'banner_text', label: 'Spotlight banner text', type: 'text' },
    { key: 'spotlight_overlay_color', label: 'Spotlight overlay colour', type: 'color' },
    { key: 'spotlight_overlay_opacity', label: 'Spotlight overlay opacity', type: 'text', hint: '0 – 1, e.g. 0.55' },
  ] },
  {
    id: 'frontend', label: 'Frontend', icon: 'fa-display',
    fields: [
      { key: 'footer_style', label: 'Footer style', type: 'select', options: ['dark', 'light'] },
      { key: 'preloader_enabled', label: 'Show page preloader', type: 'boolean' },
      { key: 'preloader_text', label: 'Preloader text', type: 'text' },
      { key: 'show_publication_card_file_type_badge', label: 'File-type badge on cards', type: 'boolean' },
      { key: 'theme_cards_per_row', label: 'Theme cards per row', type: 'select', options: ['3', '4', '6'] },
      { key: 'content_disclaimer', label: 'Content disclaimer', type: 'textarea' },
    ],
  },
  {
    id: 'colors', label: 'Colors & Typography', icon: 'fa-palette',
    fields: [
      { key: 'primary_color', label: 'Primary colour', type: 'color' },
      { key: 'secondary_color', label: 'Secondary colour', type: 'color' },
      { key: 'gradient_start_color', label: 'Gradient start', type: 'color' },
      { key: 'gradient_end_color', label: 'Gradient end', type: 'color' },
      { key: 'default_font_color', label: 'Body font colour', type: 'color' },
      { key: 'primary_text_color', label: 'Primary text colour', type: 'color' },
      { key: 'primary_font', label: 'Primary font', type: 'select', options: FONTS },
      { key: 'front_body_font_size', label: 'Body font size (px)', type: 'text' },
    ],
  },
  {
    id: 'navigation', label: 'Navigation', icon: 'fa-bars',
    fields: [
      { key: 'nav_style', label: 'Navigation style', type: 'text' },
      { key: 'menu_icons_enabled', label: 'Icons in the menu', type: 'boolean' },
      { key: 'nav_font_size', label: 'Nav font size (px)', type: 'text' },
      { key: 'nav_font_weight', label: 'Nav font weight', type: 'select', options: ['400', '500', '600', '700'] },
      { key: 'nav_link_color', label: 'Link colour', type: 'color' },
      { key: 'nav_link_hover_color', label: 'Link hover colour', type: 'color' },
      { key: 'nav_link_active_color', label: 'Active link colour', type: 'color' },
    ],
  },
  {
    id: 'homepage', label: 'Homepage', icon: 'fa-house',
    fields: [
      { key: 'show_featured', label: 'Featured resources section', type: 'boolean' },
      { key: 'show_top_searches', label: 'Top searches section', type: 'boolean' },
      { key: 'show_health_themes', label: 'Health themes section', type: 'boolean' },
      { key: 'show_events', label: 'Events section', type: 'boolean' },
      { key: 'show_quotes', label: 'Quotes section', type: 'boolean' },
      { key: 'show_tags', label: 'Tags section', type: 'boolean' },
      { key: 'show_quiz', label: 'Quiz section', type: 'boolean' },
      { key: 'section_title_health_themes', label: 'Health themes title', type: 'text' },
      { key: 'section_title_top_searches', label: 'Top searches title', type: 'text' },
      { key: 'section_title_recommended', label: 'Recommended title', type: 'text' },
      { key: 'section_title_flagship_initiatives', label: 'Flagship initiatives title', type: 'text' },
      { key: 'spotlight_animation', label: 'Spotlight animation', type: 'text' },
      { key: 'spotlight_animation_speed', label: 'Spotlight speed (s)', type: 'text' },
    ],
  },
  {
    id: 'search', label: 'Search', icon: 'fa-magnifying-glass',
    fields: [
      { key: 'enable_ai_search', label: 'AI search insights', type: 'boolean' },
      { key: 'search_show_forums', label: 'Include forums in results', type: 'boolean' },
      { key: 'search_show_communities', label: 'Include communities in results', type: 'boolean' },
      { key: 'search_pagination_mode', label: 'Pagination mode', type: 'select', options: ['numbers', 'load_more', 'infinite'] },
    ],
  },
  {
    id: 'contact', label: 'Contact', icon: 'fa-address-book',
    fields: [
      { key: 'address', label: 'Address', type: 'textarea' },
      { key: 'email', label: 'Contact email', type: 'text' },
      { key: 'phone', label: 'Phone', type: 'text' },
      { key: 'facebook', label: 'Facebook URL', type: 'text' },
      { key: 'twitter', label: 'X / Twitter URL', type: 'text' },
      { key: 'linkedin', label: 'LinkedIn URL', type: 'text' },
      { key: 'researchgate', label: 'ResearchGate URL', type: 'text' },
    ],
  },
  { id: 'email', label: 'Email', icon: 'fa-envelope', fields: [] },
  {
    id: 'social', label: 'Social Login', icon: 'fa-right-to-bracket',
    note: 'Secrets are write-only: saved values show as •••••••• and are never echoed back.',
    fields: [
      { key: 'enable_google_login', label: 'Google sign-in', type: 'boolean' },
      { key: 'google_client_id', label: 'Google client ID', type: 'text' },
      { key: 'google_client_secret', label: 'Google client secret', type: 'password' },
      { key: 'enable_microsoft_login', label: 'Microsoft sign-in', type: 'boolean' },
      { key: 'microsoft_client_id', label: 'Microsoft client ID', type: 'text' },
      { key: 'microsoft_client_secret', label: 'Microsoft client secret', type: 'password' },
      { key: 'microsoft_tenant_id', label: 'Microsoft tenant ID', type: 'text' },
      { key: 'enable_linkedin_login', label: 'LinkedIn sign-in', type: 'boolean' },
      { key: 'linkedin_client_id', label: 'LinkedIn client ID', type: 'text' },
      { key: 'linkedin_client_secret', label: 'LinkedIn client secret', type: 'password' },
      { key: 'allow_email_password_accounts_social_login', label: 'Allow email+password accounts to also use social login', type: 'boolean' },
    ],
  },
  { id: 'badges', label: 'Badges', icon: 'fa-medal', fields: [] },
  {
    id: 'advanced', label: 'Advanced', icon: 'fa-gears',
    fields: [
      { key: 'auto_approve_comments', label: 'Auto-approve comments', type: 'boolean' },
      { key: 'auto_approve_publications', label: 'Auto-approve publications', type: 'boolean' },
      { key: 'enable_version_submission', label: 'Allow new versions of resources', type: 'boolean' },
      { key: 'publication_min_words', label: 'Minimum words in descriptions', type: 'text' },
      { key: 'block_disposable_email_registration', label: 'Block disposable email registration', type: 'boolean' },
      { key: 'blocked_email_domains', label: 'Blocked email domains', type: 'textarea', hint: 'One domain per line.' },
      { key: 'analytics_script', label: 'Analytics script', type: 'textarea', hint: 'Injected on every public page — administrators only.' },
      { key: 'trash_retention_days', label: 'Trash retention (days)', type: 'text', hint: 'Trashed resources and documents are destroyed permanently after this many days (default 30).' },
      { key: 'federation_api_token', label: 'Federation API token', type: 'password', hint: 'Partner hubs present this token to read from / write to this hub. Share it only with trusted hubs.' },
    ],
  },
];

const SettingField = ({ field, value, onChange }) => {
  if (field.type === 'boolean') {
    return (
      <label className="d-flex align-items-center mb-0" style={{ gap: 8, cursor: 'pointer', fontWeight: 500 }}>
        <input type="checkbox" checked={Boolean(value)} onChange={(event) => onChange(field.key, event.target.checked)} />
        {field.label}
      </label>
    );
  }
  return (
    <FormField label={field.label} hint={field.hint}>
      {field.type === 'textarea' && (
        <textarea className="form-control" rows={3} value={value ?? ''} onChange={(event) => onChange(field.key, event.target.value)} />
      )}
      {field.type === 'color' && (
        <span className="d-flex align-items-center" style={{ gap: 8 }}>
          <input type="color" className="form-control" style={{ width: 52, height: 38, padding: 3 }} value={value || '#006239'} onChange={(event) => onChange(field.key, event.target.value)} />
          <input className="form-control" style={{ maxWidth: 120 }} value={value ?? ''} placeholder="#006239" onChange={(event) => onChange(field.key, event.target.value)} />
        </span>
      )}
      {field.type === 'select' && (
        <SearchSelect options={(field.options ?? []).map((option) => (typeof option === 'string' ? { id: option, name: option } : option))}
          value={value ?? ''} placeholder="Select…" onChange={(next) => onChange(field.key, next)} />
      )}
      {field.type === 'text' && (
        <input className="form-control" value={value ?? ''} onChange={(event) => onChange(field.key, event.target.value)} />
      )}
      {field.type === 'password' && (
        <input type="password" className="form-control" value={value ?? ''} autoComplete="new-password"
          placeholder="Not set" onFocus={(event) => { if (event.target.value === '••••••••') onChange(field.key, ''); }}
          onChange={(event) => onChange(field.key, event.target.value)} />
      )}
    </FormField>
  );
};

/** Branding media picker: preview + config-gallery select + upload. */
const MediaPicker = ({ label, hint, value, gallery, onPick, onUploaded }) => {
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState('');
  const current = String(value ?? '').split('/').pop();

  const uploadFile = async (file) => {
    setBusy(true);
    setError('');
    try {
      const body = new FormData();
      body.set('file', file);
      const response = await fetch('/api/admin/settings/gallery', { method: 'POST', body, credentials: 'same-origin' });
      const payload = await response.json().catch(() => ({}));
      if (!response.ok) throw new Error(payload.error ?? `Upload failed (${response.status})`);
      onUploaded(payload);
    } catch (err) {
      setError(err.message);
    } finally {
      setBusy(false);
    }
  };

  return (
    <div className="kh-admin-card" style={{ padding: 16 }}>
      <div className="d-flex align-items-start justify-content-between" style={{ gap: 12 }}>
        <div>
          <h3 className="ft-bold mb-1" style={{ fontSize: '0.95rem', color: '#18181b' }}>{label}</h3>
          <small className="text-muted d-block mb-2">{hint}</small>
        </div>
        {value && <img src={gallery.find((file) => file.name === current)?.url ?? value} alt="" style={{ maxHeight: 54, maxWidth: 120, objectFit: 'contain', border: '1px dashed #d4d4d8', borderRadius: 6, padding: 4, background: '#fff' }} />}
      </div>
      <FormField label="Config gallery">
        <SearchSelect options={gallery.map((file) => ({ id: file.name, name: file.name }))} value={current}
          placeholder="Choose an uploaded file…" onChange={(next) => onPick(next)} />
      </FormField>
      <label className="btn btn-sm btn-outline-secondary mb-0" style={{ cursor: 'pointer' }}>
        <i className={`fa ${busy ? 'fa-spinner fa-spin' : 'fa-upload'} mr-1`} aria-hidden="true" /> {busy ? 'Uploading…' : 'Upload new file'}
        <input type="file" hidden accept="image/*,.ico,.svg" onChange={(event) => event.target.files?.[0] && uploadFile(event.target.files[0])} />
      </label>
      {error && <small className="text-danger d-block mt-1">{error}</small>}
    </div>
  );
};

export const ConfigurePage = () => {
  const canEdit = usePermission('admin.settings.edit');
  const [tab, setTab] = useState('general');
  const [values, setValues] = useState(null);
  const [dirty, setDirty] = useState({});
  const [gallery, setGallery] = useState([]);
  const [message, setMessage] = useState(null);
  const [busy, setBusy] = useState(false);

  useEffect(() => {
    apiFetch('/api/admin/settings').then((payload) => setValues(payload.settings)).catch((err) => setMessage({ error: err.message }));
    apiFetch('/api/admin/settings/gallery').then((payload) => setGallery(payload.files)).catch(() => setGallery([]));
  }, []);

  const set = (key, value) => {
    setValues((previous) => ({ ...previous, [key]: value }));
    setDirty((previous) => ({ ...previous, [key]: value }));
  };

  const save = async () => {
    if (Object.keys(dirty).length === 0) return setMessage({ ok: 'Nothing changed.' });
    setBusy(true);
    setMessage(null);
    try {
      const payload = await apiFetch('/api/admin/settings', { method: 'PUT', body: dirty });
      setDirty({});
      setMessage({ ok: `Saved ${payload.updated.length} setting(s). Reload the public site to see the change.` });
    } catch (err) {
      setMessage({ error: err.details ? err.details.map((detail) => `${detail.path}: ${detail.message}`).join('; ') : err.message });
    } finally {
      setBusy(false);
    }
  };

  const active = useMemo(() => TABS.find((entry) => entry.id === tab), [tab]);
  const dirtyCount = Object.keys(dirty).length;

  return (
    <AdminShell title="System configuration" subtitle="Manage the site's settings and preferences."
      actions={canEdit && (
        <button type="button" className="btn btn-sm theme-bg text-white" disabled={busy || dirtyCount === 0} onClick={save}>
          <i className="fa fa-floppy-disk mr-1" aria-hidden="true" /> {busy ? 'Saving…' : `Save changes${dirtyCount ? ` (${dirtyCount})` : ''}`}
        </button>
      )}>
      <Flash message={message} />
      <nav className="mb-3 d-flex flex-wrap" style={{ gap: 6 }} aria-label="Configuration tabs">
        {TABS.map((entry) => (
          <button key={entry.id} type="button" onClick={() => setTab(entry.id)}
            className={`btn btn-sm ${tab === entry.id ? 'theme-bg text-white' : 'btn-outline-secondary'}`}>
            <i className={`fa ${entry.icon} mr-1`} aria-hidden="true" /> {entry.label}
          </button>
        ))}
      </nav>
      {!values && !message?.error && <AdminCard><p role="status" className="mb-0">Loading…</p></AdminCard>}
      {values && active && (
        <>
          {tab === 'branding' && (
            <div className="row">
              <div className="col-lg-4 col-md-6 mb-1">
                <MediaPicker label="Site logo" hint="Displayed in the site header and footer." value={values.logo} gallery={gallery}
                  onPick={(name) => set('logo', name)} onUploaded={(file) => { setGallery((g) => [file, ...g]); set('logo', file.name); }} />
              </div>
              <div className="col-lg-4 col-md-6 mb-1">
                <MediaPicker label="Favicon" hint="Browser tab icon shown across the site." value={values.favicon} gallery={gallery}
                  onPick={(name) => set('favicon', name)} onUploaded={(file) => { setGallery((g) => [file, ...g]); set('favicon', file.name); }} />
              </div>
              <div className="col-lg-4 col-md-6 mb-1">
                <MediaPicker label="Spotlight banner" hint="Homepage hero background image." value={values.spotlight_banner} gallery={gallery}
                  onPick={(name) => set('spotlight_banner', name)} onUploaded={(file) => { setGallery((g) => [file, ...g]); set('spotlight_banner', file.name); }} />
              </div>
              <div className="col-12">
                <AdminCard title="Logo display options">
                  <div className="row">
                    <div className="col-md-3">
                      <FormField label="Logo height (px)">
                        <SearchSelect options={['50', '60', '70', '80', '90'].map((v) => ({ id: v, name: `${v}px` }))}
                          value={String(values.logo_scale ?? '')} placeholder="70px" onChange={(next) => set('logo_scale', next)} />
                      </FormField>
                    </div>
                    <div className="col-md-4 d-flex align-items-center">
                      <SettingField field={{ key: 'header_logo_inverse', label: 'Inverse logo in header', type: 'boolean' }} value={values.header_logo_inverse} onChange={set} />
                    </div>
                    <div className="col-md-4 d-flex align-items-center">
                      <SettingField field={{ key: 'footer_logo_inverse', label: 'Inverse logo in footer', type: 'boolean' }} value={values.footer_logo_inverse} onChange={set} />
                    </div>
                  </div>
                </AdminCard>
              </div>
            </div>
          )}
          {tab === 'email' && (
            <AdminCard title="Email delivery">
              <div className="alert alert-warning">Secrets are write-only: saved values show as •••••••• and are never echoed back. Leave a masked field untouched to keep the stored value.</div>
              <div className="row">
                <div className="col-md-5">
                  <FormField label="Mail service" hint={MAIL_DRIVERS.find((d) => d.id === (values.email_driver || 'exchange'))?.description}>
                    <SearchSelect options={MAIL_DRIVERS} value={values.email_driver || 'exchange'} placeholder="Choose a service"
                      onChange={(next) => set('email_driver', next)} />
                  </FormField>
                </div>
                <div className="col-md-4"><SettingField field={{ key: 'mail_from_name', label: 'From name', type: 'text' }} value={values.mail_from_name} onChange={set} /></div>
                <div className="col-md-3"><SettingField field={{ key: 'mail_from_address', label: 'From address', type: 'text' }} value={values.mail_from_address} onChange={set} /></div>
              </div>
              <hr />
              <div className="row">
                {(MAIL_PANEL_FIELDS[MAIL_DRIVERS.find((d) => d.id === (values.email_driver || 'exchange'))?.panel ?? 'smtp'] ?? []).map((field) => (
                  <div key={field.key} className="col-md-4">
                    <SettingField field={field} value={values[field.key]} onChange={set} />
                  </div>
                ))}
                {(values.email_driver || 'exchange') === 'log' && <div className="col-12 text-muted">Messages are written to the application log — nothing is sent.</div>}
              </div>
            </AdminCard>
          )}
          {tab === 'badges' ? (
            <AdminCard title="Contributor badges">
              <p className="mb-2">Badge tiers (names, thresholds, colours) are managed on their own page.</p>
              <Link to="../badges" relative="path" className="btn btn-sm theme-bg text-white"><i className="fa fa-medal mr-1" aria-hidden="true" /> Open badge management</Link>
            </AdminCard>
          ) : (
            <AdminCard title={active.label}>
              {active.note && <div className="alert alert-warning">{active.note}</div>}
              <div className="row">
                {active.fields.map((field) => (
                  <div key={field.key} className={field.type === 'textarea' ? 'col-12' : 'col-md-6 col-lg-4'} style={field.type === 'boolean' ? { paddingTop: 8, paddingBottom: 8 } : undefined}>
                    <SettingField field={field} value={values[field.key]} onChange={set} />
                  </div>
                ))}
              </div>
            </AdminCard>
          )}
        </>
      )}
    </AdminShell>
  );
};
