<?php

use App\Models\Category;
use App\Models\Municipality;
use App\Models\Ranking;
use App\Models\Sponsorship;
use App\Models\User;

it('considera activo un patrocinio solo dentro de sus fechas', function () {
    $municipality = Municipality::create(['name' => 'Almería', 'slug' => 'almeria']);
    $category = Category::create(['name' => 'Agencia', 'name_plural' => 'Agencias', 'slug' => 'agencias', 'is_published' => true]);
    $company = publishedCompany('Empresa patrocinio ejemplo', $municipality, $category);
    $author = User::factory()->create(['role' => 'editor']);
    $ranking = Ranking::create(['category_id' => $category->id, 'title' => 'Ranking borrador', 'slug' => 'ranking-borrador', 'h1' => 'Ranking borrador', 'answer' => str_repeat('Respuesta ', 10), 'primary_keyword' => 'ranking borrador almería', 'intro' => '<p>Contenido</p>', 'criteria' => ['Uno', 'Dos', 'Tres'], 'faq' => array_fill(0, 4, ['q' => 'Pregunta', 'a' => 'Respuesta']), 'closing_text' => 'Cierre', 'author_id' => $author->id, 'status' => 'draft']);
    $active = Sponsorship::create(['ranking_id' => $ranking->id, 'company_id' => $company->id, 'slot' => 1, 'starts_at' => now()->subDay(), 'ends_at' => now()->addDay(), 'amount_cents' => 2900, 'status' => 'active']);
    $expired = Sponsorship::create(['ranking_id' => $ranking->id, 'company_id' => $company->id, 'slot' => 2, 'starts_at' => now()->subDays(2), 'ends_at' => now()->subDay(), 'amount_cents' => 2900, 'status' => 'active']);

    expect($active->isActive())->toBeTrue()->and($expired->isActive())->toBeFalse();
});
