<?php

use App\Models\Category;
use App\Models\Ranking;
use App\Models\User;
use Illuminate\Validation\ValidationException;

it('bloquea publicar rankings incompletos', function () {
    $category = Category::create(['name' => 'Agencia', 'name_plural' => 'Agencias', 'slug' => 'agencias', 'is_published' => true]);
    $author = User::factory()->create(['role' => 'editor']);

    expect(fn () => Ranking::create(['category_id' => $category->id, 'title' => 'Ranking incompleto', 'slug' => 'ranking-incompleto', 'h1' => 'Ranking incompleto', 'answer' => str_repeat('Respuesta ', 10), 'primary_keyword' => 'ranking incompleto almería', 'intro' => '<p>Contenido</p>', 'criteria' => ['Uno'], 'faq' => [], 'closing_text' => 'Cierre', 'author_id' => $author->id, 'status' => 'published']))->toThrow(ValidationException::class);
});
