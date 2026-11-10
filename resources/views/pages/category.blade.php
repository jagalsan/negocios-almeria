@php
$plural = $category->name_plural;
$lower = \App\Support\Seo::lowerFirst($plural);
$year = date('Y');
$count = $companies->count();
$canonical = route('categories.show', $category->slug);
$title = \App\Support\Seo::title("{$plural} en Almería ({$year}): listado completo · Negocios Almería", "{$plural} en Almería ({$year}): listado completo", "{$plural} en Almería ({$year})", "{$plural} en Almería");
$description = \App\Support\Seo::description("Listado de las {$count} {$lower} de la provincia de Almería con web propia: municipio, servicios y puesto en el ranking. Las 10 mejores, en nuestro ranking.");
$byPlace = $companies->groupBy(fn ($company) => $company->municipality->name)->map->count()->sortDesc();
$places = $byPlace->map(fn ($n, $name) => $n.' en '.($name === 'Almería' ? 'Almería capital' : $name))->values();
$placesText = $places->count() > 1 ? $places->slice(0, -1)->join(', ').' y '.$places->last() : $places->first();
$ranked = $positions->count();
$schema = [
    \App\Support\Seo::webPage('CollectionPage', $canonical, $title, $description, ['breadcrumb' => ['@id' => $canonical.'#breadcrumb'], 'mainEntity' => ['@id' => $canonical.'#list'], 'about' => ['@type' => 'Thing', 'name' => $plural.' en Almería'], 'spatialCoverage' => ['@type' => 'AdministrativeArea', 'name' => 'Provincia de Almería']]),
    ['@type' => 'ItemList', '@id' => $canonical.'#list', 'name' => $plural.' en Almería', 'numberOfItems' => $count, 'itemListElement' => $companies->values()->map(fn ($company, $index) => ['@type' => 'ListItem', 'position' => $index + 1, 'name' => $company->name, 'url' => route('companies.show', $company->slug)])->all()],
    \App\Support\Seo::breadcrumbs($canonical, [['Empresas', route('companies.index')], [$plural, null]]),
];
@endphp
@extends('layouts.app')
@section('content')
<div class="container breadcrumbs"><ol><li><a href="{{ route('home') }}">Inicio</a></li><li><a href="{{ route('companies.index') }}">Empresas</a></li><li>{{ $plural }}</li></ol></div>
<header class="page-heading"><div class="container">
    <h1 class="h1 h1-ranking">{{ $plural }} en Almería</h1>
    <div class="direct-answer">En Negocios Almería hay {{ $count }} {{ $lower }} de la provincia de Almería con web propia.@if($ranking) Aparecen por su puesto en nuestro ranking editorial: las {{ min($ranked, \App\Support\RankingPositions::TOP) }} primeras forman <a href="{{ route('rankings.show', $ranking->slug) }}">el ranking de las mejores {{ $lower }} en Almería</a> y el resto va a continuación.@endif</div>
    <p class="muted">En resumen: de las {{ $count }} {{ $lower }} del directorio, {{ $placesText }}.@if($ranking) {{ $ranked }} tienen puesto en el ranking y cualquiera puede subir hasta el top {{ \App\Support\RankingPositions::TOP }}.@endif</p>
</div></header>
<section class="section"><div class="container">
    <div class="company-list">
    @foreach($companies as $company)
        @php($position = $positions->get($company->id))
        <a class="company-list-row" href="{{ route('companies.show', $company->slug) }}">
            <span class="logo-box">{{ Str::upper(Str::substr($company->name, 0, 2)) }}</span>
            <span><h2>{{ $company->name }}</h2><span class="muted">{{ $company->short_description }}</span>
            @if($position)<br><span class="badge">{{ $position <= \App\Support\RankingPositions::TOP ? 'Top '.\App\Support\RankingPositions::TOP.' · ' : '' }}Puesto {{ $position }} del ranking</span>@endif</span>
            <span class="muted font-display">{{ $company->municipality->name }}</span>
        </a>
    @endforeach
    </div>
    @if($ranking)<p style="margin-top:32px"><a class="button" href="{{ route('rankings.show', $ranking->slug) }}">Ver las {{ min($ranked, \App\Support\RankingPositions::TOP) }} mejores {{ $lower }} en Almería</a></p>@endif
</div></section>
<div class="container">
<section class="section reading">
    <h2 class="section-title">Cómo ordenamos este listado</h2>
    <p>Solo incluimos empresas con web propia y presencia en la provincia de Almería. El orden sigue el ranking editorial, que decidimos con <a href="{{ route('pages.como-elaboramos-los-rankings') }}">criterios públicos</a> y que no se puede comprar. Las empresas sin puesto aparecen por orden alfabético.</p>
    @if($municipalRankings->isNotEmpty())
    <h2 class="section-title">Rankings por municipio</h2>
    <nav class="category-index" aria-label="Rankings por municipio">@foreach($municipalRankings as $local)<a class="badge" href="{{ route('rankings.show', $local->slug) }}">{{ $plural }} en {{ $local->municipality->name }}</a>@endforeach</nav>
    @endif
    @if($others->isNotEmpty())
    <h2 class="section-title">Otros listados de empresas en Almería</h2>
    <nav class="category-index" aria-label="Otros listados">@foreach($others as $other)<a class="badge" href="{{ route('categories.show', $other->slug) }}">{{ $other->name_plural }}</a>@endforeach</nav>
    @endif
</section>
</div>
@endsection
