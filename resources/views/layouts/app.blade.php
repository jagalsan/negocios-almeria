<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Empresas digitales en Almería: rankings '.date('Y').' · Negocios Almería' }}</title>
    <meta name="description" content="{{ $description ?? 'Las mejores agencias, desarrolladores y startups de Almería, elegidas con criterios públicos. Rankings revisados a mano.' }}">
    <meta name="robots" content="{{ $robots ?? 'index,follow' }}">
    <link rel="canonical" href="{{ $canonical ?? url()->current().'/' }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate" type="text/plain" href="{{ route('llms') }}" title="LLMs">
    <meta property="og:locale" content="es_ES"><meta property="og:site_name" content="Negocios Almería"><meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title ?? 'Negocios Almería' }}"><meta property="og:description" content="{{ $description ?? 'Rankings independientes de empresas digitales de Almería.' }}"><meta property="og:url" content="{{ $canonical ?? url()->current().'/' }}">
    <meta name="twitter:card" content="summary_large_image">
    @if(config('services.google.site_verification'))
    <meta name="google-site-verification" content="{{ config('services.google.site_verification') }}">
    @endif
    <script>
        window.dataLayer = window.dataLayer || [];
        window.gtag = window.gtag || function () { window.dataLayer.push(arguments); };
        window.gtag('consent', 'default', {
            analytics_storage: 'denied',
            ad_storage: 'denied',
            ad_user_data: 'denied',
            ad_personalization: 'denied',
            functionality_storage: 'granted',
            security_storage: 'granted',
            wait_for_update: 500
        });
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <a class="logo" href="{{ route('home') }}">negocios almería</a>
            <nav class="nav" aria-label="Principal">
                <a href="{{ route('rankings.index') }}">Rankings</a><a href="{{ route('companies.index') }}">Empresas</a><a href="{{ route('pages.como-elaboramos-los-rankings') }}">Cómo elaboramos los rankings</a>
            </nav>
            <div class="header-actions">
                <a href="{{ route('search') }}">Buscar</a><a class="button button-secondary button-small" href="{{ route('pages.anade-tu-empresa') }}">Añade tu empresa</a>
                @auth<a href="{{ route('account.index') }}">{{ auth()->user()->name }}</a>@else<a href="{{ route('login.spanish') }}">Acceder</a>@endauth
            </div>
            <details class="mobile-nav"><summary>Menú</summary><div class="mobile-panel"><form class="search" action="{{ route('search') }}"><input name="q" placeholder="Buscar empresa o servicio"><button class="button">Buscar</button></form><a href="{{ route('rankings.index') }}">Rankings</a><a href="{{ route('companies.index') }}">Empresas</a><a href="{{ route('pages.como-elaboramos-los-rankings') }}">Cómo elaboramos los rankings</a><a href="{{ route('pages.anade-tu-empresa') }}">Añade tu empresa</a>@auth<a href="{{ route('account.index') }}">Mi cuenta</a>@else<a href="{{ route('login.spanish') }}">Acceder</a>@endauth</div></details>
        </div>
    </header>
    <main>@yield('content')</main>
    <footer class="site-footer">
        <div class="container"><div class="footer-grid">
            <div><a class="logo" href="{{ route('home') }}">negocios almería</a><p>Rankings independientes de empresas digitales de Almería. Revisados a mano.</p></div>
            <div><h2 class="footer-title">Rankings</h2><ul class="footer-links">@foreach($navCategories->take(6) as $category)<li><a href="{{ route('rankings.show', $category->slug) }}">{{ $category->name_plural }}</a></li>@endforeach</ul></div>
            <div><h2 class="footer-title">Municipios</h2><ul class="footer-links">@foreach(['Almería','Roquetas de Mar','El Ejido','Vícar','Níjar','Huércal de Almería'] as $municipality)<li><a href="{{ route('companies.index', ['municipio' => Str::slug($municipality)]) }}">{{ $municipality }}</a></li>@endforeach</ul></div>
            <div><h2 class="footer-title">El directorio</h2><ul class="footer-links"><li><a href="{{ route('pages.como-elaboramos-los-rankings') }}">Cómo elaboramos los rankings</a></li><li><a href="{{ route('pages.sobre-negocios-almeria') }}">Sobre Negocios Almería</a></li><li><a href="{{ route('pages.anade-tu-empresa') }}">Añade tu empresa</a></li><li><a href="{{ route('pages.anunciate') }}">Anúnciate</a></li><li><a href="{{ route('pages.contacto') }}">Contacto</a></li></ul></div>
        </div><div class="footer-bottom"><span>© {{ date('Y') }} Negocios Almería · Hecho en Almería</span><span><a href="{{ route('pages.aviso-legal') }}">Aviso legal</a> · <a href="{{ route('pages.privacidad') }}">Privacidad</a> · <a href="{{ route('pages.cookies') }}">Cookies</a> · <button type="button" class="footer-cookie-settings" data-cookie-settings>Configurar cookies</button> · <a href="{{ route('pages.condiciones') }}">Condiciones</a></span></div></div>
    </footer>
    <x-cookie-consent />
</body>
</html>
