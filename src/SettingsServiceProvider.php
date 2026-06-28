<?php

namespace Mca\Settings;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Mca\Settings\Console\InstallSettingsCommand;
use Mca\Settings\Console\SyncSettingsCommand;
use Mca\Settings\Http\Middleware\EnsureMaintenanceMode;
use Mca\Settings\Http\Middleware\EnsureMcaSettingsRoot;
use Mca\Settings\Http\Middleware\SetMcaSettingsLocale;
use Mca\Settings\Services\SettingsService;

class SettingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/settings.php', 'settings');

        $this->app->singleton(SettingsService::class);
    }

    public function boot(): void
    {
        if (! config('settings.enabled', true)) {
            return;
        }

        $this->registerPublishing();
        $this->registerMiddleware();
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'mca-settings');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'mca-settings');
        $this->registerRoutes();
        $this->registerMaintenanceMiddleware();
        $this->registerHub();

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallSettingsCommand::class,
                SyncSettingsCommand::class,
            ]);
        }
    }

    protected function registerHub(): void
    {
        if (! function_exists('mca_hub_register')) {
            return;
        }

        mca_hub_register('settings', [
            'enabled' => fn () => (bool) config('settings.enabled', true),
        ]);
    }

    protected function registerPublishing(): void
    {
        $this->publishes([
            __DIR__.'/../config/settings.php' => config_path('settings.php'),
            __DIR__.'/../config/definitions.php' => config_path('mca-settings-definitions.php'),
        ], 'mca-settings-config');

        $this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/mca-settings'),
        ], 'mca-settings-assets');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/mca-settings'),
        ], 'mca-settings-views');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'mca-settings-migrations');
    }

    protected function registerMiddleware(): void
    {
        /** @var Router $router */
        $router = $this->app['router'];
        $router->aliasMiddleware('mca.settings.root', EnsureMcaSettingsRoot::class);
        $router->aliasMiddleware('mca.settings.locale', SetMcaSettingsLocale::class);
        $router->aliasMiddleware('mca.settings.maintenance', EnsureMaintenanceMode::class);
    }

    protected function registerRoutes(): void
    {
        if (! config('settings.routes.load_package_routes', true)) {
            return;
        }

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }

    protected function registerMaintenanceMiddleware(): void
    {
        if (! config('settings.middleware.register_maintenance', false)) {
            return;
        }

        $this->app->booted(function () {
            /** @var Router $router */
            $router = $this->app['router'];
            $router->pushMiddlewareToGroup('web', EnsureMaintenanceMode::class);
        });
    }
}
