<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Cahaya Sembahyang: pilihan hio, lilin, kertas sembahyang, dan perlengkapan ritual.">
    <meta name="theme-color" content="#8b1e21">
    <title>@yield('title', 'Cahaya Sembahyang | Perlengkapan Sembahyang')</title>
    <link rel="stylesheet" href="{{ asset('css/cahaya.css') }}">
    <script src="{{ asset('js/cahaya.js') }}" defer></script>
    @stack('styles')
</head>
<body data-catalog-url="{{ route('products.index') }}" data-asset-root="{{ asset('images/cahaya') }}">
    <a class="skip-link" href="#main-content">Langsung ke konten</a>

    @include('partials.header')

    <main id="main-content">
        @yield('content')
    </main>

    @include('partials.footer')
    @include('partials.cart')

    <div class="toast" id="site-toast" role="status" aria-live="polite" hidden></div>
    @stack('scripts')
</body>
</html>
