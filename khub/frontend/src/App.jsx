import { Routes, Route, Navigate } from 'react-router-dom';
import { SiteProvider, useSite } from './core/site-context.jsx';
import { I18nProvider } from './core/i18n.jsx';
import { Layout } from './core/Layout.jsx';
import { ModuleGate } from './core/ModuleGate.jsx';
import { HomePage } from './modules/home/HomePage.jsx';
import { SearchPage } from './modules/publications/SearchPage.jsx';
import { ResourcePage } from './modules/publications/ResourcePage.jsx';
import { ForumsPage, ForumPage } from './modules/forums/ForumsPage.jsx';
import { CommunitiesPage } from './modules/communities/CommunitiesPage.jsx';
import { FaqsPage } from './modules/faqs/FaqsPage.jsx';
import { HealthTopicsPage, HealthTopicPage } from './modules/health-topics/HealthTopicsPage.jsx';
import { EventsPage } from './modules/events/EventsPage.jsx';
import { AuthorsPage, AuthorPage } from './modules/authors/AuthorsPage.jsx';
import { CountriesPage, CountryPage } from './modules/countries/CountriesPage.jsx';
import { ModulesAdminPage } from './modules/admin/ModulesAdminPage.jsx';
import { DashboardPage } from './modules/admin/DashboardPage.jsx';
import { ApprovalsPage } from './modules/admin/ApprovalsPage.jsx';
import { ConfigurePage } from './modules/admin/ConfigurePage.jsx';
import { ThemesPage } from './modules/admin/ThemesPage.jsx';
import { AiSettingsPage } from './modules/admin/AiSettingsPage.jsx';
import { BadgesPage } from './modules/admin/BadgesPage.jsx';
import { UsersPage } from './modules/admin/UsersPage.jsx';
import { RolesPage } from './modules/admin/RolesPage.jsx';
import { LanguagesPage } from './modules/admin/LanguagesPage.jsx';
import { TranslationsPage } from './modules/admin/TranslationsPage.jsx';
import { ListsPage } from './modules/admin/ListsPage.jsx';
import { LogsPage, MailingPage } from './modules/admin/LogsPage.jsx';
import { PublicationsAdminPage } from './modules/admin/PublicationsAdminPage.jsx';
import { PublicationAdminDetailsPage } from './modules/admin/PublicationAdminDetailsPage.jsx';
import { DocumentsPage } from './modules/admin/DocumentsPage.jsx';
import { MetricsPage } from './modules/admin/MetricsPage.jsx';
import { RegisterPage } from './modules/account/AuthPages.jsx';
import { LoginPage } from './modules/account/LoginPage.jsx';
import { ProfilePage } from './modules/account/ProfilePage.jsx';
import { PublishPage } from './modules/account/PublishPage.jsx';
import { MyActivityPage } from './modules/account/MyActivityPage.jsx';
import { NotFoundPage } from './modules/system/NotFoundPage.jsx';
import { Seo } from './core/Seo.jsx';
import { AdminGate } from './modules/admin/AdminGate.jsx';
import { AdminLayout } from './modules/admin/AdminLayout.jsx';

/**
 * Routes are relative to the site prefix. The same tree serves
 * `/` (default site) and `/nigeria/...` (path-based multi-site).
 * Public paths match the Laravel portal so bookmarks keep working.
 */
