@extends('layouts.app', ['title' => ($page->seo_title ?: $page->title.' · Negocios Almería'), 'description' => $page->seo_description])
@section('content')
<div class="container breadcrumbs"><ol><li><a href="{{ route('home') }}">Inicio</a></li><li>{{ $page->title }}</li></ol></div>
<article class="section legal-page">
    <div class="container legal-layout">
        <aside class="legal-nav" aria-label="Documentos legales">
            <h2>Información legal</h2>
            <a href="{{ route('pages.aviso-legal') }}" @class(['is-current' => $page->slug === 'aviso-legal'])>Aviso legal</a>
            <a href="{{ route('pages.privacidad') }}" @class(['is-current' => $page->slug === 'privacidad'])>Privacidad</a>
            <a href="{{ route('pages.cookies') }}" @class(['is-current' => $page->slug === 'cookies'])>Cookies</a>
            <a href="{{ route('pages.condiciones') }}" @class(['is-current' => $page->slug === 'condiciones'])>Condiciones de uso</a>
            <button type="button" class="legal-cookie-button" data-cookie-settings>Configurar cookies</button>
        </aside>
        <div class="legal-content">
            <h1 class="h1 h1-ranking">{{ $page->title }}</h1>
            <p class="legal-updated">Última actualización: {{ $page->updated_at->translatedFormat('j \d\e F \d\e Y') }}</p>
            <div class="prose">{!! $page->body !!}</div>
        </div>
    </div>
</article>
@endsection
