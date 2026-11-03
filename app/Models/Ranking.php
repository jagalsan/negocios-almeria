<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Ranking extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(function (Ranking $ranking) {
            $ranking->uuid ??= (string) Str::uuid();
        });
        static::saving(function (Ranking $ranking) {
            if ($ranking->status === 'published' && ($ranking->entries()->count() < 5 || count($ranking->faq ?? []) < 4 || count($ranking->criteria ?? []) < 3)) {
                throw ValidationException::withMessages(['status' => 'Para publicar faltan al menos 5 empresas, 3 criterios o 4 preguntas frecuentes.']);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'secondary_keywords' => 'array',
            'criteria' => 'array',
            'faq' => 'array',
            'published_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function municipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(RankingEntry::class)->orderBy('position');
    }

    public function sponsorships(): HasMany
    {
        return $this->hasMany(Sponsorship::class)->orderBy('slot');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
