<?php

namespace Mca\Settings\Support;

use Mca\Settings\Services\SettingsService;

final class ContactMapField
{
    public const string EMBED_KEY = 'contact.map_embed';

    public const string LAT_KEY = 'contact.map_lat';

    public const string LNG_KEY = 'contact.map_lng';

    public static function mode(?SettingsService $settings = null): string
    {
        $settings ??= app(SettingsService::class);

        if (trim((string) $settings->get(self::EMBED_KEY, '')) !== '') {
            return 'embed';
        }

        if (self::latitude($settings) !== null && self::longitude($settings) !== null) {
            return 'coordinates';
        }

        return 'none';
    }

    public static function latitude(?SettingsService $settings = null): ?float
    {
        $settings ??= app(SettingsService::class);

        return self::parseCoordinate($settings->get(self::LAT_KEY, ''));
    }

    public static function longitude(?SettingsService $settings = null): ?float
    {
        $settings ??= app(SettingsService::class);

        return self::parseCoordinate($settings->get(self::LNG_KEY, ''));
    }

    public static function embedHtml(?SettingsService $settings = null): string
    {
        $settings ??= app(SettingsService::class);

        return trim((string) $settings->get(self::EMBED_KEY, ''));
    }

    public static function openStreetMapEmbedUrl(?float $lat = null, ?float $lng = null): string
    {
        $lat ??= self::latitude();
        $lng ??= self::longitude();

        if ($lat === null || $lng === null) {
            return 'https://www.openstreetmap.org/export/embed.html?bbox='
                .rawurlencode('28.85,41.08,28.88,41.11')
                .'&layer=mapnik';
        }

        $delta = 0.012;
        $west = $lng - $delta;
        $south = $lat - $delta;
        $east = $lng + $delta;
        $north = $lat + $delta;

        return 'https://www.openstreetmap.org/export/embed.html?bbox='
            ."{$west},{$south},{$east},{$north}"
            .'&layer=mapnik&marker='.$lat.','.$lng;
    }

    public static function externalMapUrl(?string $fallbackQuery = null, ?SettingsService $settings = null): string
    {
        $settings ??= app(SettingsService::class);
        $lat = self::latitude($settings);
        $lng = self::longitude($settings);

        if ($lat !== null && $lng !== null) {
            return 'https://maps.google.com/?q='.rawurlencode("{$lat},{$lng}");
        }

        $query = trim((string) ($fallbackQuery ?? mca_setting('contact.address', '')));

        if ($query !== '') {
            return 'https://maps.google.com/?q='.rawurlencode($query);
        }

        return 'https://maps.google.com/';
    }

    private static function parseCoordinate(mixed $value): ?float
    {
        $raw = trim((string) $value);

        if ($raw === '' || ! is_numeric($raw)) {
            return null;
        }

        return (float) $raw;
    }
}
