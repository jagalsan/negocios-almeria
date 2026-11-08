@php
$title = $company['seo']['title'];
$description = $company['seo']['description'];
$canonical = $company['url'];
$robots = $company['seo']['robots'];
$ogImage = $company['cover'];
$municipalityUrl = $company['municipalitySlug'] ? route('companies.index', ['municipio' => $company['municipalitySlug']]) : null;
$schema = [
    \App\Support\Seo::webPage('ProfilePage', $canonical, $title, $description, ['breadcrumb' => ['@id' => $canonical.'#breadcrumb'], 'mainEntity' => ['@id' => $canonical.'#business'], 'abstract' => $company['summary'], 'dateModified' => $company['updatedAt']]),
    [
        '@type' => 'ProfessionalService',
        '@id' => $canonical.'#business',
        'name' => $company['name'],
        'url' => $company['website'],
        'description' => $company['shortDescription'],
        'image' => $company['cover'] ?? $company['logo'],
        'logo' => $company['logo'],
        'telephone' => $company['phone'],
        'email' => $company['email'],
        'foundingDate' => $company['foundedYear'] ? (string) $company['foundedYear'] : null,
        'address' => ['@type' => 'PostalAddress', 'streetAddress' => $company['address'], 'postalCode' => $company['postalCode'], 'addressLocality' => $company['municipality'], 'addressRegion' => 'Almería', 'addressCountry' => 'ES'],
        'geo' => $company['lat'] && $company['lng'] ? ['@type' => 'GeoCoordinates', 'latitude' => (float) $company['lat'], 'longitude' => (float) $company['lng']] : null,
        'areaServed' => ['@type' => 'AdministrativeArea', 'name' => 'Provincia de Almería'],
        'knowsAbout' => array_values($company['services']),
        'sameAs' => collect($company['social'])->flatten()->filter(fn ($value) => is_string($value) && str_starts_with($value, 'http'))->values()->all(),
    ],
    \App\Support\Seo::breadcrumbs($canonical, array_values(array_filter([['Empresas', route('companies.index')], $municipalityUrl ? [$company['municipality'], $municipalityUrl] : null, [$company['name'], null]]))),
];
@endphp
@extends('layouts.app')
@section('content')
<div class="container breadcrumbs"><ol><li><a href="{{ route('home') }}">Inicio</a></li><li><a href="{{ route('companies.index') }}">Empresas</a></li>@if($municipalityUrl)<li><a href="{{ $municipalityUrl }}">{{ $company['municipality'] }}</a></li>@endif<li>{{ $company['name'] }}</li></ol></div>
<header class="page-heading"><div class="container company-heading"><div class="logo-box logo-box-large">{{ Str::upper(Str::substr($company['name'],0,2)) }}</div><div><h1>{{ $company['name'] }}</h1><p class="muted">{{ $company['categories'][0]['name'] ?? 'Empresa digital' }} en {{ $company['municipality'] }}</p><span class="badge {{ $company['verified'] ? 'badge-verified' : '' }}">{{ $company['verified'] ? 'Ficha verificada' : 'Ficha no reclamada' }}</span></div><div class="row-actions">@if($company['website'])<a class="button" href="{{ $company['website'] }}" rel="noopener">Visitar web</a>@endif @if($company['phone'])<a class="button button-secondary" href="tel:{{ $company['phone'] }}">Llamar</a>@endif</div></div></header>
@if(count($company['rankings']))
<section class="ranking-memberships section-tint" aria-labelledby="ranking-memberships-title">
    <div class="container">
        <h2 id="ranking-memberships-title">Presencia en rankings</h2>
        <div class="ranking-membership-list">
            @foreach($company['rankings'] as $ranking)
            <div class="ranking-membership-item">
                <a class="ranking-membership" href="{{ $ranking['url'] }}">
                    <span class="ranking-membership-position"><small>Posición</small><strong>Nº {{ $ranking['position'] }}</strong><x-ranking-movement :movement="$ranking['movement']" :label="$ranking['movementLabel']" /></span>
                    <span class="ranking-membership-title">{{ $ranking['title'] }}</span>
                    <span class="ranking-membership-action">Ver ranking</span>
                </a>
                @if(count($ranking['history']) > 1)
                <details class="ranking-history">
                    <summary>Ver histórico de posiciones</summary>
                    <ol>
                        @foreach(array_slice($ranking['history'], 0, 6) as $snapshot)
                        <li><time datetime="{{ $snapshot['recordedOn'] }}">{{ $snapshot['label'] }}</time><strong>Nº {{ $snapshot['position'] }}</strong></li>
                        @endforeach
                    </ol>
                </details>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
<section class="section"><div class="container company-grid"><div><h2 class="section-title">Sobre {{ $company['name'] }}</h2><p class="lead">{{ $company['summary'] }}</p><div class="prose">{!! $company['descriptionHtml'] !!}</div>@if(count($company['services']))<h2 class="h2">Servicios</h2><ul class="service-list">@foreach($company['services'] as $service)<li>{{ $service }}</li>@endforeach</ul>@endif @if(count($company['sectors']))<h2 class="h2">Sectores con los que trabaja</h2><ul class="service-list">@foreach($company['sectors'] as $sector)<li>{{ $sector }}</li>@endforeach</ul>@endif</div><aside><h2 class="section-title">Datos</h2><dl class="data-list">@if($company['foundedYear'])<div><dt>Fundada</dt><dd>{{ $company['foundedYear'] }}</dd></div>@endif @if($company['teamSize'])<div><dt>Equipo</dt><dd>{{ $company['teamSize'] }} personas</dd></div>@endif @if($company['googleRating'])<div><dt>Google</dt><dd>{{ $company['googleRating'] }}/5 · {{ $company['googleReviews'] }} reseñas</dd></div>@endif <div><dt>Municipio</dt><dd>{{ $company['municipality'] }}</dd></div>@if($company['address'])<div><dt>Dirección</dt><dd>{{ $company['address'] }}, {{ $company['postalCode'] }}</dd></div>@endif @if($company['email'])<div><dt>Email</dt><dd><a href="mailto:{{ $company['email'] }}">{{ $company['email'] }}</a></dd></div>@endif</dl></aside></div></section>
<section class="section section-tint"><div class="container heading-grid"><div><h2 class="section-title">{{ $company['verified'] ? '¿Ves un error?' : '¿Es tu empresa?' }}</h2><p>{{ $company['verified'] ? 'Avísanos para que podamos comprobar y corregir la información.' : 'Hemos creado esta ficha con información pública. Gestiónala gratis para completarla.' }}</p></div><div>@if($company['verified'])<a class="button button-secondary" href="{{ route('pages.contacto', ['motivo'=>'correccion']) }}">Solicitar una corrección</a>@else<a class="button" href="{{ auth()->check() ? route('account.claims.create', $company['slug']) : route('login.spanish', ['redirect'=>route('account.claims.create',$company['slug'])]) }}">Reclamar mi ficha</a>@endif</div></div></section>
<div class="container" style="padding-block:32px"><small class="muted">Ficha actualizada el {{ \Carbon\Carbon::parse($company['updatedAt'])->translatedFormat('j \d\e F \d\e Y') }}</small></div>
@endsection
