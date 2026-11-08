@php
$title = $ranking['seo']['title'];
$description = $ranking['seo']['description'];
$canonical = $ranking['seo']['canonical'];
$schema = [
    \App\Support\Seo::webPage('CollectionPage', $canonical, $title, $description, ['breadcrumb' => ['@id' => $canonical.'#breadcrumb'], 'mainEntity' => ['@id' => $canonical.'#list'], 'datePublished' => $ranking['publishedAt'], 'dateModified' => $ranking['reviewedAt'], 'publisher' => ['@id' => route('home').'#organization'], 'author' => ['@id' => route('home').'#organization'], 'about' => ['@type' => 'Thing', 'name' => $ranking['category']['namePlural'].' en Almería'], 'spatialCoverage' => ['@type' => 'AdministrativeArea', 'name' => 'Provincia de Almería'], 'abstract' => $ranking['summary']]),
    ['@type' => 'ItemList', '@id' => $canonical.'#list', 'name' => $ranking['h1'], 'numberOfItems' => count($ranking['entries']), 'itemListOrder' => 'https://schema.org/ItemListOrderAscending', 'itemListElement' => collect($ranking['entries'])->map(fn ($entry) => ['@type' => 'ListItem', 'position' => $entry['position'], 'name' => $entry['company']['name'], 'url' => $entry['company']['url'], 'description' => $entry['reason']])->values()->all()],
    $ranking['faq'] ? ['@type' => 'FAQPage', '@id' => $canonical.'#faq', 'mainEntity' => collect($ranking['faq'])->map(fn ($faq) => ['@type' => 'Question', 'name' => $faq['q'], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['a']]])->values()->all()] : null,
    \App\Support\Seo::breadcrumbs($canonical, [['Rankings', route('rankings.index')], [$ranking['category']['namePlural'], null]]),
];
@endphp
@extends('layouts.app')
@section('content')
<div class="container breadcrumbs"><ol><li><a href="{{ route('home') }}">Inicio</a></li><li><a href="{{ route('rankings.index') }}">Rankings</a></li><li>{{ $ranking['category']['namePlural'] }}</li></ol></div>
<header class="page-heading"><div class="container heading-grid"><div><h1 class="h1 h1-ranking">{{ $ranking['h1'] }}</h1><div class="direct-answer">{{ $ranking['answer'] }}</div>@if($ranking['summary'])<p class="muted">{{ $ranking['summary'] }}</p>@endif</div><div class="heading-meta">Revisado el {{ \Carbon\Carbon::parse($ranking['reviewedAt'])->translatedFormat('j \d\e F \d\e Y') }}<br>Por {{ $ranking['author']['name'] }}<br>{{ count($ranking['entries']) }} empresas</div></div></header>
<div class="container">
@if(count($ranking['sponsored']))
<section class="sponsored">
    <span class="sponsored-label">Patrocinado</span>
    <p class="muted">Estas empresas pagan por aparecer aquí. No influye en el ranking.</p>
    @foreach($ranking['sponsored'] as $sponsor)
    <div class="sponsored-row">
        <span class="logo-box">{{ Str::upper(Str::substr($sponsor['company']['name'], 0, 2)) }}</span>
        <div><strong class="font-display">{{ $sponsor['company']['name'] }}</strong><div>{{ $sponsor['company']['shortDescription'] }}</div></div>
        <a class="button button-secondary button-small" href="{{ $sponsor['company']['website'] }}" rel="sponsored noopener">Visitar web</a>
    </div>
    @endforeach
</section>
@endif
<section>
    <h2 class="section-title">Ranking {{ date('Y') }}</h2>
    <div class="ranking-list">
        @foreach($ranking['entries'] as $entry)
        <article class="ranking-row">
            <div class="ranking-position-block">
                <div class="ranking-position">{{ str_pad($entry['position'], 2, '0', STR_PAD_LEFT) }}</div>
                <x-ranking-movement :movement="$entry['movement']" :label="$entry['movementLabel']" />
            </div>
            <div class="logo-box">{{ Str::upper(Str::substr($entry['company']['name'], 0, 2)) }}</div>
            <div class="ranking-content">
                <h3><a href="{{ $entry['company']['url'] }}">{{ $entry['company']['name'] }}</a></h3>
                <p class="ranking-facts">
                    {{ $entry['company']['municipality'] }}
                    @if($entry['company']['foundedYear'])
                        · Desde {{ $entry['company']['foundedYear'] }}
                    @endif
                    @if($entry['company']['googleRating'])
                        · {{ $entry['company']['googleRating'] }}/5 en Google
                    @endif
                </p>
                <p class="ranking-reason">{{ $entry['reason'] }}</p>
                <div class="row-actions">
                    <a class="button button-secondary button-small" href="{{ $entry['company']['url'] }}">Ver ficha</a>
                    @if($entry['company']['website'])
                    <a class="button button-link button-small" href="{{ $entry['company']['website'] }}" rel="noopener">Visitar web</a>
                    @endif
                </div>
            </div>
        </article>
        @endforeach
    </div>
</section>
<section class="section"><h2 class="section-title">Comparativa</h2><div class="table-wrap"><table><thead><tr><th>Empresa</th><th>Municipio</th><th>Especialidad</th><th>Fundada</th><th>Valoración</th></tr></thead><tbody>@foreach($ranking['entries'] as $entry)<tr><td><a href="{{ $entry['company']['url'] }}">{{ $entry['company']['name'] }}</a></td><td>{{ $entry['company']['municipality'] }}</td><td>{{ $entry['company']['services'][0] ?? '—' }}</td><td>{{ $entry['company']['foundedYear'] ?? '—' }}</td><td>{{ $entry['company']['googleRating'] ? $entry['company']['googleRating'].'/5' : '—' }}</td></tr>@endforeach</tbody></table></div></section>
<section class="section section-tint" style="margin-inline:calc(50% - 50vw);padding-inline:calc(50vw - 50%)"><div class="company-grid"><div><h2 class="section-title">Cómo hemos elegido</h2><ol>@foreach($ranking['criteria'] as $criterion)<li>{{ $criterion }}</li>@endforeach</ol><p><a href="{{ route('pages.como-elaboramos-los-rankings') }}">Lee la metodología completa</a></p></div><div class="heading-meta">El orden lo decide el equipo editorial. Revisamos el ranking al menos cada seis meses.</div></div></section>
<section class="section prose">{!! $ranking['introHtml'] !!}</section>
<section class="section reading"><h2 class="section-title">Preguntas frecuentes</h2><div class="faq">@foreach($ranking['faq'] as $faq)<details><summary>{{ $faq['q'] }}</summary><p>{{ $faq['a'] }}</p></details>@endforeach</div></section>
@if($ranking['related'])<section class="section reading"><h2 class="section-title">Otros rankings de empresas en Almería</h2><ul>@foreach($ranking['related'] as $related)<li><a href="{{ $related['url'] }}">{{ $related['name'] }}</a></li>@endforeach</ul></section>@endif
<section class="section section-tint" style="margin-inline:calc(50% - 50vw);padding-inline:calc(50vw - 50%)"><h2 class="section-title">¿Tu empresa debería estar aquí?</h2><p class="lead">{{ $ranking['closingText'] }}</p><div class="row-actions"><a class="button" href="{{ route('pages.anade-tu-empresa') }}">Añadir mi empresa</a><a class="button button-secondary" href="{{ route('companies.index') }}">Reclamar mi ficha</a></div></section>
</div>
@endsection
