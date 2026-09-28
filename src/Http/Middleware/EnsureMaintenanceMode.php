<?php

namespace Mca\Settings\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMaintenanceMode
{
    /** @var list<string> */
    private array $defaultExcept = [
        'mca',
        'mca/*',
        'panel',
        'panel/*',
        // EN auth (redirects / Fortify defaults)
        'login',
        'logout',
        'register',
        'forgot-password',
        'reset-password',
        'reset-password/*',
        'two-factor-challenge',
        // TR auth slugs (emlak-cms)
        'giris',
        'cikis',
        'kayit',
        'sifremi-unuttum',
        'sifre-sifirla',
        'sifre-sifirla/*',
        'sifre-onayla',
        'sifre-onay-durumu',
        'iki-faktor',
        'passkeys',
        'passkeys/*',
        'user',
        'user/*',
        'profil',
        'sanctum/*',
        'livewire/*',
        'up',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (! mca_setting_bool('maintenance.enabled', false)) {
            return $next($request);
        }

        $except = config('settings.middleware.maintenance_except');
        if (! is_array($except) || $except === []) {
            $except = $this->defaultExcept;
        }

        foreach ($except as $pattern) {
            if (is_string($pattern) && $pattern !== '' && $request->is($pattern)) {
                return $next($request);
            }
        }

        if ($request->user() && function_exists('mca_is_root') && mca_is_root($request->user())) {
            return $next($request);
        }

        $view = (string) config('settings.views.maintenance', 'mca-settings::maintenance.show');
        if ($view === '' || ! view()->exists($view)) {
            $view = 'mca-settings::maintenance.show';
        }

        return response()->view($view, [
            'title' => mca_setting('maintenance.title', mca_sett('maintenance.default_title')),
            'message' => mca_setting('maintenance.description', ''),
            'endDate' => mca_setting('maintenance.end_date', ''),
            'image' => mca_setting('maintenance.image', ''),
            'siteName' => mca_setting('general.site_name', config('app.name')),
            'logoLight' => mca_setting('branding.light_logo', ''),
            'logoDark' => mca_setting('branding.dark_logo', ''),
            'favicon' => mca_setting('branding.favicon', ''),
        ], 503);
    }
}
