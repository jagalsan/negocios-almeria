<?php

namespace App\Actions;

use App\Models\Company;
use App\Support\DomainNormalizer;
use App\Support\NameNormalizer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateCompany
{
    public function handle(array $data, ?int $userId = null): Company
    {
        $normalized = NameNormalizer::normalize($data['name']);
        $domain = DomainNormalizer::normalize($data['website_url'] ?? null);
        $existing = Company::withTrashed()
            ->where(fn ($query) => $query->where('name_normalized', $normalized)->when($domain, fn ($query) => $query->orWhere('website_domain', $domain)))
            ->first();

        if ($existing) {
            throw ValidationException::withMessages(['name' => "Esta empresa ya está registrada como {$existing->name}. Si es tuya, reclámala."]);
        }

        return DB::transaction(function () use ($data, $userId, $normalized, $domain) {
            $company = Company::create([...$data, 'name_normalized' => $normalized, 'website_domain' => $domain, 'slug' => Str::slug($data['name']), 'origin' => $userId ? 'user' : 'admin', 'created_by' => $userId]);
            $categories = collect($data['category_ids'] ?? [])->take(5);
            $company->categories()->sync($categories->mapWithKeys(fn ($id, $index) => [$id => ['is_primary' => $index === 0]])->all());

            return $company;
        });
    }
}
