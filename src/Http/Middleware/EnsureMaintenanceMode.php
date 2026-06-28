<?php

namespace Mca\Settings\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMaintenanceMode
{
    /** @var list<string> */
    private array $except = [
        'mca/*',
        'login',
        'logout',
        'register',
        'forgot-password',
        'reset-password',
        'reset-password/*',
        'up',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (! mca_setting_bool('maintenance.enabled', false)) {
            return $next($request);
        }

        foreach ($this->except as $pattern) {
            if ($request->is($pattern)) {
                return $next($request);
            }
        }

        if ($request->user() && function_exists('mca_is_root') && mca_is_root($request->user())) {
            return $next($request);
        }

        return response()->view('mca-settings::maintenance.show', [
            'title' => mca_setting('maintenance.title', mca_sett('maintenance.default_title')),
            'message' => mca_setting('maintenance.description', ''),
            'endDate' => mca_setting('maintenance.end_date', ''),
            'image' => mca_setting('maintenance.image', ''),
        ], 503);
    }
}
