@php
$title = \App\Support\Seo::title('Rankings de empresas digitales en Almería ('.date('Y').') · Negocios Almería', 'Rankings de empresas digitales en Almería ('.date('Y').')');
$description = 'Compara agencias SEO, de marketing, desarrollo web, software y startups de Almería en rankings revisados a mano y con criterios públicos.';
$canonical = route('rankings.index');
$schema = [
    \App\Support\Seo::webPage('CollectionPage', $canonical, $title, $description, ['breadcrumb' => ['@id' => $canonical.'#breadcrumb'], 'mainEntity' => ['@id' => $canonical.'#list']]),
    ['@type' => 'ItemList', '@id' => $canonical.'#list', 'name' => 'Rankings de empresas digitales de Almería', 'numberOfItems' => $rankings->count(), 'itemListElement' => $rankings->values()->map(fn ($ranking, $index) => ['@type' => 'ListItem', 'position' => $index + 1, 'name' => $ranking->category->name_plural.' en '.($ranking->municipality?->name ?? 'Almería'), 'url' => route('rankings.show', $ranking->slug)])->all()],
    \App\Support\Seo::breadcrumbs($canonical, [['Rankings', null]]),
];
@endphp
@extends('layouts.app')
@section('content')<header class="page-heading"><div class="container"><h1 class="h1 h1-ranking">Rankings de empresas digitales de Almería</h1><p class="lead">Selecciones independientes, con criterios públicos y revisión semestral.</p></div></header><section class="section section-tint"><div class="container"><h2 class="section-title">Todos los rankings</h2><div class="ranking-index">@foreach($rankings as $ranking)<a class="index-row" href="{{ route('rankings.show',$ranking->slug) }}"><span class="index-number">{{ str_pad($loop->iteration,2,'0',STR_PAD_LEFT) }}</span><span><span class="index-name">{{ $ranking->category->name_plural }}@if($ranking->municipality) en {{ $ranking->municipality->name }}@endif</span><span class="index-companies">{{ \App\Support\RankingPositions::topEntries($ranking)->take(3)->pluck('company.name')->join(', ') }}</span></span><span class="index-meta">{{ \App\Support\RankingPositions::topEntries($ranking)->count() }} empresas<br>Revisado {{ optional($ranking->reviewed_at)->translatedFormat('M Y') }}</span></a>@endforeach</div></div></section>@endsection
