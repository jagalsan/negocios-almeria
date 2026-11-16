@php
$plural = $category->name_plural;
$lower = \App\Support\Seo::lowerFirst($plural);
[$topic, $scope] = config('directory.topics.'.$category->slug, [$lower, null]);
$count = $companies->count();
$top = min($positions->count(), \App\Support\RankingPositions::TOP);
$canonical = route('categories.show', $category->slug);
$title = \App\Support\Seo::title("Directorio de {$topic} en Almería por municipio", "Directorio de {$topic} en Almería", "Directorio de {$topic}");
$meta = "{$count} {$topic} de la provincia de Almería, por municipio y con sus servicios.";
$description = \App\Support\Seo::description($ranking && mb_strlen($meta) < 110 ? $meta." ¿Buscas las mejores? Mira el ranking de las {$top}." : $meta);
$groups = $companies->groupBy(fn ($company) => $company->municipality->slug)->sortByDesc(fn ($group) => $group->count());
$placeName = fn ($municipality) => $municipality->name === 'Almería' ? 'Almería capital' : $municipality->name;
$places = $groups->map(fn ($group) => $group->count().' en '.$placeName($group->first()->municipality))->values();
$placesText = $places->count() > 1 ? $places->slice(0, -1)->join(', ').' y '.$places->last() : $places->first();
$placeholder = '/^(Agencia|Empresa|Startup|Productora|Estudio|Consultora|Coworking|Centro)\b[^.]{0,80} en [^.]+\.$/u';
$blurb = fn ($company) => (preg_match($placeholder, $company->short_description) || mb_strlen($company->short_description) < 50) && filled($company->services)
    ? implode(', ', array_slice($company->services, 0, 4)).' en '.$placeName($company->municipality).'.'
    : $company->short_description;
$schema = [
    \App\Support\Seo::webPage('CollectionPage', $canonical, $title, $description, ['breadcrumb' => ['@id' => $canonical.'#breadcrumb'], 'mainEntity' => ['@id' => $canonical.'#list'], 'spatialCoverage' => ['@type' => 'AdministrativeArea', 'name' => 'Provincia de Almería']]),
    ['@type' => 'ItemList', '@id' => $canonical.'#list', 'name' => "Directorio de {$topic} en Almería", 'numberOfItems' => $count, 'itemListElement' => $companies->values()->map(fn ($company, $index) => ['@type' => 'ListItem', 'position' => $index + 1, 'name' => $company->name, 'url' => route('companies.show', $company->slug)])->all()],
    \App\Support\Seo::breadcrumbs($canonical, [['Empresas', route('companies.index')], ["Directorio de {$topic}", null]]),
];
@endphp
@extends('layouts.app')
@section('content')
<div class="container breadcrumbs"><ol><li><a href="{{ route('home') }}">Inicio</a></li><li><a href="{{ route('companies.index') }}">Empresas</a></li><li>Directorio de {{ $topic }}</li></ol></div>
<header class="page-heading"><div class="container">
    <h1 class="h1 h1-ranking">Directorio de {{ $topic }} en Almería</h1>
    <p class="lead">Este directorio reúne {{ $count }} {{ $topic }} con web propia y sede u oficina en la provincia de Almería{{ $scope ? ': '.$scope : '' }}. Las agrupamos por municipio para que encuentres proveedores cercanos: {{ $placesText }}. Solo incluimos empresas con web activa y presencia comprobable en la provincia. Cada ficha recoge servicios, municipio y contacto, y si la empresa tiene puesto en nuestro ranking editorial lo indicamos junto a su nombre.</p>
    @if($ranking)<p><a class="button" href="{{ route('rankings.show', $ranking->slug) }}">Ver el ranking de las {{ $top }} mejores</a></p>@endif
</div></header>
<div class="container">
<section class="section">
    <h2 class="section-title">Municipios</h2>
    <nav class="category-index" aria-label="Municipios">@foreach($groups as $slug => $group)<a class="badge" href="#{{ $slug }}">{{ $placeName($group->first()->municipality) }} ({{ $group->count() }})</a>@endforeach</nav>
    @foreach($groups as $slug => $group)
    <h2 class="section-title" id="{{ $slug }}">{{ ucfirst($topic) }} en {{ $placeName($group->first()->municipality) }}</h2>
    <div class="company-list">
        @foreach($group as $company)
        @php($position = $positions->get($company->id))
        <a class="company-list-row" href="{{ route('companies.show', $company->slug) }}">
            <span class="logo-box">{{ Str::upper(Str::substr($company->name, 0, 2)) }}</span>
            <span><h3>{{ $company->name }}</h3><span class="muted">{{ $blurb($company) }}</span>@if($position)<br><span class="badge">Puesto {{ $position }} en el ranking</span>@endif</span>
            <span class="muted font-display">{{ $company->municipality->name }}</span>
        </a>
        @endforeach
    </div>
    @endforeach
</section>
@if($municipalRankings->isNotEmpty() || $others->isNotEmpty())
<section class="section reading">
    @if($municipalRankings->isNotEmpty())
    <h2 class="section-title">Rankings locales</h2>
    <nav class="category-index" aria-label="Rankings locales">@foreach($municipalRankings as $local)<a class="badge" href="{{ route('rankings.show', $local->slug) }}">{{ $plural }} en {{ $local->municipality->name }}</a>@endforeach</nav>
    @endif
    @if($others->isNotEmpty())
    <h2 class="section-title">Otros directorios</h2>
    <nav class="category-index" aria-label="Otros directorios">@foreach($others as $other)<a class="badge" href="{{ route('categories.show', $other->slug) }}">{{ ucfirst(config('directory.topics.'.$other->slug.'.0', \App\Support\Seo::lowerFirst($other->name_plural))) }}</a>@endforeach</nav>
    @endif
</section>
@endif
</div>
@endsection
