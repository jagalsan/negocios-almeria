<?php

namespace App\Support;

use Illuminate\Support\Str;

class Seo
{
    public const ROBOTS = 'index,follow,max-snippet:-1,max-image-preview:large,max-video-preview:-1';

    public const SITE_NAME = 'Negocios Almería';

    public const TITLE_MAX = 60;

    public const DESCRIPTION_MAX = 155;

    public static function title(string ...$candidates): string
    {
        foreach ($candidates as $candidate) {
            if (mb_strlen($candidate) <= self::TITLE_MAX) {
                return $candidate;
            }
        }

        return Str::limit(end($candidates), self::TITLE_MAX - 1, '…');
    }

    public static function description(?string $text): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $text)));

        if (mb_strlen($text) <= self::DESCRIPTION_MAX) {
            return $text;
        }

        $cut = mb_substr($text, 0, self::DESCRIPTION_MAX - 1);

        return rtrim(mb_substr($cut, 0, mb_strrpos($cut, ' ') ?: null), ' ,.;:').'…';
    }

    public static function lowerFirst(string $text): string
    {
        $second = mb_substr($text, 1, 1);

        return $second !== '' && mb_strtolower($second) === $second ? mb_strtolower(mb_substr($text, 0, 1)).mb_substr($text, 1) : $text;
    }

    public static function isMasculine(string $noun): bool
    {
        $word = mb_strtolower(Str::before(trim($noun), ' '));

        return str_ends_with($word, 'o') || str_ends_with($word, 'os') || in_array($word, ['coworking', 'coworkings', 'agente', 'agentes'], true);
    }

    public static function organization(): array
    {
        return [
            '@type' => 'Organization',
            '@id' => route('home').'#organization',
            'name' => self::SITE_NAME,
            'url' => route('home'),
            'logo' => ['@type' => 'ImageObject', 'url' => asset('og-image.png'), 'width' => 1200, 'height' => 630],
            'email' => config('legal.email'),
            'description' => 'Directorio editorial independiente con rankings de agencias, desarrolladores, startups y empresas digitales de la provincia de Almería.',
            'knowsAbout' => ['Empresas digitales de Almería', 'Agencias de marketing en Almería', 'Desarrollo web y software en Almería', 'Ecosistema emprendedor de Almería'],
            'contactPoint' => ['@type' => 'ContactPoint', 'contactType' => 'customer support', 'email' => config('legal.email'), 'availableLanguage' => 'es'],
            'areaServed' => ['@type' => 'AdministrativeArea', 'name' => 'Provincia de Almería'],
            'founder' => config('legal.founder_name') ? ['@type' => 'Person', 'name' => config('legal.founder_name'), 'url' => config('legal.founder_url')] : null,
        ];
    }

    public static function website(): array
    {
        return [
            '@type' => 'WebSite',
            '@id' => route('home').'#website',
            'url' => route('home'),
            'name' => self::SITE_NAME,
            'inLanguage' => 'es-ES',
            'publisher' => ['@id' => route('home').'#organization'],
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => ['@type' => 'EntryPoint', 'urlTemplate' => route('search').'?q={search_term_string}'],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    public static function webPage(string $type, string $url, string $name, ?string $description = null, array $extra = []): array
    {
        return [
            '@type' => $type,
            '@id' => $url.'#webpage',
            'url' => $url,
            'name' => $name,
            'description' => $description,
            'inLanguage' => 'es-ES',
            'isPartOf' => ['@id' => route('home').'#website'],
            ...$extra,
        ];
    }

    /** @param array<int, array{0: string, 1: ?string}> $items */
    public static function breadcrumbs(string $url, array $items): array
    {
        return [
            '@type' => 'BreadcrumbList',
            '@id' => $url.'#breadcrumb',
            'itemListElement' => collect([['Inicio', route('home')], ...$items])->values()->map(fn ($item, $index) => array_filter([
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item[0],
                'item' => $item[1] ?? $url,
            ]))->all(),
        ];
    }

    public static function graph(array $nodes): string
    {
        $graph = collect([self::organization(), self::website(), ...$nodes])->filter()->map(fn ($node) => self::clean($node))->values()->all();

        return json_encode(['@context' => 'https://schema.org', '@graph' => $graph], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG);
    }

    private static function clean(array $node): array
    {
        return collect($node)
            ->map(fn ($value) => is_array($value) ? self::clean($value) : $value)
            ->reject(fn ($value) => $value === null || $value === '' || $value === [])
            ->all();
    }
}
