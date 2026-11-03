<?php

use App\Actions\ImportRankingsSpreadsheet;
use App\Models\Category;
use App\Models\Company;
use App\Models\Ranking;
use App\Models\User;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

it('importa empresas y posiciones del excel como borradores', function () {
    Category::create(['name' => 'Agencia de marketing', 'name_plural' => 'Agencias de marketing', 'slug' => 'agencias-de-marketing', 'is_published' => true]);
    User::factory()->create(['role' => 'admin']);
    $spreadsheet = new Spreadsheet;
    $rankings = $spreadsheet->getActiveSheet()->setTitle('Rankings');
    $rankings->fromArray([
        ['Categoría', 'Posición', 'Empresa', 'Web', 'Municipio', 'Estado', 'Dato destacado (para el motivo)', 'Fuente'],
        ['Agencias de marketing', 1, 'Empresa importada', 'https://empresa-importada.test', 'Almería', 'Verificada', 'Caso verificable incluido en la fuente.', 'https://fuente.test'],
    ]);
    $profiles = new Worksheet($spreadsheet, 'Fichas n.º 1');
    $profiles->fromArray([
        ['Campo', 'Empresa importada'],
        ['Nombre', 'Empresa importada'],
        ['Web', 'https://empresa-importada.test'],
        ['Municipio', 'Almería'],
        ['Categoría principal', 'Agencias de marketing'],
        ['Descripción breve (≤160)', 'Agencia de marketing de prueba en Almería.'],
        ['Descripción completa (borrador)', 'Descripción obtenida del archivo editorial y pendiente de revisión antes de su publicación.'],
    ]);
    $spreadsheet->addSheet($profiles);
    $path = tempnam(sys_get_temp_dir(), 'rankings-').'.xlsx';
    (new Xlsx($spreadsheet))->save($path);

    try {
        $result = app(ImportRankingsSpreadsheet::class)->handle($path);
    } finally {
        @unlink($path);
    }

    expect($result['companies'])->toBe(1)
        ->and($result['rankings'])->toBe(1)
        ->and($result['entries'])->toBe(1)
        ->and(Company::first()->status->value)->toBe('draft')
        ->and(Ranking::first()->status)->toBe('draft')
        ->and(Ranking::first()->entries()->value('position'))->toBe(1);
});
