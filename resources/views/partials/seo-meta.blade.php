<!-- SEO Meta Tags -->
@php
    $seoTitle = $seoMeta['title'] ?? config('app.name') . ' - Professional Scout Platform';
    $seoDescription = $seoMeta['description'] ?? 'NextScout - Professional scout platform for football, basketball, and volleyball. Discover talent, track performance, and connect with clubs.';
    $seoKeywords = $seoMeta['keywords'] ?? 'scout, football, basketball, volleyball, player profile, transfer market';
    $canonicalUrl = $seoMeta['canonical_url'] ?? url()->current();
    $ogTitle = $seoMeta['og_title'] ?? $seoTitle;
    $ogDescription = $seoMeta['og_description'] ?? $seoDescription;
    $ogImage = $seoMeta['og_image'] ?? asset('images/og-default.jpg');
    $schemaMarkup = $seoMeta['schema_markup'] ?? null;
@endphp

<!-- Basic Meta Tags -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- SEO Meta Tags -->
<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}">
<meta name="keywords" content="{{ $seoKeywords }}">
<link rel="canonical" href="{{ $canonicalUrl }}">
<meta name="robots" content="index, follow">
<meta name="author" content="NextScout">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="{{ $seoMeta['og_type'] ?? 'website' }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:title" content="{{ $ogTitle }}">
<meta property="og:description" content="{{ $ogDescription }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:site_name" content="NextScout">
<meta property="og:locale" content="en_US">

<!-- Twitter Card -->
<meta name="twitter:card" content="{{ $seoMeta['twitter_card'] ?? 'summary_large_image' }}">
<meta name="twitter:url" content="{{ $canonicalUrl }}">
<meta name="twitter:title" content="{{ $seoMeta['twitter_title'] ?? $ogTitle }}">
<meta name="twitter:description" content="{{ $seoMeta['twitter_description'] ?? $ogDescription }}">
<meta name="twitter:image" content="{{ $seoMeta['twitter_image'] ?? $ogImage }}">
<meta name="twitter:site" content="@NextScout">
<meta name="twitter:creator" content="@NextScout">

<!-- Mobile App Links -->
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="apple-mobile-web-app-title" content="NextScout">
<link rel="apple-touch-icon" href="{{ asset('images/app-icon-180.png') }}">

<!-- Android -->
<meta name="mobile-web-app-capable" content="yes">
<meta name="theme-color" content="#1E40AF">

<!-- Favicons -->
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
<link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

<!-- Schema.org JSON-LD -->
@if($schemaMarkup)
<script type="application/ld+json">
{!! json_encode($schemaMarkup, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endif

<!-- Organization Schema (Global) -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "NextScout",
  "url": "{{ url('/') }}",
  "logo": "{{ asset('images/logo.png') }}",
  "description": "Professional scout platform for discovering and tracking sports talent",
  "sameAs": [
    "https://facebook.com/nextscout",
    "https://twitter.com/nextscout",
    "https://instagram.com/nextscout",
    "https://linkedin.com/company/nextscout"
  ],
  "contactPoint": {
    "@type": "ContactPoint",
    "telephone": "+90-XXX-XXX-XXXX",
    "contactType": "Customer Service",
    "email": "support@nextscout.com",
    "availableLanguage": ["en", "tr"]
  }
}
</script>

<!-- WebSite Search Schema -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "url": "{{ url('/') }}",
  "potentialAction": {
    "@type": "SearchAction",
    "target": {
      "@type": "EntryPoint",
      "urlTemplate": "{{ url('/search?q={search_term_string}') }}"
    },
    "query-input": "required name=search_term_string"
  }
}
</script>

<!-- Preconnect to external resources -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="dns-prefetch" href="//cdn.jsdelivr.net">
<link rel="dns-prefetch" href="//www.googletagmanager.com">
<link rel="dns-prefetch" href="//www.google-analytics.com">

<!-- Alternate Language Versions -->
<link rel="alternate" hreflang="en" href="{{ url()->current() }}">
<link rel="alternate" hreflang="tr" href="{{ url()->current() . '?lang=tr' }}">
<link rel="alternate" hreflang="x-default" href="{{ url()->current() }}">
