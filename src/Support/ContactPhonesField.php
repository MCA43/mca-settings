<?php

namespace Mca\Settings\Support;

use Mca\Settings\Services\SettingsService;

final class ContactPhonesField
{
    public const string KEY = 'contact.phones';

    /** @return array<string, string> */
    public static function types(): array
    {
        return [
            'phone' => 'phone',
            'mobile' => 'mobile',
            'whatsapp' => 'whatsapp',
            'fax' => 'fax',
            'other' => 'other',
        ];
    }

    /** @return array<string, array{label: string, type: string}> */
    public static function legacyPresets(): array
    {
        return [
            'contact.phone' => ['label' => 'Telefon', 'type' => 'phone'],
            'contact.phone_2' => ['label' => 'Telefon 2', 'type' => 'phone'],
            'contact.gsm' => ['label' => 'GSM', 'type' => 'mobile'],
            'contact.whatsapp' => ['label' => 'WhatsApp', 'type' => 'whatsapp'],
        ];
    }

    /** @return array{label: string, number: string, type: string} */
    public static function emptyRow(): array
    {
        return ['label' => '', 'number' => '', 'type' => 'phone'];
    }

    /** @return list<array{label: string, number: string, type: string}> */
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

    /** @return list<array{label: string, number: string, type: string}> */
    public static function legacyRowsFromSettings(SettingsService $settings): array
    {
        $rows = [];

        foreach (self::legacyPresets() as $key => $preset) {
            $number = trim((string) $settings->get($key, ''));

            if ($number === '') {
                continue;
            }

            $rows[] = [
                'label' => $preset['label'],
                'number' => $number,
                'type' => $preset['type'],
            ];
        }

        return $rows;
    }

    /**
     * @param  mixed  $rows
     * @return list<array{label: string, number: string, type: string}>
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
            $number = trim((string) ($row['number'] ?? $row['phone'] ?? ''));
            $type = trim((string) ($row['type'] ?? 'phone'));

            if ($label === '' && $number === '') {
                continue;
            }

            if (! in_array($type, $allowedTypes, true)) {
                $type = 'other';
            }

            $normalized[] = [
                'label' => $label,
                'number' => $number,
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
