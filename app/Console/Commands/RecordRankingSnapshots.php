<?php

namespace App\Console\Commands;

use App\Actions\RecordRankingPositions;
use Carbon\CarbonImmutable;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('rankings:snapshot {--date= : Fecha del histórico en formato YYYY-MM-DD}')]
#[Description('Guarda una instantánea diaria de las posiciones públicas de todos los rankings')]
class RecordRankingSnapshots extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(RecordRankingPositions $action): int
    {
        $date = $this->option('date') ? CarbonImmutable::parse($this->option('date')) : today();
        $total = $action->handle(date: $date);
        $this->info("Se han registrado {$total} posiciones para {$date->toDateString()}.");

        return self::SUCCESS;
    }
}
