<?php

namespace App\Support;

class DomainNormalizer
{
    private const IGNORED = ['facebook.com', 'instagram.com', 'linktr.ee', 'google.com', 'sites.google.com', 'wixsite.com'];

    public static function normalize(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $url = str_contains($url, '://') ? $url : 'https://'.$url;
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $host = preg_replace('/^www\./', '', $host) ?? $host;

        if ($host === '' || in_array($host, self::IGNORED, true)) {
            return null;
        }

        return function_exists('idn_to_ascii') ? idn_to_ascii($host) ?: $host : $host;
    }
}
