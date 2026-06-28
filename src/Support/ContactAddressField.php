<?php

namespace Mca\Settings\Support;

use Illuminate\Support\Facades\Schema;
use Mca\Settings\Services\SettingsService;

final class ContactAddressField
{
    public const string CITY_KEY = 'contact.city';

    public const string DISTRICT_KEY = 'contact.district';

    public const string NEIGHBORHOOD_KEY = 'contact.neighborhood';

    public const string CITY_ID_KEY = 'contact.city_id';

    public const string DISTRICT_ID_KEY = 'contact.district_id';

    public const string NEIGHBORHOOD_ID_KEY = 'contact.neighborhood_id';

    public static function isPackageAvailable(): bool
    {
        if (! class_exists(\Mca\Address\Models\City::class)) {
            return false;
        }

        return Schema::hasTable((new \Mca\Address\Models\City)->getTable());
    }

    /** @return array{cities: string, districts: string, neighborhoods: string}|array{} */
    public static function apiRoutes(): array
    {
        if (! self::isPackageAvailable()) {
            return [];
        }

        $routes = [];

        foreach ([
            'cities' => 'mca.address.api.cities',
            'districts' => 'mca.address.api.districts',
            'neighborhoods' => 'mca.address.api.neighborhoods',
        ] as $key => $name) {
            if (\Illuminate\Support\Facades\Route::has($name)) {
                $routes[$key] = route($name);
            }
        }

        return $routes;
    }

    /** @return array<string, mixed> */
    public static function valuesForForm(SettingsService $settings): array
    {
        return [
            'city' => (string) $settings->get(self::CITY_KEY, ''),
            'district' => (string) $settings->get(self::DISTRICT_KEY, ''),
            'neighborhood' => (string) $settings->get(self::NEIGHBORHOOD_KEY, ''),
            'city_id' => (int) $settings->get(self::CITY_ID_KEY, 0),
            'district_id' => (int) $settings->get(self::DISTRICT_ID_KEY, 0),
            'neighborhood_id' => (int) $settings->get(self::NEIGHBORHOOD_ID_KEY, 0),
        ];
    }

    /** @return list<string> */
    public static function hiddenFormKeys(): array
    {
        return [
            self::CITY_KEY,
            self::DISTRICT_KEY,
            self::NEIGHBORHOOD_KEY,
            self::CITY_ID_KEY,
            self::DISTRICT_ID_KEY,
            self::NEIGHBORHOOD_ID_KEY,
        ];
    }

    public static function usesSelectMode(): bool
    {
        return self::isPackageAvailable() && self::apiRoutes() !== [];
    }
}
