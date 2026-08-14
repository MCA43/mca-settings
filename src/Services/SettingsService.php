<?php

namespace Mca\Settings\Services;

use Illuminate\Support\Facades\Cache;
use Mca\Settings\Models\Setting;
use Mca\Settings\Support\ContactPhonesField;
use Mca\Settings\Support\ContactAddressField;
use Mca\Settings\Support\ContactEmailsField;
use Mca\Settings\Support\SocialLinksField;

final class SettingsService
{
    /** @var array<string, mixed>|null */
    private ?array $memory = null;

    public function get(string $key, mixed $default = null): mixed
    {
        $all = $this->allKeyed();

        if (! array_key_exists($key, $all)) {
            return $default;
        }

        return $this->castOut($all[$key]['value'], (string) ($all[$key]['type'] ?? 'string'));
    }

    public function set(string $key, mixed $value): void
    {
        $setting = Setting::query()->where('key', $key)->first();

        if ($setting === null) {
            $group = str_contains($key, '.') ? explode('.', $key, 2)[0] : 'general';

            $setting = Setting::query()->create([
                'key' => $key,
                'group' => $group,
                'type' => $this->detectType($value),
                'value' => $this->castIn($value, $this->detectType($value)),
                'is_locked' => false,
            ]);
        } else {
            $setting->update([
                'value' => $this->castIn($value, (string) $setting->type),
            ]);
        }

        $this->forgetCache($key);
        $this->memory = null;
    }

    /** @return list<array<string, mixed>> */
    public function forGroup(string $group): array
    {
        return array_map(
            fn (array $item) => $this->enrichItem($item),
            collect($this->allKeyed())
                ->filter(fn (array $row) => ($row['group'] ?? '') === $group)
                ->sortBy('sort')
                ->values()
                ->all(),
        );
    }

    /** @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    private function enrichItem(array $item): array
    {
        $definition = $this->definitionFor((string) ($item['key'] ?? ''));

        if ($definition === null) {
            return $item;
        }

        if (isset($definition['widget'])) {
            $item['widget'] = $definition['widget'];
        }

        // Config definitions are the UI source of truth for label/description.
        if (isset($definition['label'])) {
            $item['label'] = $definition['label'];
        }

        if (isset($definition['description'])) {
            $item['description'] = $definition['description'];
        }

        if (isset($definition['options']) && is_array($definition['options'])) {
            $item['options'] = $definition['options'];
        }

        return $item;
    }

    /** @return array<string, mixed>|null */
    public function definitionFor(string $key): ?array
    {
        foreach (config('settings.definitions', []) as $items) {
            if (! is_array($items)) {
                continue;
            }

            if (isset($items[$key]) && is_array($items[$key])) {
                return $items[$key];
            }
        }

        return null;
    }

    /** @return list<string> */
    public function groups(): array
    {
        $order = config('settings.groups_order', []);
        $fromDb = collect($this->allKeyed())->pluck('group')->unique();
        $fromConfig = collect(array_keys(config('settings.definitions', [])));

        $merged = $fromConfig->merge($fromDb)->unique();

        if ($order !== []) {
            return collect($order)
                ->filter(fn (string $g) => $merged->contains($g))
                ->merge($merged->diff($order))
                ->values()
                ->all();
        }

        return $merged->sort()->values()->all();
    }

    public function syncDefinitions(): int
    {
        $created = 0;

        foreach (config('settings.definitions', []) as $group => $items) {
            if (! is_array($items)) {
                continue;
            }

            foreach ($items as $key => $definition) {
                if (! is_string($key) || ! is_array($definition)) {
                    continue;
                }

                $exists = Setting::query()->where('key', $key)->exists();
                if ($exists) {
                    continue;
                }

                $type = (string) ($definition['type'] ?? 'string');
                $default = $definition['default'] ?? null;

                Setting::query()->create([
                    'group' => $group,
                    'key' => $key,
                    'type' => $type,
                    'value' => $this->castIn($default, $type),
                    'label' => $definition['label'] ?? null,
                    'description' => $definition['description'] ?? null,
                    'sort' => (int) ($definition['sort'] ?? 100),
                    'is_locked' => true,
                ]);

                $created++;
            }
        }

        $this->flushCache();

        return $created;
    }

    public function migrateSocialLinksFromLegacy(): bool
    {
        $stored = $this->get(SocialLinksField::KEY, []);

        if (is_array($stored) && $stored !== []) {
            return false;
        }

        $normalized = SocialLinksField::normalize(
            SocialLinksField::legacyRowsFromSettings($this),
        );

        if ($normalized === []) {
            return false;
        }

        $this->set(SocialLinksField::KEY, $normalized);

        return true;
    }

