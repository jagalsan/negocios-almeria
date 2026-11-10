<?php

namespace App\Support;

use App\Enums\CompanyStatus;
use App\Models\Company;
use App\Models\Ranking;
use App\Models\RankingPositionHistory;
use Illuminate\Support\Collection;

class RankingPositions
{
    public const TOP = 10;

    public static function visibleEntries(Ranking $ranking): Collection
    {
        $ranking->loadMissing('entries.company');

        return $ranking->entries
            ->filter(fn ($entry) => $entry->company->status === CompanyStatus::Published && filled($entry->company->website_url))
            ->sortBy('position')
            ->values();
    }

    public static function topEntries(Ranking $ranking): Collection
    {
        return self::visibleEntries($ranking)->take(self::TOP);
    }

    public static function forCompany(Ranking $ranking, Company $company): ?array
    {
        $entries = self::visibleEntries($ranking);
        $index = $entries->search(fn ($entry) => $entry->company_id === $company->id);

        if ($index === false) {
            return null;
        }

        $position = $index + 1;
        $history = RankingPositionHistory::query()
            ->where('ranking_id', $ranking->id)
            ->where('company_id', $company->id)
            ->latest('recorded_on')
            ->limit(12)
            ->get();
        $latest = $history->first();
        $comparison = $latest && $latest->recorded_on->isToday() && $latest->position === $position
            ? $history->get(1)
            : $latest;
        $movement = $comparison ? $comparison->position - $position : 0;

        return [
            'position' => $position,
            'previousPosition' => $comparison?->position,
            'movement' => $movement,
            'movementLabel' => $movement > 0 ? "Sube {$movement}" : ($movement < 0 ? 'Baja '.abs($movement) : 'Sin cambios'),
            'history' => $history->map(fn ($snapshot) => [
                'position' => $snapshot->position,
                'recordedOn' => $snapshot->recorded_on->toDateString(),
                'label' => $snapshot->recorded_on->translatedFormat('j M Y'),
            ])->values()->all(),
        ];
    }
}
