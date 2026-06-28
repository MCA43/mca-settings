<?php

namespace Mca\Settings\Support;

use Mca\Settings\Services\SettingsService;

final class SocialLinksField
{
    public const string KEY = 'social.links';

    /** @return array<string, array{icon: string, name: string}> */
    public static function platformPresets(): array
    {
        return [
            'facebook' => ['icon' => 'fab fa-facebook', 'name' => 'Facebook'],
            'instagram' => ['icon' => 'fab fa-instagram', 'name' => 'Instagram'],
            'twitter' => ['icon' => 'fab fa-x-twitter', 'name' => 'X (Twitter)'],
            'linkedin' => ['icon' => 'fab fa-linkedin', 'name' => 'LinkedIn'],
            'youtube' => ['icon' => 'fab fa-youtube', 'name' => 'YouTube'],
            'tiktok' => ['icon' => 'fab fa-tiktok', 'name' => 'TikTok'],
            'github' => ['icon' => 'fab fa-github', 'name' => 'GitHub'],
            'whatsapp' => ['icon' => 'fab fa-whatsapp', 'name' => 'WhatsApp'],
            'telegram' => ['icon' => 'fab fa-telegram', 'name' => 'Telegram'],
            'pinterest' => ['icon' => 'fab fa-pinterest', 'name' => 'Pinterest'],
        ];
    }

    /** @return list<array{icon: string, name: string, link: string}> */
    public static function valueForForm(SettingsService $settings): array
    {
        $stored = $settings->get(self::KEY, []);

        if (is_array($stored) && $stored !== []) {
            $normalized = self::normalize($stored);

            return $normalized !== [] ? $normalized : [self::emptyRow()];
        }

        $legacy = self::legacyRowsFromSettings($settings);

        return $legacy !== [] ? $legacy : [self::emptyRow()];
    }

    /** @return array{icon: string, name: string, link: string} */
    public static function emptyRow(): array
    {
        return ['icon' => '', 'name' => '', 'link' => ''];
    }

    /** @return list<array{icon: string, name: string, link: string}> */
    public static function legacyRowsFromSettings(SettingsService $settings): array
    {
        $links = [];

        foreach (self::platformPresets() as $slug => $preset) {
            $url = trim((string) $settings->get('social.'.$slug, ''));

            if ($url !== '') {
                $links[] = [
                    'icon' => $preset['icon'],
                    'name' => $preset['name'],
                    'link' => $url,
                ];
            }
        }

        return $links;
    }

    /**
     * @param  mixed  $rows
     * @return list<array{icon: string, name: string, link: string}>
     */
    public static function normalize(mixed $rows): array
    {
        if (! is_array($rows)) {
            return [];
        }

        $normalized = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $icon = trim((string) ($row['icon'] ?? ''));
            $name = trim((string) ($row['name'] ?? ''));
            $link = trim((string) ($row['link'] ?? $row['url'] ?? ''));

            if ($name === '' && isset($row['platform'])) {
                $platform = trim((string) $row['platform']);
                $preset = self::platformPresets()[$platform] ?? null;

                if ($preset !== null) {
                    $icon = $icon !== '' ? $icon : $preset['icon'];
                    $name = $preset['name'];
                } elseif ($platform !== '') {
                    $name = ucfirst($platform);
                }
            }

            if ($icon === '' && $name === '' && $link === '') {
                continue;
            }

            $normalized[] = [
                'icon' => $icon,
                'name' => $name,
                'link' => $link,
            ];
        }

        return $normalized;
    }

    /** @return list<string> */
    public static function legacyKeys(): array
    {
        return array_map(
            fn (string $slug) => 'social.'.$slug,
            array_keys(self::platformPresets()),
        );
    }
}
