<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('ppid.name'))</title>
    <meta name="description" content="@yield('meta_description', \App\Models\SiteSetting::getValue('meta_description', config('ppid.name_long')))">
    <link rel="canonical" href="@yield('canonical', url()->current())">
    <link rel="sitemap" type="application/xml" href="{{ route('sitemap') }}">

    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:locale" content="id_ID">
    <meta property="og:site_name" content="{{ config('ppid.name') }}">
    <meta property="og:title" content="@yield('og_title', config('ppid.name'))">
    <meta property="og:description" content="@yield('og_description', config('ppid.name_long'))">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    <meta property="og:image" content="@yield('og_image', asset(config('ppid.hero')))">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', config('ppid.name'))">
    <meta name="twitter:description" content="@yield('og_description', config('ppid.name_long'))">

    <link rel="icon" href="{{ asset(config('ppid.logo')) }}" type="image/png">

    @php
        $orgSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'GovernmentOrganization',
            'name' => config('ppid.name'),
            'legalName' => config('ppid.name_long'),
            'url' => config('app.url'),
            'logo' => asset(config('ppid.logo')),
            'parentOrganization' => [
                '@type' => 'Organization',
                'name' => config('ppid.institution'),
                'url' => 'https://poltekkesbanten.ac.id',
            ],
        ];
        $breadcrumbItems = $breadcrumbs ?? null;
    @endphp
    <script type="application/ld+json">{!! json_encode($orgSchema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
    @hasSection('breadcrumbs_json')
        @yield('breadcrumbs_json')
    @elseif (! empty($breadcrumbItems))
        @php
            $crumbList = [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => collect($breadcrumbItems)->values()->map(function ($item, $i) {
                    $entry = [
                        '@type' => 'ListItem',
                        'position' => $i + 1,
                        'name' => $item['label'],
                    ];
                    if (! empty($item['url'])) {
                        $entry['item'] = $item['url'];
                    }
                    return $entry;
                })->all(),
            ];
        @endphp
        <script type="application/ld+json">{!! json_encode($crumbList, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen flex flex-col antialiased" x-data>
    <a class="skip-link" href="#main-content">Lewati ke konten utama</a>

    <x-layout.header />

    <main id="main-content" class="flex-1" tabindex="-1">
        @yield('content')
    </main>

    <x-layout.footer />
    <x-layout.accessibility />
    <x-layout.back-to-top />

    @stack('scripts')
</body>
</html>
