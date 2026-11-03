<?php

use App\Actions\UpdateOwnedCompany;
use App\Models\Category;
use App\Models\Municipality;
use App\Models\Ownership;
use App\Models\User;

it('impide editar otra empresa y no permite cambiar campos reservados', function () {
    $municipality = Municipality::create(['name' => 'Almería', 'slug' => 'almeria']);
    $category = Category::create(['name' => 'Agencia', 'name_plural' => 'Agencias', 'slug' => 'agencias', 'is_published' => true]);
    $company = publishedCompany('Empresa permisos ejemplo', $municipality, $category);
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    Ownership::create(['company_id' => $company->id, 'user_id' => $owner->id, 'approved_at' => now()]);

    $this->actingAs($stranger)->patch(route('account.companies.update', $company->uuid), ['short_description' => str_repeat('x', 30), 'description' => str_repeat('texto ', 30)])->assertForbidden();
    app(UpdateOwnedCompany::class)->handle($owner, $company, ['name' => 'Nombre manipulado', 'slug' => 'manipulado', 'google_rating' => 1, 'short_description' => 'Descripción permitida actualizada.']);

    expect($company->fresh()->name)->toBe('Empresa permisos ejemplo')
        ->and($company->fresh()->slug)->toBe('empresa-permisos-ejemplo')
        ->and($company->fresh()->google_rating)->toBeNull()
        ->and($company->fresh()->short_description)->toBe('Descripción permitida actualizada.');
});