    public function upgradeSocialLinksFormat(): bool
    {
        $stored = $this->get(SocialLinksField::KEY, []);

        if (! is_array($stored) || $stored === []) {
            return false;
        }

        $hasLegacyShape = false;

        foreach ($stored as $row) {
            if (is_array($row) && (isset($row['platform']) || isset($row['url']))) {
                $hasLegacyShape = true;
                break;
            }
        }

        if (! $hasLegacyShape) {
            return false;
        }

        $this->set(SocialLinksField::KEY, SocialLinksField::normalize($stored));

        return true;
    }

    public function migrateContactPhonesFromLegacy(): bool
    {
        $stored = $this->get(ContactPhonesField::KEY, []);

        if (is_array($stored) && $stored !== []) {
            return false;
        }

        $normalized = ContactPhonesField::normalize(
            ContactPhonesField::legacyRowsFromSettings($this),
        );

        if ($normalized === []) {
            return false;
        }

        $this->set(ContactPhonesField::KEY, $normalized);

        return true;
    }

    public function migrateContactEmailsFromLegacy(): bool
    {
        $stored = $this->get(ContactEmailsField::KEY, []);

        if (is_array($stored) && $stored !== []) {
            return false;
        }

        $normalized = ContactEmailsField::normalize(
            ContactEmailsField::legacyRowsFromSettings($this),
        );

        if ($normalized === []) {
            return false;
        }

        $this->set(ContactEmailsField::KEY, $normalized);

        return true;
    }

    public function pruneOrphaned(): int
    {
        $allowed = $this->definedKeys();

        $deleted = Setting::query()
            ->whereNotIn('key', $allowed)
            ->delete();

        if ($deleted > 0) {
            $this->flushCache();
        }

        return $deleted;
    }

    /** @return list<string> */
    private function definedKeys(): array
    {
        $keys = [];

        foreach (config('settings.definitions', []) as $items) {
            if (! is_array($items)) {
                continue;
            }

            foreach ($items as $key => $definition) {
                if (is_string($key)) {
                    $keys[] = $key;
                }
            }
        }

        return $keys;
    }

    /** @param  array<string, mixed>  $values */
    public function updateMany(array $values): void
    {
        foreach ($values as $key => $value) {
            if (! is_string($key)) {
                continue;
            }

            $setting = Setting::query()->where('key', $key)->first();
            if ($setting === null) {
                continue;
            }

            $setting->update([
                'value' => $this->castIn($value, (string) $setting->type),
            ]);

            $this->forgetCache($key);
        }

        $this->memory = null;
    }

    /** @return array<string, array<string, mixed>> */
    private function allKeyed(): array
    {
        if ($this->memory !== null) {
            return $this->memory;
        }

        if (! config('settings.cache.enabled', true)) {
            return $this->memory = $this->loadFromDatabase();
        }

        return $this->memory = Cache::remember(
            config('settings.cache.prefix', 'mca.settings.').'all',
            (int) config('settings.cache.ttl', 3600),
            fn () => $this->loadFromDatabase(),
        );
    }

    /** @return array<string, array<string, mixed>> */
    private function loadFromDatabase(): array
    {
        return Setting::query()
            ->orderBy('sort')
            ->get()
            ->keyBy('key')
            ->map(fn (Setting $setting) => [
                'group' => $setting->group,
                'key' => $setting->key,
                'value' => $setting->value,
                'type' => $setting->type,
                'label' => $setting->label,
                'description' => $setting->description,
                'sort' => $setting->sort,
                'is_locked' => $setting->is_locked,
            ])
            ->all();
    }

    private function castOut(mixed $value, string $type): mixed
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'json' => is_string($value) ? json_decode($value, true) : $value,
            default => $value,
        };
    }

    private function castIn(mixed $value, string $type): ?string
    {
        return match ($type) {
            'boolean' => $value ? '1' : '0',
            'integer' => (string) (int) $value,
            'json' => is_string($value) ? $value : json_encode($value),
            default => $value === null ? null : (string) $value,
        };
    }

    private function detectType(mixed $value): string
    {
        return match (true) {
            is_bool($value) => 'boolean',
            is_int($value) => 'integer',
            is_array($value) => 'json',
            default => 'string',
        };
    }

    private function forgetCache(string $key): void
    {
        if (! config('settings.cache.enabled', true)) {
            return;
        }

        Cache::forget(config('settings.cache.prefix', 'mca.settings.').$key);
        Cache::forget(config('settings.cache.prefix', 'mca.settings.').'all');
    }

    public function flushCache(): void
    {
        if (! config('settings.cache.enabled', true)) {
            $this->memory = null;

            return;
        }

        Cache::forget(config('settings.cache.prefix', 'mca.settings.').'all');
        $this->memory = null;
    }
}
