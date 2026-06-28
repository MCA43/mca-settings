<?php

return [

    'enabled' => env('MCA_SETTINGS_ENABLED', true),

    'locale' => env('MCA_SETTINGS_LOCALE'),

    'table' => env('MCA_SETTINGS_TABLE', 'mca_settings'),

    'cache' => [
        'enabled' => env('MCA_SETTINGS_CACHE', true),
        'ttl' => (int) env('MCA_SETTINGS_CACHE_TTL', 3600),
        'prefix' => 'mca.settings.',
    ],

    'routes' => [
        'load_package_routes' => env('MCA_SETTINGS_LOAD_ROUTES', true),
        'web' => [
            'prefix' => env('MCA_SETTINGS_ROUTE_PREFIX', 'mca/settings'),
            'middleware' => array_filter(explode(',', (string) env(
                'MCA_SETTINGS_MIDDLEWARE',
                'web,auth,mca.settings.root,mca.settings.locale'
            ))),
            'name_prefix' => 'mca.settings.',
        ],
    ],

    'controllers' => [
        'web' => [
            'settings' => \Mca\Settings\Http\Controllers\Web\SettingsController::class,
        ],
    ],

    'views' => [
        'namespace' => env('MCA_SETTINGS_VIEW_NAMESPACE', 'mca-settings'),
        'layout' => env('MCA_SETTINGS_VIEW_LAYOUT', 'mca-settings::layouts.app'),
    ],

    'ui' => [
        'title' => env('MCA_SETTINGS_UI_TITLE'),
        'class_prefix' => 'mca-sett',
        'show_keys' => env('MCA_SETTINGS_UI_SHOW_KEYS', false),
        'assets' => [
            'css' => 'vendor/mca-settings/mca-settings.css',
            'js' => 'vendor/mca-settings/mca-settings.js',
            'ui' => 'vendor/mca-permission/mca-ui.css',
            'ui_js' => 'vendor/mca-permission/mca-ui.js',
        ],
    ],

    'access' => [
        'use_permission_root' => env('MCA_SETTINGS_USE_PERMISSION_ROOT', true),
        'role_column' => env('MCA_SETTINGS_ROLE_COLUMN', 'role_id'),
        'root_role' => env('MCA_SETTINGS_ROOT_ROLE', 'root'),
    ],

    'middleware' => [
        'register_maintenance' => env('MCA_SETTINGS_MAINTENANCE_MIDDLEWARE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Group tab order in admin UI
    |--------------------------------------------------------------------------
    */
    'groups_order' => [
        'general', 'security', 'locale', 'branding', 'contact', 'social',
        'integrations', 'seo', 'maintenance',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default setting definitions (synced to DB on install / mca:settings:sync)
    |--------------------------------------------------------------------------
    */
    'definitions' => require (file_exists(__DIR__.'/mca-settings-definitions.php')
        ? __DIR__.'/mca-settings-definitions.php'
        : __DIR__.'/definitions.php'),

];