const Routed = () => (
  <Routes>
    <Route element={<Layout />}>
      <Route index element={<HomePage />} />
      <Route path="records" element={<Navigate to="search" replace />} />
      <Route path="records/search" element={<SearchPage />} />
      <Route path="records/resource/:key" element={<ResourcePage />} />
      <Route path="records/tag/:key" element={<SearchPage />} />
      <Route path="records/theme/:key" element={<SearchPage />} />
      <Route path="publications" element={<Navigate to="../records/search" replace />} />
      <Route path="health-topics" element={<ModuleGate module="health-topics"><HealthTopicsPage /></ModuleGate>} />
      <Route path="health-topics/:key" element={<ModuleGate module="health-topics"><HealthTopicPage /></ModuleGate>} />
      <Route path="forums" element={<ModuleGate module="forums"><ForumsPage /></ModuleGate>} />
      <Route path="forums/:key" element={<ModuleGate module="forums"><ForumPage /></ModuleGate>} />
      <Route path="communities" element={<ModuleGate module="communities"><CommunitiesPage /></ModuleGate>} />
      <Route path="events" element={<ModuleGate module="events"><EventsPage /></ModuleGate>} />
      <Route path="faqs" element={<ModuleGate module="faqs"><FaqsPage /></ModuleGate>} />
      <Route path="authors" element={<ModuleGate module="authors"><AuthorsPage /></ModuleGate>} />
      <Route path="browse/authors" element={<Navigate to="../authors" replace />} />
      <Route path="authors/publications/:key" element={<ModuleGate module="authors"><AuthorPage /></ModuleGate>} />
      <Route path="countries" element={<ModuleGate module="countries"><CountriesPage /></ModuleGate>} />
      <Route path="countries/details/:key" element={<ModuleGate module="countries"><CountryPage /></ModuleGate>} />
      <Route path="adminunits" element={<Navigate to="../countries" replace />} />
      <Route path="login" element={<LoginPage />} />
      <Route path="account" element={<ProfilePage />} />
      <Route path="account/publish" element={<PublishPage />} />
      <Route path="account/:kind" element={<MyActivityPage />} />
      <Route path="register" element={<RegisterPage />} />
      <Route path="*" element={<NotFoundPage />} />
    </Route>
    {/* The admin panel has its own chrome (AdminLayout) — no public-site navigation. */}
    <Route path="admin" element={<AdminLayout />}>
      <Route index element={<AdminGate permission="admin.dashboard.view"><DashboardPage /></AdminGate>} />
      <Route path="approvals" element={<AdminGate permission="admin.approvals.view"><ApprovalsPage /></AdminGate>} />
      <Route path="publications" element={<AdminGate permission="admin.publications.view"><PublicationsAdminPage /></AdminGate>} />
      <Route path="publications/details" element={<AdminGate permission="admin.publications.view"><PublicationAdminDetailsPage /></AdminGate>} />
      <Route path="documents" element={<AdminGate permission="admin.documents.view"><DocumentsPage /></AdminGate>} />
      <Route path="configure" element={<AdminGate permission="admin.settings.view"><ConfigurePage /></AdminGate>} />
      <Route path="users" element={<AdminGate permission="admin.users.view"><UsersPage /></AdminGate>} />
      <Route path="roles" element={<AdminGate permission="admin.roles.view"><RolesPage /></AdminGate>} />
      <Route path="languages" element={<AdminGate permission="admin.languages.manage"><LanguagesPage /></AdminGate>} />
      <Route path="translations" element={<AdminGate permission="admin.languages.manage"><TranslationsPage /></AdminGate>} />
      <Route path="lists" element={<AdminGate permission="admin.lists.view"><ListsPage /></AdminGate>} />
      <Route path="themes" element={<AdminGate permission="admin.themes.manage"><ThemesPage /></AdminGate>} />
      <Route path="ai" element={<AdminGate permission="admin.ai.manage"><AiSettingsPage /></AdminGate>} />
      <Route path="badges" element={<AdminGate permission="admin.badges.manage"><BadgesPage /></AdminGate>} />
      <Route path="modules" element={<AdminGate permission="admin.modules.manage"><ModulesAdminPage /></AdminGate>} />
      <Route path="logs" element={<AdminGate permission="admin.logs.view"><LogsPage /></AdminGate>} />
      <Route path="mailing" element={<AdminGate permission="admin.mailing.view"><MailingPage /></AdminGate>} />
      <Route path="metrics" element={<AdminGate permission="admin.metrics.view"><MetricsPage /></AdminGate>} />
    </Route>
  </Routes>
);

const Boot = () => {
  const { status, manifest, error } = useSite();
  if (status === 'loading') {
    return (
      <div className="d-flex align-items-center justify-content-center" style={{ minHeight: '100vh' }} role="status" aria-live="polite">
        <div className="spinner-ring" aria-hidden="true" />
        <span className="sr-only">Loading…</span>
      </div>
    );
  }
  if (status === 'error') {
    return (
      <div className="container py-5">
        <Seo title="Unavailable" noindex />
        <h1 style={{ fontSize: '1.5rem' }}>The portal is temporarily unavailable</h1>
        <p className="text-muted">{error?.message}</p>
      </div>
    );
  }
  return manifest ? <I18nProvider><Routed /></I18nProvider> : null;
};

export const App = () => (
  <SiteProvider>
    <Boot />
  </SiteProvider>
);
