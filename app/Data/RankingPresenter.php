<?php

namespace App\Data;

use App\Enums\CompanyStatus;
use App\Models\Ranking;
use App\Support\RankingPositions;
use App\Support\Seo;

class RankingPresenter
{
    public static function make(Ranking $ranking): array
    {
        $ranking->loadMissing(['category', 'municipality', 'author', 'entries.company.municipality', 'sponsorships.company']);
        $canonical = route('rankings.show', $ranking->slug);
        $entries = RankingPositions::visibleEntries($ranking);
        $plural = Seo::lowerFirst($ranking->category->name_plural);
        $article = Seo::isMasculine($plural) ? 'los' : 'las';
        $heading = ucfirst($article)." {$entries->count()} mejores {$plural} en Almería";
        $year = date('Y');

        return [
            'uuid' => $ranking->uuid,
            'slug' => $ranking->slug,
            'url' => $canonical,
            'category' => ['slug' => $ranking->category->slug, 'name' => $ranking->category->name, 'namePlural' => $ranking->category->name_plural],
            'municipality' => $ranking->municipality?->name,
            'seo' => [
                'title' => $ranking->seo_title ?: Seo::title("{$heading} ({$year}) · ".Seo::SITE_NAME, "{$heading} ({$year})", "Top {$entries->count()} {$plural} en Almería ({$year})", "Top {$entries->count()} {$plural} en Almería", $heading),
                'description' => Seo::description($ranking->seo_description ?: "Ranking {$year} de {$article} {$entries->count()} mejores {$plural} de Almería, revisado a mano: motivo de inclusión, servicios y contacto de cada empresa."),
                'canonical' => $canonical,
                'robots' => 'index,follow',
            ],
            'h1' => $ranking->h1,
            'answer' => $ranking->answer,
            'introHtml' => $ranking->intro,
            'criteria' => $ranking->criteria ?? [],
            'author' => ['name' => $ranking->author->name],
            'publishedAt' => $ranking->published_at?->toDateString(),
            'reviewedAt' => $ranking->reviewed_at?->toDateString(),
            'sponsored' => $ranking->sponsorships->filter(fn ($sponsorship) => $sponsorship->isActive() && $sponsorship->company->status === CompanyStatus::Published && filled($sponsorship->company->website_url))->map(fn ($sponsorship) => ['slot' => $sponsorship->slot, 'rel' => 'sponsored noopener', 'company' => CompanyPresenter::make($sponsorship->company)])->values()->all(),
            'entries' => $entries->map(function ($entry) use ($ranking) {
                $position = RankingPositions::forCompany($ranking, $entry->company);

                return [...$position, 'reason' => $entry->reason, 'rel' => 'noopener', 'company' => CompanyPresenter::make($entry->company)];
            })->all(),
            'faq' => $ranking->faq ?? [],
            'closingText' => $ranking->closing_text,
        ];
    }
}
