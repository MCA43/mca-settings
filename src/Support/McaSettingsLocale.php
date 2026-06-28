<?php

namespace Mca\Settings\Support;

final class McaSettingsLocale
{
    public static function resolve(): string
    {
        $locale = config('settings.locale');

        if (is_string($locale) && $locale !== '') {
            return $locale;
        }

        return (string) app()->getLocale();
    }

    public static function apply(): void
    {
        app()->setLocale(self::resolve());
    }
}
