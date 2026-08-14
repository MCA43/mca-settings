<?php

use Mca\Settings\Services\SettingsService;

if (! function_exists('mca_setting')) {
    function mca_setting(string $key, mixed $default = null): mixed
    {
        return app(SettingsService::class)->get($key, $default);
    }
}

if (! function_exists('mca_setting_bool')) {
    function mca_setting_bool(string $key, bool $default = false): bool
    {
        return (bool) mca_setting($key, $default);
    }
}

if (! function_exists('mca_setting_map_mode')) {
    function mca_setting_map_mode(): string
    {
        return \Mca\Settings\Support\ContactMapField::mode();
    }
}

if (! function_exists('mca_setting_map_embed_html')) {
    function mca_setting_map_embed_html(): string
    {
        return \Mca\Settings\Support\ContactMapField::embedHtml();
    }
}

if (! function_exists('mca_setting_map_lat')) {
    function mca_setting_map_lat(): ?float
    {
        return \Mca\Settings\Support\ContactMapField::latitude();
    }
}

if (! function_exists('mca_setting_map_lng')) {
    function mca_setting_map_lng(): ?float
    {
        return \Mca\Settings\Support\ContactMapField::longitude();
    }
}

if (! function_exists('mca_setting_map_osm_embed_url')) {
    function mca_setting_map_osm_embed_url(): string
    {
        return \Mca\Settings\Support\ContactMapField::openStreetMapEmbedUrl();
    }
}

if (! function_exists('mca_setting_map_external_url')) {
    function mca_setting_map_external_url(?string $fallbackAddress = null): string
    {
        return \Mca\Settings\Support\ContactMapField::externalMapUrl($fallbackAddress);
    }
}

if (! function_exists('mca_setting_contact_emails')) {
    /** @return list<array{label: string, email: string, type: string}> */
    function mca_setting_contact_emails(): array
    {
        return \Mca\Settings\Support\ContactEmailsField::normalize(
            mca_setting(\Mca\Settings\Support\ContactEmailsField::KEY, []),
        );
    }
}

if (! function_exists('mca_setting_contact_location')) {
    /** @return array{city: string, district: string, neighborhood: string, city_id: int, district_id: int, neighborhood_id: int} */
    function mca_setting_contact_location(): array
    {
        return \Mca\Settings\Support\ContactAddressField::valuesForForm(mca_settings());
    }
}

if (! function_exists('mca_setting_contact_phones')) {
    /** @return list<array{label: string, number: string, type: string}> */
    function mca_setting_contact_phones(): array
    {
        return \Mca\Settings\Support\ContactPhonesField::normalize(
            mca_setting(\Mca\Settings\Support\ContactPhonesField::KEY, []),
        );
    }
}

if (! function_exists('mca_setting_social_links')) {
    /** @return list<array{icon: string, name: string, link: string}> */
    function mca_setting_social_links(): array
    {
        return \Mca\Settings\Support\SocialLinksField::normalize(
            mca_setting(\Mca\Settings\Support\SocialLinksField::KEY, []),
        );
    }
}

if (! function_exists('mca_settings')) {
    function mca_settings(): SettingsService
    {
        return app(SettingsService::class);
    }
}

if (! function_exists('mca_sett')) {
    /** @param  array<string, string|int>  $replace */
    function mca_sett(string $key, array $replace = []): string
    {
        $line = 'mca-settings::settings.'.$key;

        if (! trans()->has($line)) {
            return $key;
        }

        return (string) __($line, $replace);
    }
}

if (! function_exists('mca_sett_group')) {
    function mca_sett_group(string $group): string
    {
        $key = 'groups.'.$group;
        $translated = mca_sett($key);

        if ($translated !== $key) {
            return $translated;
        }

        return str_replace(['_', '-'], ' ', ucfirst($group));
    }
}
