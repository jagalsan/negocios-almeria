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
        $all = RankingPositions::visibleEntries($ranking);
        $entries = $all->take(RankingPositions::TOP);
        $plural = Seo::lowerFirst($ranking->category->name_plural);
        $article = Seo::isMasculine($plural) ? 'los' : 'las';
        $place = $ranking->municipality?->name ?? 'Almería';
        $heading = ucfirst($article)." {$entries->count()} mejores {$plural} en {$place}";
        $year = date('Y');

        return [
            'uuid' => $ranking->uuid,
            'slug' => $ranking->slug,
            'url' => $canonical,
            'category' => ['slug' => $ranking->category->slug, 'name' => $ranking->category->name, 'namePlural' => $ranking->category->name_plural],
            'municipality' => $ranking->municipality?->name,
            'total' => $all->count(),
            'listingUrl' => route('categories.show', $ranking->category->slug),
            'seo' => [
                'title' => $ranking->seo_title ?: Seo::title("{$heading} ({$year}) · ".Seo::SITE_NAME, "{$heading} ({$year})", "Top {$entries->count()} {$plural} en {$place} ({$year})", "Top {$entries->count()} {$plural} en {$place}", $heading),
                'description' => Seo::description($ranking->seo_description ?: "Ranking {$year} de {$article} {$entries->count()} mejores {$plural} de {$place}, revisado a mano: motivo de inclusión, servicios y contacto de cada empresa."),
                'canonical' => $canonical,
                'robots' => Seo::ROBOTS,
            ],
            'h1' => preg_replace('/\b\d+ mejores\b/u', $entries->count().' mejores', $ranking->h1),
            'answer' => $ranking->answer,
            'summary' => self::summary($ranking, $entries, $heading),
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
            'related' => Ranking::published()->whereKeyNot($ranking->id)->with(['category', 'municipality'])->orderBy('title')->get()
                ->map(fn (Ranking $related) => ['name' => $related->category->name_plural.' en '.($related->municipality?->name ?? 'Almería'), 'url' => route('rankings.show', $related->slug)])
                ->all(),
        ];
    }

    private static function summary(Ranking $ranking, $entries, string $heading): ?string
    {
        if ($entries->isEmpty()) {
            return null;
        }

        $names = $entries->take(3)->map(fn ($entry) => $entry->company->name)->values();
        $list = $names->count() > 1 ? $names->slice(0, -1)->join(', ').' y '.$names->last() : $names->first();
        $rest = $entries->count() - $names->count();
        $date = ($ranking->reviewed_at ?? $ranking->updated_at)?->translatedFormat('F \\d\\e Y');

        return "En resumen: {$heading} según nuestra revisión de {$date} son {$list}"
            .($rest > 0 ? ", seguidas de {$rest} empresas más con web propia y sede en ".($ranking->municipality ? $ranking->municipality->name : 'la provincia').'.' : '.')
            .' El orden es editorial y no se puede comprar.';
    }
}
