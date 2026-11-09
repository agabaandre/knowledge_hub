import { describe, it, expect, beforeEach, vi } from 'vitest';
import { render, screen, waitFor } from '@testing-library/react';
import { MemoryRouter } from 'react-router-dom';
import { HelmetProvider } from 'react-helmet-async';
import { App } from '../src/App.jsx';

/** Canned API responses so pages render the way they do against the real backend. */
const card = {
  id: 1, slug: 'ebola-report', title: 'Ebola situation report', excerpt: 'Weekly update', yearPublished: 2024,
  cover: '', isFeatured: true, isVideo: false, visits: 42, likeCount: 2, commentCount: 1,
  associatedAuthors: '', dataCategory: 'Reports', createdAt: '2026-01-05T00:00:00Z',
  author: { id: 1, name: 'Africa CDC' }, fileType: { id: 1, name: 'Report', icon: 'fa-file' },
  subTheme: { id: 1, name: 'Outbreaks' }, theme: { id: 1, name: 'Emergencies' }, path: '/records/resource/ebola-report',
};

const RESPONSES = {
  '/api/site/manifest': {
    site: { id: 'default', name: 'Knowledge Hub', pathPrefix: '', publicUrl: 'http://localhost', locales: ['en'], features: { statesEnabled: true } },
    settings: { site_name: 'Knowledge Hub', slogan: 'Informing Health Decisions and Actions', logo: '', primary_color: '#006239' },
    modules: { publications: true, forums: true, communities: true, 'health-topics': true, events: true, faqs: true, authors: true, countries: true },
    user: null,
    locale: 'en',
    languages: [
      { code: 'en', name: 'English', flag: '🇺🇸', dir: 'ltr' },
      { code: 'fr', name: 'Français', flag: '🇫🇷', dir: 'ltr' },
    ],
    theme: { slug: 'classic', tokens: {}, stylesheet: null },
  },
  '/api/i18n/en': { locale: 'en', dir: 'ltr', messages: { 'frontend_nav.home': 'Home' } },
  '/api/publications/home': {
    featured: [card], topSearches: [card], topSearchesTotal: 1, initiatives: [],
    themes: [{ id: 1, name: 'Emergency preparedness', icon: 'fa-shield', slug: null, subThemes: [] }],
    healthEmergencies: [], quotes: [], sectionCounts: { healthTopics: 3, authors: 2, geographical: 55, forums: 1 },
  },
  '/api/publications/lookups': { themes: [], dataCategories: [], fileCategories: [], fileTypes: [{ id: 1, name: 'Report' }], countries: [], years: [2024], popularTags: [{ id: 1, slug: 'ebola', name: 'Ebola' }], healthEmergencies: [], staticLinks: [] },
  '/api/publications/search': { data: [card], meta: { page: 1, perPage: 10, total: 1, lastPage: 1 } },
  '/api/faqs': { data: [{ id: 1, question: 'How do I publish?', answer: '<p>Sign in first.</p>' }] },
};

beforeEach(() => {
  vi.stubGlobal('fetch', vi.fn(async (url) => {
    const path = new URL(url, 'http://localhost').pathname;
    const body = RESPONSES[path];
    return new Response(JSON.stringify(body ?? { error: `no stub for ${path}` }), {
      status: body ? 200 : 404,
      headers: { 'content-type': 'application/json' },
    });
  }));
});

const renderAt = (path) =>
  render(
    <HelmetProvider>
      <MemoryRouter initialEntries={[path]}>
        <App />
      </MemoryRouter>
    </HelmetProvider>,
  );

describe('pages render with the Laravel theme markup', () => {
  it('home shows spotlight search, themes, key sections and top searches', async () => {
    const { container } = renderAt('/');
    await waitFor(() => expect(screen.getAllByText('Knowledge Hub').length).toBeGreaterThan(0));
    await waitFor(() => expect(container.querySelector('.home-spotlight')).toBeTruthy());
    expect(container.querySelector('#simple_search')).toBeTruthy();
    expect(container.querySelector('.themes-grid .theme-card')).toBeTruthy();
    await waitFor(() => expect(container.querySelector('.kh-key-sections-grid .kh-key-section-card')).toBeTruthy());
    expect(container.querySelector('.jbr-wrap .top-searches-card')).toBeTruthy();
    expect(container.querySelector('.dark-footer .widget_title')).toBeTruthy();
    expect(container.querySelector('nav#navigation.navigation')).toBeTruthy();
  });

  it('records search shows feed cards and the refine sidebar', async () => {
    const { container } = renderAt('/records/search?term=ebola');
    await waitFor(() => expect(container.querySelector('.community-pub-card')).toBeTruthy());
    expect(container.querySelector('.records-search-sidebar')).toBeTruthy();
    expect(screen.getByText(/1 result found/)).toBeTruthy();
  });

  it('faqs render as a controlled accordion', async () => {
    renderAt('/faqs');
    await waitFor(() => expect(screen.getByText('How do I publish?')).toBeTruthy());
  });

  it('login page uses the welcome card with remember me', async () => {
    renderAt('/login');
    await waitFor(() => expect(screen.getByText('Welcome Back')).toBeTruthy());
    expect(screen.getByLabelText('Email')).toBeTruthy();
    expect(screen.getByText(/Remember me/)).toBeTruthy();
  });
});
