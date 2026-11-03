<?php

use App\Actions\RecordRankingPositions;
use App\Models\Category;
use App\Models\Company;
use App\Models\Municipality;
use App\Models\Ranking;
use App\Models\RankingEntry;
use App\Models\User;
use App\Support\RankingPositions;

function rankingHistoryFixture(): array
{
    $municipality = Municipality::create(['name' => 'Almería', 'slug' => 'almeria']);
    $category = Category::create(['name' => 'Consultora IT', 'name_plural' => 'Consultoras IT', 'slug' => 'consultoras-it', 'is_published' => true]);
    $author = User::factory()->create(['role' => 'editor']);
    $ranking = Ranking::create(['category_id' => $category->id, 'title' => 'Ranking de prueba', 'slug' => 'ranking-prueba', 'h1' => 'Ranking de prueba', 'answer' => str_repeat('Respuesta ', 10), 'primary_keyword' => 'consultoras it almería', 'intro' => '<p>Contenido</p>', 'criteria' => ['Uno', 'Dos', 'Tres'], 'faq' => array_fill(0, 4, ['q' => 'Pregunta', 'a' => 'Respuesta']), 'closing_text' => 'Cierre', 'author_id' => $author->id, 'status' => 'draft']);
    $companies = collect(['Empresa A', 'Empresa oculta', 'Empresa B', 'Empresa C', 'Empresa D', 'Empresa E'])->map(function ($name, $index) use ($municipality, $category, $ranking) {
        $visible = $name !== 'Empresa oculta';
        $company = Company::create(['name' => $name, 'website_url' => $visible ? 'https://empresa-'.$index.'.test' : null, 'status' => $visible ? 'published' : 'draft', 'origin' => 'admin', 'short_description' => 'Descripción breve.', 'description' => 'Descripción completa de prueba.', 'municipality_id' => $municipality->id]);
        $company->categories()->attach($category, ['is_primary' => true]);
        RankingEntry::create(['ranking_id' => $ranking->id, 'company_id' => $company->id, 'position' => $index + 1, 'reason' => 'Motivo editorial de prueba.']);

        return $company;
    });
    $ranking->update(['status' => 'published', 'published_at' => now()]);

    return [$ranking, $companies];
}

it('calcula la misma posición pública aunque existan entradas ocultas', function () {
    [$ranking, $companies] = rankingHistoryFixture();

    expect(RankingPositions::forCompany($ranking, $companies[2])['position'])->toBe(2);
});

it('registra y calcula subidas y bajadas entre instantáneas', function () {
    [$ranking, $companies] = rankingHistoryFixture();
    app(RecordRankingPositions::class)->handle($ranking, today()->subDay());
    $first = $ranking->entries()->where('company_id', $companies[0]->id)->firstOrFail();
    $second = $ranking->entries()->where('company_id', $companies[2]->id)->firstOrFail();
    $first->update(['position' => 99]);
    $second->update(['position' => 1]);
    $first->update(['position' => 3]);
    $ranking->unsetRelation('entries');
    app(RecordRankingPositions::class)->handle($ranking, today());

    expect(RankingPositions::forCompany($ranking, $companies[0])['movement'])->toBe(-1)
        ->and(RankingPositions::forCompany($ranking, $companies[2])['movement'])->toBe(1);
});
