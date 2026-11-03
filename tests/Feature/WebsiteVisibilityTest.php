<?php

use App\Models\Category;
use App\Models\Company;
use App\Models\Municipality;

it('no publica fichas sin una web oficial', function () {
    $municipality = Municipality::create(['name' => 'Almería', 'slug' => 'almeria']);
    $category = Category::create(['name' => 'Agencia', 'name_plural' => 'Agencias', 'slug' => 'agencias', 'is_published' => true]);
    $company = Company::create(['name' => 'Empresa sin web', 'status' => 'published', 'origin' => 'admin', 'short_description' => 'Empresa pendiente de web oficial.', 'description' => 'Información pendiente de completar.', 'municipality_id' => $municipality->id]);
    $company->categories()->attach($category, ['is_primary' => true]);

    expect(Company::published()->count())->toBe(0);
    $this->get(route('companies.index'))->assertOk()->assertDontSee($company->name);
});
