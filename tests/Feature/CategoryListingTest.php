<?php

use App\Models\Category;
use App\Models\Company;
use App\Models\Municipality;
use App\Models\Ranking;
use App\Models\RankingEntry;
use App\Models\User;

function listingFixture(): array
{
    $municipality = Municipality::create(['name' => 'Almería', 'slug' => 'almeria']);
    $category = Category::create(['name' => 'Agencia de marketing', 'name_plural' => 'Agencias de marketing', 'slug' => 'agencias-de-marketing', 'is_published' => true]);
    $author = User::factory()->create(['role' => 'editor']);
    $ranking = Ranking::create(['category_id' => $category->id, 'title' => 'Las mejores agencias de marketing en Almería', 'slug' => 'agencias-de-marketing', 'h1' => 'Las 12 mejores agencias de marketing en Almería', 'answer' => str_repeat('Respuesta ', 10), 'primary_keyword' => 'mejores agencias de marketing en almería', 'intro' => '<p>Contenido</p>', 'criteria' => ['Uno', 'Dos', 'Tres'], 'faq' => array_fill(0, 4, ['q' => 'Pregunta', 'a' => 'Respuesta']), 'closing_text' => 'Cierre', 'author_id' => $author->id, 'status' => 'draft']);
    foreach (range(1, 13) as $i) {
        $company = Company::create(['name' => "Agencia {$i}", 'status' => 'published', 'origin' => 'admin', 'website_url' => "https://agencia{$i}.es", 'short_description' => 'Agencia de prueba.', 'description' => 'Texto.', 'municipality_id' => $municipality->id]);
        $company->categories()->attach($category, ['is_primary' => true]);
        if ($i <= 12) {
            RankingEntry::create(['ranking_id' => $ranking->id, 'company_id' => $company->id, 'position' => $i, 'reason' => 'Motivo editorial.']);
        }
    }
    $ranking->update(['status' => 'published', 'published_at' => now(), 'reviewed_at' => now()]);

    return [$category, $ranking];
}

it('muestra solo el top 10 en el ranking y enlaza al listado completo', function () {
    [$category, $ranking] = listingFixture();

    $this->get(route('rankings.show', $ranking->slug))->assertOk()
        ->assertSee('Las 10 mejores agencias de marketing en Almería')
        ->assertSee('"numberOfItems":10', false)
        ->assertSee('Agencia 10')
        ->assertDontSee('Agencia 11')
        ->assertSee(route('categories.show', $category->slug), false);
});

it('lista todas las empresas de la categoría con su puesto en el ranking', function () {
    [$category] = listingFixture();

    $this->get(route('categories.show', $category->slug))->assertOk()
        ->assertSee('Agencias de marketing en Almería</h1>', false)
        ->assertSee('Top 10 · Puesto 1 del ranking')
        ->assertSee('Puesto 12 del ranking')
        ->assertSee('Agencia 13')
        ->assertSee('"numberOfItems":13', false)
        ->assertDontSee('noindex', false)
        ->assertSeeInOrder(['Agencia 1<', 'Agencia 11<', 'Agencia 12<', 'Agencia 13<'], false);

    $this->get(route('companies.index', ['categoria' => $category->slug]))->assertRedirect(route('categories.show', $category->slug));
    $this->get(route('sitemap'))->assertSee(route('categories.show', $category->slug), false);
    $this->get(route('llms'))->assertSee(route('categories.show', $category->slug), false);
    $this->get(route('companies.index'))->assertOk()->assertSee('Agencias de marketing (13)')->assertSee(route('categories.show', $category->slug), false);
    $this->get(route('home'))->assertSee('Todas las categorías');
    $this->get(route('companies.show', 'agencia-1'))->assertOk()->assertSee('href="'.route('categories.show', $category->slug).'"', false);
});
