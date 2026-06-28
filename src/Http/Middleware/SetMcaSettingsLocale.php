<?php

namespace Mca\Settings\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Mca\Settings\Support\McaSettingsLocale;
use Symfony\Component\HttpFoundation\Response;

class SetMcaSettingsLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        McaSettingsLocale::apply();

        return $next($request);
    }
}
