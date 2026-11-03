<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class Sponsorship extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::saving(function (Sponsorship $sponsorship) {
            if ($sponsorship->slot < 1 || $sponsorship->slot > 3 || $sponsorship->ends_at <= $sponsorship->starts_at) {
                throw ValidationException::withMessages(['slot' => 'El hueco debe ser del 1 al 3 y las fechas deben ser válidas.']);
            }

            $overlaps = static::query()
                ->where('ranking_id', $sponsorship->ranking_id)
                ->where('slot', $sponsorship->slot)
                ->where('id', '!=', $sponsorship->id ?? 0)
                ->whereNot('status', 'cancelled')
                ->where('starts_at', '<=', $sponsorship->ends_at)
                ->where('ends_at', '>=', $sponsorship->starts_at)
                ->exists();

            if ($overlaps) {
                throw ValidationException::withMessages(['slot' => 'Ya existe un patrocinio en este hueco durante esas fechas.']);
            }
        });
    }

    protected function casts(): array
    {
        return ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'paid_at' => 'datetime'];
    }

    public function ranking(): BelongsTo
    {
        return $this->belongsTo(Ranking::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->starts_at->isPast() && $this->ends_at->isFuture();
    }
}
