<?php

namespace App\Console\Commands;

use App\Actions\ImportRankingsSpreadsheet as ImportAction;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Throwable;

#[Signature('rankings:import {path : Ruta del archivo XLSX} {--replace-examples : Sustituye las entradas ficticias y archiva sus empresas}')]
#[Description('Importa empresas y posiciones de ranking desde el Excel de Negocios Almería')]
class ImportRankingsSpreadsheet extends Command
{
    public function handle(ImportAction $action): int
    {
        try {
            $result = $action->handle(
                path: $this->argument('path'),
                replaceExamples: (bool) $this->option('replace-examples'),
            );
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->table(['Empresas', 'Rankings', 'Posiciones', 'Ejemplos archivados'], [[
            $result['companies'],
            $result['rankings'],
            $result['entries'],
            $result['archivedExamples'],
        ]]);

        foreach ($result['duplicateDomains'] as $duplicate) {
            $this->warn("{$duplicate['company']} comparte {$duplicate['domain']} con {$duplicate['usedBy']}; se ha conservado como referencia, sin asignarlo como dominio único.");
        }

        $this->info('Importación terminada. Todas las empresas y rankings importados quedan en borrador.');

        return self::SUCCESS;
    }
}
