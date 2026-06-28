<?php

namespace Mca\Settings\Support;

use Illuminate\Support\Facades\Schema;

final class PermissionRoleField
{
    public static function isAvailable(): bool
    {
        if (! class_exists(\Mca\Permission\Models\Role::class)) {
            return false;
        }

        return Schema::hasTable((new \Mca\Permission\Models\Role)->getTable());
    }

    /** @return list<array{id: int, slug: string, name: string, label: string}> */
    public static function options(): array
    {
        if (! self::isAvailable()) {
            return [];
        }

        return \Mca\Permission\Models\Role::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'slug', 'name'])
            ->map(fn ($role) => [
                'id' => (int) $role->id,
                'slug' => (string) $role->slug,
                'name' => (string) $role->name,
                'label' => trim($role->name.' ('.$role->slug.')'),
            ])
            ->all();
    }

    /** Select option value for a stored setting (id or slug). */
    public static function selectedOptionValue(mixed $stored): string
    {
        if ($stored === null || $stored === '') {
            return '';
        }

        $stored = (string) $stored;

        if (! self::isAvailable()) {
            return $stored;
        }

        $roleClass = \Mca\Permission\Models\Role::class;

        if (ctype_digit($stored)) {
            $role = $roleClass::query()->find((int) $stored);

            return $role ? (string) $role->id : $stored;
        }

        $role = $roleClass::query()->where('slug', $stored)->first();

        return $role ? (string) $role->id : $stored;
    }

    public static function isValidValue(mixed $value): bool
    {
        if ($value === null || $value === '') {
            return true;
        }

        $value = (string) $value;

        if (! self::isAvailable()) {
            return $value !== '';
        }

        $roleClass = \Mca\Permission\Models\Role::class;

        if (ctype_digit($value)) {
            return $roleClass::query()->whereKey((int) $value)->exists();
        }

        return $roleClass::query()->where('slug', $value)->exists();
    }
}
