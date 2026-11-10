<?php

use App\Models\Category;
use App\Models\Company;
use App\Models\Municipality;

function seoCompany(): Company
{
    $municipality = Municipality::create(['name' => 'Roquetas de Mar', 'slug' => 'roquetas-de-mar']);
    $category = Category::create(['name' => 'Agencia SEO', 'name_plural' => 'Agencias SEO', 'slug' => 'agencias-seo', 'is_published' => true]);
    $company = Company::create(['name' => 'Empresa Prueba', 'status' => 'published', 'origin' => 'admin', 'website_url' => 'https://empresa-prueba.es', 'short_description' => 'Agencias SEO en Roquetas.', 'description' => 'Texto de prueba.', 'services' => ['SEO local', 'Auditorías'], 'municipality_id' => $municipality->id]);
    $company->categories()->attach($category, ['is_primary' => true]);

    return $company;
}

function jsonLdGraph($response): array
{
    preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $response->getContent(), $matches);
    expect($matches[1])->toHaveCount(1);

    return json_decode($matches[1][0], true, flags: JSON_THROW_ON_ERROR)['@graph'];
}

it('genera title, description y schema de la ficha de empresa', function () {
    $company = seoCompany();

    $response = $this->get(route('companies.show', $company->slug))->assertOk()
        ->assertSee('<title>Empresa Prueba: agencia SEO en Roquetas de Mar</title>', false)
        ->assertSee('content="Empresa Prueba es una agencia SEO en Roquetas de Mar (Almería). Servicios: SEO local, Auditorías. Consulta su ficha, web y contacto."', false)
        ->assertSee('<link rel="canonical" href="'.route('companies.show', $company->slug).'">', false)
        ->assertSee('property="og:image"', false);

    expect(collect(jsonLdGraph($response))->pluck('@type')->all())
        ->toContain('Organization', 'WebSite', 'ProfilePage', 'ProfessionalService', 'BreadcrumbList');
});

it('indexa las páginas de municipio y desindexa los filtros por especialidad', function () {
    seoCompany();
    $url = route('companies.index', ['municipio' => 'roquetas-de-mar']);

    $this->get($url)->assertOk()
        ->assertSee('Empresas digitales en Roquetas de Mar</h1>', false)
        ->assertSee('<link rel="canonical" href="'.e($url).'">', false)
        ->assertDontSee('noindex', false);
    $this->get(route('companies.index', ['categoria' => 'agencias-seo']))->assertRedirect(route('categories.show', 'agencias-seo'));
    $this->get(route('companies.index', ['categoria' => 'agencias-seo', 'municipio' => 'roquetas-de-mar']))->assertOk()->assertSee('content="noindex,follow"', false);
    $this->get(route('sitemap'))->assertOk()->assertSee($url, false);
});

it('publica un único grafo JSON-LD válido en la portada', function () {
    $types = collect(jsonLdGraph($this->get(route('home'))->assertOk()))->pluck('@type')->all();

    expect($types)->toContain('Organization', 'WebSite', 'WebPage', 'ItemList');
});
