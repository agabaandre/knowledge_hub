import { createContext, useContext, useEffect, useState } from 'react';
import { apiFetch } from './api.js';

/** Map uploaded-theme tokens onto the CSS variables the portal uses. */
const TOKEN_VARS = {
  primaryColor: '--theme-color-primary',
  secondaryColor: '--theme-color-secondary',
  textColor: '--default-font-color',
  linkColor: '--text-color-secondary',
  bodyFont: '--font-family-primary',
  headingFont: '--heading-font-family',
};

/** Same font mapping as the Laravel typography partial (settings.primary_font). */
const FONT_STACKS = {
  univers_45_light: '"Univers 45 Light", "Univers", sans-serif',
  arial: 'Arial, Helvetica, sans-serif',
  times_new_roman: '"Times New Roman", Times, serif',
  montserrat: '"Montserrat", sans-serif',
  brandon_text: '"Brandon Text", sans-serif',
};

const applyTypography = (settings) => {
  const root = document.documentElement;
  const stack = FONT_STACKS[settings.primary_font] ?? 'system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
  root.style.setProperty('--font-family-primary', stack);
  if (settings.primary_font === 'montserrat' && !document.getElementById('khub-font-montserrat')) {
    const link = document.createElement('link');
    link.id = 'khub-font-montserrat';
    link.rel = 'stylesheet';
    link.href = 'https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap';
    document.head.appendChild(link);
  }
  const bodySize = Number(settings.front_body_font_size);
  if (bodySize >= 10 && bodySize <= 24) root.style.setProperty('--front-body-font-size', `${bodySize}px`);
  if (settings.default_font_color) root.style.setProperty('--default-font-color', settings.default_font_color);
  const navSize = Number(settings.nav_font_size);
  if (navSize >= 9 && navSize <= 16) {
    root.style.setProperty('--nav-font-size', `${navSize}px`);
    root.style.setProperty('--nav-icon-size', `${navSize + 5}px`);
  }
};

const applyTheme = (theme) => {
  const root = document.documentElement;
  for (const [token, variable] of Object.entries(TOKEN_VARS)) {
    if (theme?.tokens?.[token]) root.style.setProperty(variable, theme.tokens[token]);
    else root.style.removeProperty(variable);
  }
  const id = 'khub-theme-css';
  document.getElementById(id)?.remove();
  if (theme?.stylesheet) {
    const link = document.createElement('link');
    link.id = id;
    link.rel = 'stylesheet';
    link.href = theme.stylesheet;
    document.head.appendChild(link);
  }
};

/**
 * Loads /api/site/manifest once per site: branding, enabled modules, user.
 * Modules that are off are simply absent from `modules`, so routes and
 * navigation can check `useModule('forums')` without knowing about the API.
 */
const SiteContext = createContext(null);

export const SiteProvider = ({ children }) => {
  const [state, setState] = useState({ status: 'loading', manifest: null, error: null });

  useEffect(() => {
    const controller = new AbortController();
    apiFetch('/api/site/manifest', { signal: controller.signal })
      .then((manifest) => {
        applyTheme(manifest.theme);
        applyTypography(manifest.settings ?? {});
        setState({ status: 'ready', manifest, error: null });
      })
      .catch((error) => {
        if (error.name !== 'AbortError') setState({ status: 'error', manifest: null, error });
      });
    return () => controller.abort();
  }, []);

  const refresh = () =>
    apiFetch('/api/site/manifest').then((manifest) => {
      applyTheme(manifest.theme);
      applyTypography(manifest.settings ?? {});
      setState({ status: 'ready', manifest, error: null });
    });

  return <SiteContext.Provider value={{ ...state, refresh }}>{children}</SiteContext.Provider>;
};

export const useSite = () => {
  const context = useContext(SiteContext);
  if (!context) throw new Error('useSite must be used inside SiteProvider');
  return context;
};

export const useModule = (name) => Boolean(useSite().manifest?.modules?.[name]);

export const usePermission = (name) => Boolean(useSite().manifest?.user?.permissions?.includes(name));
