<?php

namespace App\Actions;

use App\Models\Ranking;
use App\Models\RankingPositionHistory;
use App\Support\RankingPositions;
use Carbon\CarbonInterface;

class RecordRankingPositions
{
    public function handle(?Ranking $ranking = null, ?CarbonInterface $date = null): int
    {
        $date ??= today();
        $rankings = $ranking ? collect([$ranking]) : Ranking::published()->get();
        $recorded = 0;

        foreach ($rankings as $currentRanking) {
            foreach (RankingPositions::visibleEntries($currentRanking) as $index => $entry) {
                RankingPositionHistory::updateOrCreate(
                    ['ranking_id' => $currentRanking->id, 'company_id' => $entry->company_id, 'recorded_on' => $date->toDateString()],
                    ['position' => $index + 1],
                );
                $recorded++;
            }
        }

        return $recorded;
    }
}
