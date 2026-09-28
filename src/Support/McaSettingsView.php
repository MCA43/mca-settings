<?php

namespace Mca\Settings\Support;

use Illuminate\Contracts\View\View;

final class McaSettingsView
{
    public static function layout(): string
    {
        return (string) config('settings.views.layout', 'mca-settings::layouts.app');
    }

    public static function render(string $view, array $data = []): View
    {
        McaSettingsLocale::apply();

        $namespace = config('settings.views.namespace', 'mca-settings');

        return view($namespace.'::'.$view, array_merge([
            'mcaSettTitle' => config('settings.ui.title') ?: mca_sett('app.title'),
        ], $data));
    }

    public static function uiCssUrl(): string
    {
        $path = config('settings.ui.assets.ui', 'vendor/mca-permission/mca-ui.css');

        return asset($path);
    }

    public static function uiJsUrl(): string
    {
        $path = config('settings.ui.assets.ui_js', 'vendor/mca-permission/mca-ui.js');

        return asset($path);
    }

    public static function cssUrl(): string
    {
        $path = config('settings.ui.assets.css', 'vendor/mca-settings/mca-settings.css');

        return asset($path);
    }

    public static function jsUrl(): string
    {
        $path = config('settings.ui.assets.js', 'vendor/mca-settings/mca-settings.js');

        return asset($path);
    }

    public static function groupLabel(string $group): string
    {
        $key = 'groups.'.$group;
        $translated = mca_sett($key);

        return $translated !== $key ? $translated : ucfirst($group);
    }

    public static function label(mixed $field, ?string $locale = null): string
    {
        $locale ??= McaSettingsLocale::resolve();

        if (is_string($field)) {
            return $field;
        }

        if (! is_array($field)) {
            return '';
        }

        return (string) ($field[$locale] ?? $field['en'] ?? $field['tr'] ?? reset($field) ?: '');
    }
}
