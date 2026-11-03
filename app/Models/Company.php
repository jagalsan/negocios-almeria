<?php

namespace App\Models;

use App\Enums\CompanyStatus;
use App\Support\DomainNormalizer;
use App\Support\NameNormalizer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $hidden = ['cif', 'created_by'];

    protected static function booted(): void
    {
        static::saving(function (Company $company) {
            $company->uuid ??= (string) Str::uuid();
            $company->slug ??= Str::slug($company->name);
            $company->name_normalized = NameNormalizer::normalize($company->name);
            $company->website_domain = DomainNormalizer::normalize($company->website_url);
        });
    }

    protected function casts(): array
    {
        return [
            'status' => CompanyStatus::class,
            'services' => 'array',
            'sectors' => 'array',
            'social' => 'array',
            'opening_hours' => 'array',
            'gallery' => 'array',
            'is_verified' => 'boolean',
            'seo_noindex' => 'boolean',
            'published_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function municipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)->withPivot('is_primary');
    }

    public function rankingEntries(): HasMany
    {
        return $this->hasMany(RankingEntry::class);
    }

    public function ownerships(): HasMany
    {
        return $this->hasMany(Ownership::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', CompanyStatus::Published->value)->whereNotNull('website_url');
    }
}
