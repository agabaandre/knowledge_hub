import { Helmet } from 'react-helmet-async';
import { ROBOTS_CONTENT, DEFAULT_SEO_KEYWORDS, stripTags, truncate } from '@khub/shared/seo';
import { useSite } from './site-context.jsx';

/**
 * Per-page meta: title, description, canonical, Open Graph, Twitter, robots,
 * and optional JSON-LD. Mirrors the Laravel partials/seo/meta.blade.php output.
 */
export const Seo = ({ title, description, path = '', image, type = 'website', keywords, jsonLd, noindex = false, children }) => {
  const { manifest } = useSite();
  const site = manifest?.site;
  const siteName = manifest?.settings?.site_name ?? site?.name ?? 'Knowledge Hub';
  const base = site?.publicUrl ?? window.location.origin;
  const canonical = `${base}${path}`;
  const fullTitle = title ? `${stripTags(title)} | ${siteName}` : siteName;
  const desc = truncate(description ?? manifest?.settings?.site_description ?? '', 300);
  const pageImage = image ?? manifest?.settings?.logo ?? '';
  const robots = noindex ? 'noindex, nofollow' : ROBOTS_CONTENT;

  return (
    <Helmet>
      <html lang={manifest?.site?.locales?.[0] ?? 'en'} />
      <title>{fullTitle}</title>
      <meta name="description" content={desc} />
      <meta name="keywords" content={keywords ?? manifest?.settings?.seo_keywords ?? DEFAULT_SEO_KEYWORDS} />
      <meta name="robots" content={robots} />
      <link rel="canonical" href={canonical} />
      <meta property="og:type" content={type} />
      <meta property="og:url" content={canonical} />
      <meta property="og:title" content={fullTitle} />
      <meta property="og:description" content={desc} />
      <meta property="og:site_name" content={siteName} />
      {pageImage && <meta property="og:image" content={pageImage} />}
      <meta name="twitter:card" content={pageImage ? 'summary_large_image' : 'summary'} />
      <meta name="twitter:title" content={fullTitle} />
      <meta name="twitter:description" content={desc} />
      {jsonLd && <script type="application/ld+json">{JSON.stringify(jsonLd)}</script>}
      {children}
    </Helmet>
  );
};
