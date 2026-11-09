import { createContext, useCallback, useContext, useEffect, useMemo, useState } from 'react';
import { apiFetch } from './api.js';
import { useSite } from './site-context.jsx';

/**
 * UI translations. Dictionaries come from /api/i18n/{locale} (Laravel's lang
 * files with English as fallback). Switching languages persists a cookie,
 * flips <html lang/dir>, and loads the RTL stylesheet for Arabic.
 */
const I18nContext = createContext(null);
const RTL_CSS_ID = 'khub-rtl-css';

const applyDocumentLocale = (locale, dir) => {
  document.documentElement.lang = locale;
  document.documentElement.dir = dir;
  document.getElementById(RTL_CSS_ID)?.remove();
  if (dir === 'rtl') {
    const link = document.createElement('link');
    link.id = RTL_CSS_ID;
    link.rel = 'stylesheet';
    link.href = '/frontend/css/khub-rtl.css';
    document.head.appendChild(link);
  }
};

export const I18nProvider = ({ children }) => {
  const { manifest } = useSite();
  const [state, setState] = useState({ locale: manifest?.locale ?? 'en', dir: 'ltr', messages: {}, loading: true });

  const load = useCallback(async (locale) => {
    const payload = await apiFetch(`/api/i18n/${locale}`);
    applyDocumentLocale(payload.locale, payload.dir);
    setState({ locale: payload.locale, dir: payload.dir, messages: payload.messages, loading: false });
  }, []);

  useEffect(() => {
    if (manifest?.locale) load(manifest.locale).catch(() => setState((previous) => ({ ...previous, loading: false })));
  }, [manifest?.locale, load]);

  const setLocale = useCallback(
    async (locale) => {
      await apiFetch('/api/i18n/locale', { method: 'POST', body: { locale } });
      await load(locale);
    },
    [load],
  );

  const value = useMemo(() => {
    const t = (key, fallback, params) => {
      let text = state.messages[key] ?? fallback ?? key;
      if (params) text = String(text).replace(/:(\w+)/g, (match, name) => (name in params ? String(params[name]) : match));
      return text;
    };
    return { t, locale: state.locale, dir: state.dir, setLocale, languages: manifest?.languages ?? [] };
  }, [state, setLocale, manifest?.languages]);

  return <I18nContext.Provider value={value}>{children}</I18nContext.Provider>;
};

export const useI18n = () => {
  const context = useContext(I18nContext);
  if (!context) throw new Error('useI18n must be used inside I18nProvider');
  return context;
};

export const useT = () => useI18n().t;
