<?php

namespace Mca\Settings\Support;

use Carbon\Carbon;
use Throwable;

final class DatetimeField
{
    public static function isDatetimeWidget(string $widget): bool
    {
        return $widget === 'datetime';
    }

    /**
     * Value for HTML datetime-local input (Y-m-d\TH:i).
     */
    public static function toInputValue(mixed $value): string
    {
        $raw = trim((string) $value);
        if ($raw === '') {
            return '';
        }

        try {
            return Carbon::parse(str_replace('T', ' ', $raw))->format('Y-m-d\TH:i');
        } catch (Throwable) {
            return '';
        }
    }

    /**
     * Persist as Y-m-d H:i (empty string allowed).
     */
    public static function normalize(mixed $value): string
    {
        $raw = trim((string) $value);
        if ($raw === '') {
            return '';
        }

        try {
            return Carbon::parse(str_replace('T', ' ', $raw))->format('Y-m-d H:i');
        } catch (Throwable) {
            return '';
        }
    }

    /**
     * @param  array<string, mixed>  $filtered
     * @param  list<array{key?: string, widget?: string}>  $groupItems
     * @return array<string, mixed>
     */
    public static function normalizeGroup(array $filtered, array $groupItems): array
    {
        foreach ($groupItems as $item) {
            $key = (string) ($item['key'] ?? '');
            $widget = (string) ($item['widget'] ?? '');

            if ($key === '' || ! self::isDatetimeWidget($widget) || ! array_key_exists($key, $filtered)) {
                continue;
            }

            $filtered[$key] = self::normalize($filtered[$key]);
        }

        return $filtered;
    }
}
