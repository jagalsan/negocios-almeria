<?php

namespace App\Data;

use App\Models\Company;
use App\Support\RankingPositions;
use App\Support\Seo;

class CompanyPresenter
{
    public static function make(Company $company): array
    {
        $company->loadMissing(['municipality', 'categories', 'rankingEntries.ranking']);
        $primaryCategory = ($company->categories->first(fn ($category) => $category->pivot->is_primary) ?? $company->categories->first())?->name;

        $data = [
            'uuid' => $company->uuid,
            'slug' => $company->slug,
            'url' => route('companies.show', $company->slug),
            'name' => $company->name,
            'logo' => $company->logo_path ? asset('storage/'.$company->logo_path) : null,
            'cover' => $company->cover_path ? asset('storage/'.$company->cover_path) : null,
            'shortDescription' => $company->short_description,
            'descriptionHtml' => nl2br(e($company->description)),
            'services' => $company->services ?? [],
            'sectors' => $company->sectors ?? [],
            'categories' => $company->categories->map(fn ($category) => ['slug' => $category->slug, 'name' => $category->name, 'isPrimary' => (bool) $category->pivot->is_primary])->values()->all(),
            'primaryCategory' => $primaryCategory,
            'municipality' => $company->municipality?->name,
            'municipalitySlug' => $company->municipality?->slug,
            'address' => $company->address,
            'postalCode' => $company->postal_code,
            'lat' => $company->lat,
            'lng' => $company->lng,
            'phone' => $company->phone_public,
            'email' => $company->email_public,
            'website' => $company->website_url,
            'social' => $company->social ?? [],
            'openingHours' => $company->opening_hours ?? [],
            'foundedYear' => $company->founded_year,
            'teamSize' => $company->team_size,
            'googleRating' => $company->google_rating,
            'googleReviews' => $company->google_reviews_count,
            'verified' => (bool) $company->is_verified,
            'rankings' => $company->rankingEntries
                ->filter(fn ($entry) => $entry->ranking->status === 'published')
                ->map(function ($entry) use ($company) {
                    $position = RankingPositions::forCompany($entry->ranking, $company);

                    return $position ? [...$position, 'title' => $entry->ranking->title, 'url' => route('rankings.show', $entry->ranking->slug)] : null;
                })
                ->filter()
                ->values()
                ->all(),
            'updatedAt' => $company->updated_at?->toIso8601String(),
        ];

        return [...$data, 'summary' => self::summary($data), 'seo' => [
            'title' => $company->seo_title ?: self::seoTitle($data),
            'description' => Seo::description($company->seo_description ?: self::seoDescription($data)),
            'robots' => $company->seo_noindex ? 'noindex,follow' : null,
        ]];
    }

    private static function seoTitle(array $company): string
    {
        $category = Seo::lowerFirst($company['primaryCategory'] ?? 'Empresa digital');
        $municipality = $company['municipality'] ?? 'Almería';

        return Seo::title(
            "{$company['name']}: {$category} en {$municipality} · ".Seo::SITE_NAME,
            "{$company['name']}: {$category} en {$municipality}",
            "{$company['name']}: {$category} en Almería",
            "{$company['name']} · ".Seo::SITE_NAME,
            $company['name'],
        );
    }

    private static function seoDescription(array $company): string
    {
        $short = trim((string) $company['shortDescription']);

        if (mb_strlen($short) >= 110) {
            return $short;
        }

        $category = Seo::lowerFirst($company['primaryCategory'] ?? 'Empresa digital');
        $municipality = $company['municipality'];
        $place = ! $municipality || $municipality === 'Almería' ? 'Almería' : "{$municipality} (Almería)";
        $services = array_slice(array_filter($company['services'], 'is_string'), 0, 4);
        $rankings = count($company['rankings']);

        return collect([
            "{$company['name']} es ".(Seo::isMasculine($category) ? 'un' : 'una')." {$category} en {$place}.",
            $services ? 'Servicios: '.implode(', ', $services).'.' : null,
            ! $services && $rankings ? 'Aparece en '.$rankings.($rankings === 1 ? ' ranking' : ' rankings').' de '.Seo::SITE_NAME.'.' : null,
            'Consulta su ficha, web y contacto.',
        ])->filter()->join(' ');
    }

    private static function summary(array $data): string
    {
        $category = $data['primaryCategory'] ? Seo::lowerFirst($data['primaryCategory']) : 'empresa digital';
        $article = Seo::isMasculine($category) ? 'un' : 'una';
        $place = match ($data['municipality']) {
            null => 'de la provincia de Almería',
            'Almería' => 'con sede en Almería capital',
            default => "con sede en {$data['municipality']} (Almería)",
        };
        $text = "En resumen: {$data['name']} es {$article} {$category} {$place}";
        $text .= $data['foundedYear'] ? ", fundada en {$data['foundedYear']}." : '.';

        if ($services = array_slice(array_filter($data['services']), 0, 3)) {
            $text .= ' Trabaja en '.Seo::lowerFirst(implode(', ', $services)).'.';
        }

        if ($ranking = collect($data['rankings'])->filter()->sortBy('position')->first()) {
            $text .= isset($ranking['position']) ? " Ocupa el puesto {$ranking['position']} en «{$ranking['title']}»." : " Aparece en «{$ranking['title']}».";
        }

        return $text;
    }
}
