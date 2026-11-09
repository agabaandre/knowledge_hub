import { useEffect, useRef, useState } from 'react';
import { Link } from 'react-router-dom';
import { apiFetch } from '../../core/api.js';
import { useApi } from '../../core/hooks.js';
import { useSite } from '../../core/site-context.jsx';
import { useI18n } from '../../core/i18n.jsx';
import { Seo } from '../../core/Seo.jsx';
import { Loading } from '../../core/Page.jsx';
import { Avatar } from '../../core/Avatar.jsx';
import { SearchSelect } from '../../core/SearchSelect.jsx';
import { withSitePrefix } from '@khub/shared/urls';

const SectionLabel = ({ children }) => (
  <p className="text-uppercase ft-medium mt-4 mb-3 pb-2" style={{ color: '#64748b', fontSize: '0.78rem', letterSpacing: '0.06em', borderBottom: '1px solid #e2e8f0' }}>{children}</p>
);

const CardHead = ({ title, lead }) => (
  <div className="p-4" style={{ background: 'linear-gradient(180deg, #f1f5f3, #fff)', borderBottom: '1px solid #e2e8f0' }}>
    <h2 className="ft-bold mb-1" style={{ fontSize: '1.3rem', color: '#0f172a' }}>{title}</h2>
    <p className="text-muted mb-0">{lead}</p>
  </div>
);

const GENDERS = [['', 'Prefer not to say'], ['female', 'Female'], ['male', 'Male'], ['other', 'Other']];

