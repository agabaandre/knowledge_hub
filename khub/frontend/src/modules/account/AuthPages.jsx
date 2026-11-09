import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Seo } from '../../core/Seo.jsx';
import { apiFetch } from '../../core/api.js';
import { useSite } from '../../core/site-context.jsx';
import { withSitePrefix } from '@khub/shared/urls';

const useAfterAuth = () => {
  const { refresh, manifest } = useSite();
  const navigate = useNavigate();
  return async () => {
    await refresh();
    navigate(withSitePrefix(manifest.site.pathPrefix, '/'));
  };
};

/** Centred white card on the grey band, like the portal's login modal. */
const AuthCard = ({ title, children }) => (
  <div className="gray py-5">
    <div className="container">
      <div className="row justify-content-center">
        <div className="col-lg-5 col-md-7 col-12">
          <div className="bg-white rounded border p-5">
            <div className="text-center mb-4">
              <h1 className="m-0 ft-regular" style={{ fontSize: '1.6rem' }}>{title}</h1>
            </div>
            {children}
          </div>
        </div>
      </div>
    </div>
  </div>
);

export const RegisterPage = () => {
  const [error, setError] = useState('');
  const afterAuth = useAfterAuth();

  const [notice, setNotice] = useState('');
  const submit = async (event) => {
    event.preventDefault();
    const form = Object.fromEntries(new FormData(event.currentTarget));
    try {
      const result = await apiFetch('/api/auth/register', { method: 'POST', body: form });
      if (result.verificationRequired) {
        setNotice(result.message);
        return;
      }
      await afterAuth();
    } catch (err) {
      setError(err.message);
    }
  };

  return (
    <AuthCard title="Create an Account">
      <Seo title="Create an account" path="/register" noindex />
      {error && <div role="alert" className="alert alert-danger">{error}</div>}
      {notice && <div role="status" className="alert alert-success">{notice}</div>}
      <form onSubmit={submit}>
        <div className="row">
          <div className="col-6 form-group">
            <label htmlFor="first_name">First name</label>
            <input id="first_name" name="first_name" className="form-control" required />
          </div>
          <div className="col-6 form-group">
            <label htmlFor="last_name">Last name</label>
            <input id="last_name" name="last_name" className="form-control" required />
          </div>
        </div>
        <div className="form-group">
          <label htmlFor="email">Email</label>
          <input id="email" name="email" type="email" className="form-control" required autoComplete="email" />
        </div>
        <div className="form-group">
          <label htmlFor="organization">Organisation</label>
          <input id="organization" name="organization" className="form-control" />
        </div>
        <div className="form-group">
          <label htmlFor="password">Password (8+ characters)</label>
          <input id="password" name="password" type="password" minLength={8} className="form-control" required autoComplete="new-password" />
        </div>
        <button type="submit" className="btn btn-md full-width theme-bg text-light fs-md ft-medium" style={{ width: '100%' }}>Create Account</button>
      </form>
    </AuthCard>
  );
};
