@php
$base = $page->seo_title && substr_count($page->seo_title, \App\Support\Seo::SITE_NAME) < 2 ? $page->seo_title : $page->title;
$title = str_contains($base, \App\Support\Seo::SITE_NAME) ? $base : \App\Support\Seo::title($base.' · '.\App\Support\Seo::SITE_NAME, $base);
$description = \App\Support\Seo::description($page->seo_description);
$canonical = url($page->slug);
$type = ['sobre-negocios-almeria' => 'AboutPage', 'contacto' => 'ContactPage'][$page->slug] ?? 'WebPage';
$schema = [
    \App\Support\Seo::webPage($type, $canonical, $title, $description, ['breadcrumb' => ['@id' => $canonical.'#breadcrumb'], 'dateModified' => $page->updated_at?->toIso8601String()]),
    \App\Support\Seo::breadcrumbs($canonical, [[$page->title, null]]),
];
@endphp
@extends('layouts.app')
@section('content')<div class="container breadcrumbs"><ol><li><a href="{{ route('home') }}">Inicio</a></li><li>{{ $page->title }}</li></ol></div><article class="section"><div class="container reading"><h1 class="h1 h1-ranking">{{ $page->title }}</h1><div class="prose" style="margin-top:48px">{!! $page->body !!}</div>@if($page->slug==='anade-tu-empresa')<p style="margin-top:40px"><a class="button" href="{{ auth()->check() ? route('account.companies.create') : route('register.spanish') }}">Crear cuenta</a></p>@endif</div></article>@endsection
