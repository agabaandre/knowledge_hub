import { useState } from 'react';
import { Link, useNavigate, useSearchParams } from 'react-router-dom';
import { Seo } from '../../core/Seo.jsx';
import { apiFetch } from '../../core/api.js';
import { useSite } from '../../core/site-context.jsx';
import { useT } from '../../core/i18n.jsx';
import { withSitePrefix } from '@khub/shared/urls';

/**
 * Login styled after the portal's welcome card: green header band, remember me,
 * lost-password hint, and an honest note about single sign-on availability.
 */
export const LoginPage = () => {
  const { manifest, refresh } = useSite();
  const navigate = useNavigate();
  const [params] = useSearchParams();
  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);
  const settings = manifest.settings;
  const t = useT();
  const to = (path) => withSitePrefix(manifest.site.pathPrefix, path);
  const ssoEnabled = [settings.enable_microsoft_login && 'Microsoft', settings.enable_google_login && 'Google', settings.enable_linkedin_login && 'LinkedIn'].filter(Boolean);

  const submit = async (event) => {
    event.preventDefault();
    const form = new FormData(event.currentTarget);
    setBusy(true);
    setError('');
    try {
      await apiFetch('/api/auth/login', {
        method: 'POST',
        body: { email: form.get('email'), password: form.get('password'), remember: form.get('remember') === 'on' },
      });
      await refresh();
      navigate(to(params.get('redirect') ?? '/'));
    } catch (err) {
      setError(err.status === 401 ? 'Invalid email or password.' : err.message);
    } finally {
      setBusy(false);
    }
  };

  return (
    <div className="gray py-5">
      <Seo title="Log in" path="/login" noindex />
      <div className="container">
        <div className="row justify-content-center">
          <div className="col-xl-4 col-lg-5 col-md-7 col-12">
            <div className="bg-white rounded border overflow-hidden" style={{ boxShadow: '0 10px 32px rgba(15,23,42,0.08)' }}>
              <div className="p-4 text-center text-white" style={{ background: 'linear-gradient(135deg, var(--theme-color-primary, #006239), #0b7a4b)' }}>
                <i className="fa fa-circle-user mb-2" style={{ fontSize: '2.2rem' }} aria-hidden="true" />
                <h1 className="m-0 ft-bold" style={{ fontSize: '1.4rem', color: '#fff' }}>Welcome Back</h1>
                <p className="mb-0" style={{ opacity: 0.85 }}>Sign in to {settings.site_name}</p>
              </div>
              <div className="p-4">
                {params.get('verified') === '1' && <div role="status" className="alert alert-success py-2">Email verified — you can sign in now.</div>}
                {error && <div role="alert" className="alert alert-danger py-2">{error}</div>}
                <form onSubmit={submit}>
                  <div className="form-group">
                    <label htmlFor="email">{t('general.email', 'Email')}</label>
                    <div className="position-relative">
                      <input id="email" name="email" type="email" className="form-control" placeholder="you@example.org" required autoComplete="email" autoFocus />
                    </div>
                  </div>
                  <div className="form-group">
                    <label htmlFor="password">{t('ui_body.password', 'Password')}</label>
                    <input id="password" name="password" type="password" className="form-control" placeholder="Your password" required autoComplete="current-password" />
                  </div>
                  <div className="form-group d-flex align-items-center justify-content-between">
                    <label className="mb-0 d-flex align-items-center" style={{ fontWeight: 400, gap: 6, cursor: 'pointer' }}>
                      <input type="checkbox" name="remember" /> {t('ui_body.remember_me', 'Remember me')}
                    </label>
                    <span className="text-muted medium" title="Ask an administrator to reset your password from Admin → users">{t('ui_body.lost_password', 'Lost your password?')}</span>
                  </div>
                  <button type="submit" className="btn btn-md full-width theme-bg text-light fs-md ft-medium" style={{ width: '100%' }} disabled={busy}>
                    {busy ? '…' : t('ui_body.login_submit', 'Login')}
                  </button>
                </form>
                {ssoEnabled.length > 0 && (
                  <p className="text-muted medium text-center mt-3 mb-0">
                    {ssoEnabled.join(', ')} sign-in is enabled on the classic portal and is coming to this version soon.
                  </p>
                )}
                <hr />
                <p className="text-center mb-0">New here? <Link to={to('/register')} className="theme-cl ft-medium">Create an account</Link></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};
