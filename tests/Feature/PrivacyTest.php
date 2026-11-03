<?php

use App\Models\Category;
use App\Models\Company;
use App\Models\Municipality;
use App\Models\Ownership;
use App\Models\User;
use Illuminate\Support\Str;

function publishedCompany(string $name, Municipality $municipality, Category $category): Company
{
    $company = Company::create(['name' => $name, 'slug' => Str::slug($name), 'website_url' => 'https://'.Str::slug($name).'.test', 'status' => 'published', 'origin' => 'admin', 'short_description' => 'Empresa ficticia para comprobar la privacidad del directorio.', 'description' => str_repeat('Descripción de ejemplo sin datos personales. ', 10), 'municipality_id' => $municipality->id, 'published_at' => now()]);
    $company->categories()->attach($category, ['is_primary' => true]);

    return $company;
}

it('nunca expone datos del propietario en la ficha ni en la api pública', function () {
    $municipality = Municipality::create(['name' => 'Almería', 'slug' => 'almeria']);
    $category = Category::create(['name' => 'Agencia', 'name_plural' => 'Agencias', 'slug' => 'agencias', 'is_published' => true]);
    $company = publishedCompany('Empresa privada ejemplo', $municipality, $category);
    $owner = User::factory()->create(['name' => 'Nombre Privado Único', 'email' => 'privado@example.test', 'phone' => '600123456']);
    Ownership::create(['company_id' => $company->id, 'user_id' => $owner->id, 'approved_at' => now()]);

    $this->get(route('companies.show', $company->slug))->assertOk()->assertDontSee($owner->name)->assertDontSee($owner->email)->assertDontSee($owner->phone);
    $this->getJson('/api/v1/companies/'.$company->slug)->assertOk()->assertJsonMissing(['owner' => $owner->name])->assertDontSee($owner->email)->assertDontSee('created_by')->assertDontSee('cif');
});

it('devuelve las mismas claves públicas con y sin propietario', function () {
    $municipality = Municipality::create(['name' => 'Almería', 'slug' => 'almeria']);
    $category = Category::create(['name' => 'Agencia', 'name_plural' => 'Agencias', 'slug' => 'agencias', 'is_published' => true]);
    $owned = publishedCompany('Empresa propiedad ejemplo', $municipality, $category);
    $unowned = publishedCompany('Empresa libre ejemplo', $municipality, $category);
    $owner = User::factory()->create();
    Ownership::create(['company_id' => $owned->id, 'user_id' => $owner->id, 'approved_at' => now()]);

    expect(array_keys($this->getJson('/api/v1/companies/'.$owned->slug)->json()))->toBe(array_keys($this->getJson('/api/v1/companies/'.$unowned->slug)->json()));
});