/** Account page mirroring the original portal: quick links, Security card, Personal information card. */
export const ProfilePage = () => {
  const { manifest, refresh } = useSite();
  const { languages } = useI18n();
  const lookups = useApi('/api/publications/lookups');
  const [profile, setProfile] = useState(null);
  const [jobTitles, setJobTitles] = useState([]);
  const [form, setForm] = useState(null);
  const [customJob, setCustomJob] = useState(false);
  const [message, setMessage] = useState(null);
  const [pwMessage, setPwMessage] = useState(null);
  const [busy, setBusy] = useState('');
  const fileRef = useRef(null);
  const to = (path) => withSitePrefix(manifest.site.pathPrefix, path);

  useEffect(() => {
    apiFetch('/api/account/profile')
      .then((payload) => {
        setProfile(payload.profile);
        setJobTitles(payload.jobTitles);
        setForm(payload.profile);
        setCustomJob(Boolean(payload.profile.jobTitle) && !payload.jobTitles.includes(payload.profile.jobTitle));
      })
      .catch((err) => setMessage({ error: err.message }));
  }, []);

  const set = (key, value) => setForm((previous) => ({ ...previous, [key]: value }));

  const uploadPhoto = async (file) => {
    setBusy('photo');
    setMessage(null);
    try {
      const body = new FormData();
      body.append('photo', file);
      const response = await fetch('/api/account/photo', { method: 'POST', body, credentials: 'same-origin' });
      const payload = await response.json().catch(() => ({}));
      if (!response.ok) throw new Error(payload.error ?? `Upload failed (${response.status})`);
      setProfile(payload.profile);
      await refresh();
      setMessage({ ok: 'Profile photo updated.' });
    } catch (err) {
      setMessage({ error: err.message });
    } finally {
      setBusy('');
    }
  };

  const saveProfile = async (event) => {
    event.preventDefault();
    setBusy('profile');
    setMessage(null);
    try {
      const payload = await apiFetch('/api/account/profile', {
        method: 'PUT',
        body: {
          first_name: form.firstName, last_name: form.lastName, organization: form.organization,
          phone_number: form.phone, job_title: form.jobTitle, orcid: form.orcid, gender: form.gender,
          country_id: form.countryId || null, language: form.language, theme_preference: form.themePreference,
          is_subscribed: form.subscribed, interests: form.interests,
        },
      });
      setProfile(payload.profile);
      setForm(payload.profile);
      await refresh();
      setMessage({ ok: 'Profile updated.' });
    } catch (err) {
      setMessage({ error: err.details ? err.details.map((d) => `${d.path}: ${d.message}`).join('; ') : err.message });
    } finally {
      setBusy('');
    }
  };

  const updatePassword = async (event) => {
    event.preventDefault();
    const data = new FormData(event.currentTarget);
    setBusy('password');
    setPwMessage(null);
    try {
      await apiFetch('/api/account/password', {
        method: 'POST',
        body: { current_password: data.get('current_password'), password: data.get('password'), password_confirmation: data.get('password_confirmation') },
      });
      event.target.reset();
      setPwMessage({ ok: 'Password updated.' });
    } catch (err) {
      setPwMessage({ error: err.details ? err.details.map((d) => d.message).join('; ') : err.message });
    } finally {
      setBusy('');
    }
  };

  const subThemes = (lookups.data?.themes ?? []).flatMap((theme) => theme.subThemes.map((sub) => ({ ...sub, theme: theme.name })));

  return (
    <div className="gray pb-5">
      <Seo title="My profile" path="/account" noindex />

      {/* Quick links bar, as on the original account page */}
      <div className="bg-white border-bottom mb-4">
        <div className="container py-3 d-flex flex-wrap align-items-center" style={{ gap: '1.75rem' }}>
          <Link to={to('/communities')} className="theme-cl ft-medium text-decoration-none"><i className="fa fa-users mr-1" aria-hidden="true" /> My Communities</Link>
          <Link to={to('/account/forums')} className="theme-cl ft-medium text-decoration-none"><i className="fa fa-comments mr-1" aria-hidden="true" /> My Forums</Link>
          <Link to={to('/account/publish')} className="theme-cl ft-medium text-decoration-none"><i className="fa fa-plus-circle mr-1" aria-hidden="true" /> Publish Resource</Link>
          {profile?.access?.canModerate && (
            <Link to={to('/admin/approvals')} className="theme-cl ft-medium text-decoration-none"><i className="fa fa-inbox mr-1" aria-hidden="true" /> Moderation</Link>
          )}
        </div>
      </div>

      <div className="container">
        {message?.error && <div role="alert" className="alert alert-danger">{message.error}</div>}
        {message?.ok && <div role="status" className="alert alert-success">{message.ok}</div>}
        {!form && !message?.error && <Loading />}
        {form && (
          <div className="row">
            {/* Security card */}
            <div className="col-lg-4 col-md-5 col-12 mb-4">
              <div className="bg-white rounded border overflow-hidden" style={{ boxShadow: '0 2px 10px rgba(15,23,42,0.05)' }}>
                <CardHead title="Security" lead="Update your account password." />
                <form className="p-4" onSubmit={updatePassword}>
                  {pwMessage?.error && <div role="alert" className="alert alert-danger py-2">{pwMessage.error}</div>}
                  {pwMessage?.ok && <div role="status" className="alert alert-success py-2">{pwMessage.ok}</div>}
                  <div className="form-group">
                    <label htmlFor="current_password">Current password</label>
                    <input id="current_password" name="current_password" type="password" className="form-control" placeholder="Current password" required autoComplete="current-password" />
                  </div>
                  <div className="form-group">
                    <label htmlFor="new_password">New password</label>
                    <input id="new_password" name="password" type="password" className="form-control" placeholder="New password" minLength={8} required autoComplete="new-password" />
                  </div>
                  <div className="form-group">
                    <label htmlFor="confirm_password">Confirm new password</label>
                    <input id="confirm_password" name="password_confirmation" type="password" className="form-control" placeholder="Confirm new password" minLength={8} required autoComplete="new-password" />
                  </div>
                  <div className="text-right">
                    <button type="submit" className="btn theme-bg text-white" disabled={Boolean(busy)}>{busy === 'password' ? 'Updating…' : 'Update password'}</button>
                  </div>
                </form>
              </div>
            </div>

            {/* Personal information card */}
            <div className="col-lg-8 col-md-7 col-12 mb-4">
              <div className="bg-white rounded border overflow-hidden" style={{ boxShadow: '0 2px 10px rgba(15,23,42,0.05)' }}>
                <CardHead title="Personal information" lead="Keep your profile up to date for better recommendations and community matching." />
                <form className="p-4" onSubmit={saveProfile}>
                  {/* Photo row */}
                  <div className="d-flex align-items-center rounded p-3 mb-2 flex-wrap" style={{ gap: 18, background: 'linear-gradient(135deg, #f0f7f3, #fbfdfc)', border: '1px solid #e2efe8' }}>
                    <Avatar photo={profile.photo} name={profile.name} size={88} />
                    <div>
                      <input ref={fileRef} type="file" accept="image/jpeg,image/png,image/gif,image/webp" hidden
                        onChange={(event) => { if (event.target.files?.[0]) uploadPhoto(event.target.files[0]); event.target.value = ''; }} />
                      <button type="button" className="btn btn-sm btn-outline-success bg-white" disabled={Boolean(busy)} onClick={() => fileRef.current?.click()}>
                        <i className="fa fa-camera mr-1" aria-hidden="true" /> {busy === 'photo' ? 'Uploading…' : 'Upload photo'}
                      </button>
                      <p className="text-muted mb-0 mt-1" style={{ fontSize: '0.78rem' }}>JPG, PNG, GIF or WebP · max 5 MB</p>
                    </div>
                  </div>

                  <SectionLabel>Preferences</SectionLabel>
                  <div className="form-group">
                    <label htmlFor="pf-language">Language</label>
                    <select id="pf-language" className="custom-select" value={form.language} onChange={(event) => set('language', event.target.value)}>
                      <option value="">—</option>
                      {languages.map((lang) => <option key={lang.code} value={lang.code}>{lang.name}</option>)}
                    </select>
                  </div>
                  <div className="form-group">
                    <label htmlFor="pf-theme">Theme</label>
                    <select id="pf-theme" className="custom-select" value={form.themePreference} onChange={(event) => set('themePreference', event.target.value)}>
                      <option value="light">Light</option>
                      <option value="dark">Dark</option>
                    </select>
                    <small className="text-muted">Choose light or dark theme for the site. Saved with your profile.</small>
                  </div>

                  <SectionLabel>Contact &amp; role</SectionLabel>
                  <div className="row">
                    <div className="col-md-6 form-group">
                      <label htmlFor="pf-first">First name</label>
                      <input id="pf-first" className="form-control" value={form.firstName} onChange={(event) => set('firstName', event.target.value)} required />
                    </div>
                    <div className="col-md-6 form-group">
                      <label htmlFor="pf-last">Last name</label>
                      <input id="pf-last" className="form-control" value={form.lastName} onChange={(event) => set('lastName', event.target.value)} required />
                    </div>
                  </div>
                  <div className="form-group">
                    <label htmlFor="pf-country">Country <span className="text-danger">*</span></label>
                    <SearchSelect id="pf-country" options={(lookups.data?.countries ?? []).map((country) => ({ id: country.id, name: country.name }))}
                      value={form.countryId ?? ''} placeholder="Select Country"
                      onChange={(next) => set('countryId', next ? Number(next) : null)} />
                    <small className="text-muted">Please keep your country up to date for better recommendations and community matching.</small>
                  </div>
                  <div className="form-group">
                    <label htmlFor="pf-gender">Gender</label>
                    <select id="pf-gender" className="custom-select" value={form.gender} onChange={(event) => set('gender', event.target.value)}>
                      {GENDERS.map(([value, label]) => <option key={value} value={value}>{label}</option>)}
                    </select>
                  </div>
                  <div className="row">
                    <div className="col-md-6 form-group">
                      <label htmlFor="pf-email">Email</label>
                      <input id="pf-email" className="form-control" value={form.email} readOnly style={{ background: '#f8fafc' }} />
                      <small className="text-muted">Ask an administrator to change your sign-in email.</small>
                    </div>
                    <div className="col-md-6 form-group">
                      <label htmlFor="pf-phone">Phone number</label>
                      <input id="pf-phone" className="form-control" placeholder="Phone number" value={form.phone} onChange={(event) => set('phone', event.target.value)} />
                    </div>
                  </div>
                  <div className="form-group">
                    <label htmlFor="pf-job">Job title</label>
                    {customJob || jobTitles.length === 0 ? (
                      <input id="pf-job" className="form-control" placeholder="Your job title" value={form.jobTitle} onChange={(event) => set('jobTitle', event.target.value)} />
                    ) : (
                      <SearchSelect id="pf-job" options={jobTitles.map((title) => ({ id: title, name: title }))}
                        value={form.jobTitle} placeholder="Select Job" onChange={(next) => set('jobTitle', next)} />
                    )}
                    {jobTitles.length > 0 && (
                      <div className="rounded border p-2 mt-2" style={{ background: '#f8fafc' }}>
                        <label className="mb-0 theme-cl ft-medium d-flex align-items-center" style={{ gap: 8, cursor: 'pointer', fontSize: '0.85rem' }}>
                          <input type="checkbox" checked={customJob} onChange={(event) => setCustomJob(event.target.checked)} />
                          My job title is missing from the list
                        </label>
                      </div>
                    )}
                  </div>
                  <div className="form-group">
                    <label htmlFor="pf-org">Organization / institution</label>
                    <input id="pf-org" className="form-control" placeholder="Organization / institution name" value={form.organization} onChange={(event) => set('organization', event.target.value)} />
                  </div>
                  <div className="form-group">
                    <label htmlFor="pf-orcid">ORCID</label>
                    <input id="pf-orcid" className="form-control" placeholder="0000-0000-0000-0000" value={form.orcid} onChange={(event) => set('orcid', event.target.value)} />
                    <small className="text-muted">Optional · 19 characters, format 0000-0000-0000-0000</small>
                  </div>

                  {subThemes.length > 0 && (
                    <>
                      <SectionLabel>Interests &amp; communities</SectionLabel>
                      <div className="form-group">
                        <label htmlFor="pf-interests">Your interests</label>
                        <SearchSelect id="pf-interests" multiple
                          options={subThemes.map((sub) => ({ id: sub.id, name: `${sub.theme} — ${sub.name}` }))}
                          value={form.interests} placeholder="Select sub-themes"
                          onChange={(next) => set('interests', next.map(Number))} />
                        <small className="text-muted">Your selected sub-themes drive your recommendations.</small>
                      </div>
                    </>
                  )}

                  <SectionLabel>Updates</SectionLabel>
                  <div className="rounded border p-2" style={{ background: '#f8fafc' }}>
                    <label className="mb-0 theme-cl ft-medium d-flex align-items-center" style={{ gap: 8, cursor: 'pointer', fontSize: '0.9rem' }}>
                      <input type="checkbox" checked={form.subscribed} onChange={(event) => set('subscribed', event.target.checked)} />
                      Subscribe to monthly updates and newsletters
                    </label>
                  </div>

                  <div className="text-right mt-4 pt-3" style={{ borderTop: '1px solid #e2e8f0' }}>
                    <button type="submit" className="btn theme-bg text-white px-4" disabled={Boolean(busy)}>
                      {busy === 'profile' ? 'Saving…' : 'Update profile'}
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};
