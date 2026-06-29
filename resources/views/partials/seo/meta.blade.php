{{-- SEO Meta Tags Partial --}}
@php
    $currentUrl = url()->current();
    $canonicalUrl = $canonicalUrl ?? $currentUrl;
    $pageTitle = $pageTitle ?? (settings()->title ?? 'Africa CDC Knowledge Hub');
    $pageDescription = $pageDescription ?? \App\Support\SeoDefaults::descriptionForRequest(request());
    $pageKeywords = $pageKeywords ?? (settings()->seo_keywords ?? 'Africa CDC, public health, health research, publications, knowledge hub, Africa');
    $pageImage = $pageImage ?? (settings()->logo ?? asset('assets/images/logo.png'));
    $siteName = settings()->site_name ?? 'Africa CDC Knowledge Hub';
    $siteUrl = config('app.url');
@endphp

{{-- Basic Meta Tags --}}
<meta name="description" content="{{ strip_tags($pageDescription) }}">
<meta name="keywords" content="{{ $pageKeywords }}">
<meta name="author" content="{{ $pageAuthor ?? $siteName }}">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<meta name="googlebot" content="index, follow">
<meta name="language" content="English">
<meta name="revisit-after" content="7 days">

{{-- Open Graph / Facebook --}}
<meta property="og:type" content="{{ $ogType ?? 'website' }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:title" content="{{ strip_tags($pageTitle) }}">
<meta property="og:description" content="{{ strip_tags($pageDescription) }}">
<meta property="og:image" content="{{ $pageImage }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="{{ strip_tags($pageTitle) }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:locale" content="en_US">
@if(isset($pageAuthor) && ($ogType ?? '') === 'article')
<meta property="article:author" content="{{ strip_tags($pageAuthor) }}">
@if(!empty($articlePublishedTime))
<meta property="article:published_time" content="{{ $articlePublishedTime }}">
@endif
@if(!empty($articleModifiedTime))
<meta property="article:modified_time" content="{{ $articleModifiedTime }}">
@endif
@endif

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ $canonicalUrl }}">
<meta name="twitter:title" content="{{ strip_tags($pageTitle) }}">
<meta name="twitter:description" content="{{ strip_tags($pageDescription) }}">
<meta name="twitter:image" content="{{ $pageImage }}">
<meta name="twitter:site" content="@AfricaCDC">
@if(isset($pageAuthor))
<meta name="twitter:creator" content="{{ strip_tags($pageAuthor) }}">
@endif

{{-- Canonical URL --}}
<link rel="canonical" href="{{ $canonicalUrl }}">

{{-- Alternate Languages (if multilingual) --}}
@if(isset($alternateLanguages))
    @foreach($alternateLanguages as $lang => $url)
        <link rel="alternate" hreflang="{{ $lang }}" href="{{ $url }}">
    @endforeach
@endif

{{-- Structured Data (JSON-LD) - Organization --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "{{ $siteName }}",
    "url": "{{ $siteUrl }}",
    "logo": "{{ site_favicon_url() }}",
    "description": "{{ strip_tags($pageDescription) }}",
    "sameAs": [
        "https://africacdc.org",
        "https://twitter.com/AfricaCDC",
        "https://www.facebook.com/AfricaCDC",
        "https://www.linkedin.com/company/africa-cdc"
    ],
    "contactPoint": {
        "@type": "ContactPoint",
        "contactType": "Customer Service",
        "url": "{{ $siteUrl }}"
    }
}
</script>

{{-- Structured Data (JSON-LD) - Website --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebSite",
    "@id": "{{ $siteUrl }}#website",
    "name": "{{ $siteName }}",
    "url": "{{ $siteUrl }}",
    "description": "{{ strip_tags($pageDescription) }}",
    "potentialAction": {
        "@type": "SearchAction",
        "target": "{{ url('records/search') }}?term={search_term_string}",
        "query-input": "required name=search_term_string"
    }
}
</script>

{{-- Page-specific structured data will be added via @section('structured_data') --}}
@yield('structured_data')

{{-- Sitemap Reference --}}
<link rel="sitemap" type="application/xml" href="{{ url('sitemap.xml') }}">
<link rel="llms-txt" type="text/markdown" href="{{ url('llms.txt') }}" title="LLM site map">

{{-- Preconnect to external domains for performance --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="dns-prefetch" href="https://translate.google.com">
<link rel="dns-prefetch" href="https://www.google.com">

{{-- Favicon and Icons (square favicon only — do not use the wide site logo here; Google squashes it in search results) --}}
@php
    $faviconUrl = site_favicon_url();
    $faviconMime = site_favicon_mime();
@endphp
<link rel="icon" href="{{ $faviconUrl }}" type="{{ $faviconMime }}">
<link rel="shortcut icon" href="{{ $faviconUrl }}" type="{{ $faviconMime }}">
<link rel="icon" type="{{ $faviconMime }}" sizes="48x48" href="{{ $faviconUrl }}">
<link rel="icon" type="{{ $faviconMime }}" sizes="192x192" href="{{ $faviconUrl }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ $faviconUrl }}">

