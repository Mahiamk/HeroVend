<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>HeroVend | Vending Machines & Everything to Run Them</title>
    <meta name="description"
        content="HeroVend sells vending machines and everything it takes to run them — equipment, supplies, and support for operators of all sizes.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/') }}">

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="HeroVend">
    <meta property="og:title" content="HeroVend | Vending Machines & Everything to Run Them">
    <meta property="og:description"
        content="HeroVend sells vending machines and everything it takes to run them — equipment, supplies, and support for operators of all sizes.">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="HeroVend | Vending Machines & Everything to Run Them">
    <meta name="twitter:description"
        content="HeroVend sells vending machines and everything it takes to run them — equipment, supplies, and support for operators of all sizes.">
    <meta name="twitter:image" content="{{ asset('images/og-image.jpg') }}">

    {{-- Favicons --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon-source.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#FF5A1F">

    <script type="application/ld+json">
        {!! json_encode([
            '@@context' => 'https://schema.org',
            '@@type' => 'Organization',
            'name' => 'HeroVend',
            'url' => url('/'),
            'logo' => asset('images/icon-512.png'),
            'description' => 'HeroVend sells vending machines and everything it takes to run them.',
        ], JSON_UNESCAPED_SLASHES) !!}
    </script>

    @fonts

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="overflow-x-hidden antialiased">
    @include('welcome.hero')

    @include('welcome.features')

    @include('welcome.page3-1')

    @include('welcome.page3-2')

    @include('welcome.product-cards')
</body>

</html>