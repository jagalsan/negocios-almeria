<?php

namespace App\Support;

use Illuminate\Support\Str;

class NameNormalizer
{
    public static function normalize(string $name): string
    {
        $name = Str::ascii(Str::lower($name));
        $name = preg_replace('/\b(s\.?l\.?u?|s\.?a\.?|sociedad limitada|c\.?b\.?|s\.?c\.?p\.?)\b/u', '', $name) ?? $name;
        $name = preg_replace('/[^a-z0-9]+/u', ' ', $name) ?? $name;

        return trim(preg_replace('/\s+/', ' ', $name) ?? $name);
    }
}
