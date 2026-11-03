<?php

namespace App\Data;

use App\Models\Company;
use App\Support\RankingPositions;

class CompanyPresenter
{
    public static function make(Company $company): array
    {
        $company->loadMissing(['municipality', 'categories', 'rankingEntries.ranking']);

        return [
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
            'municipality' => $company->municipality?->name,
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
    }
}
