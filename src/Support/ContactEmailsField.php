<?php

namespace Mca\Settings\Support;

use Mca\Settings\Services\SettingsService;

final class ContactEmailsField
{
    public const string KEY = 'contact.emails';

    /** @return array<string, string> */
    public static function types(): array
    {
        return [
            'system' => 'system',
            'contact' => 'contact',
            'billing' => 'billing',
            'support' => 'support',
            'other' => 'other',
        ];
    }

    /** @return array<string, array{label: string, type: string}> */
    public static function legacyPresets(): array
    {
        return [
            'contact.system_email' => ['label' => 'Sistem', 'type' => 'system'],
            'contact.email' => ['label' => 'İletişim', 'type' => 'contact'],
        ];
    }

    /** @return array{label: string, email: string, type: string} */
    public static function emptyRow(): array
    {
        return ['label' => '', 'email' => '', 'type' => 'contact'];
    }

    /** @return list<array{label: string, email: string, type: string}> */
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

    /** @return list<array{label: string, email: string, type: string}> */
    public static function legacyRowsFromSettings(SettingsService $settings): array
    {
        $rows = [];

        foreach (self::legacyPresets() as $key => $preset) {
            $email = trim((string) $settings->get($key, ''));

            if ($email === '') {
                continue;
            }

            $rows[] = [
                'label' => $preset['label'],
                'email' => $email,
                'type' => $preset['type'],
            ];
        }

        return $rows;
    }

    /**
     * @param  mixed  $rows
     * @return list<array{label: string, email: string, type: string}>
     */
    public static function normalize(mixed $rows): array
    {
        if (! is_array($rows)) {
            return [];
        }

        $allowedTypes = array_keys(self::types());
        $normalized = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $label = trim((string) ($row['label'] ?? ''));
            $email = trim((string) ($row['email'] ?? ''));
            $type = trim((string) ($row['type'] ?? 'contact'));

            if ($label === '' && $email === '') {
                continue;
            }

            if (! in_array($type, $allowedTypes, true)) {
                $type = 'other';
            }

            $normalized[] = [
                'label' => $label,
                'email' => $email,
                'type' => $type,
            ];
        }

        return $normalized;
    }

    /** @return list<string> */
    public static function legacyKeys(): array
    {
        return array_keys(self::legacyPresets());
    }
}
