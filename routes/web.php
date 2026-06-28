<?php

use Illuminate\Support\Facades\Route;

$web = config('settings.routes.web', []);
$prefix = $web['prefix'] ?? 'mca/settings';
$middleware = $web['middleware'] ?? ['web', 'auth', 'mca.settings.root', 'mca.settings.locale'];
$namePrefix = config('settings.routes.web.name_prefix', 'mca.settings.');
$controllers = config('settings.controllers.web', []);

Route::prefix($prefix)
    ->middleware($middleware)
    ->name($namePrefix)
    ->group(function () use ($controllers) {
        Route::get('/', [$controllers['settings'], 'index'])->name('index');
        Route::put('/', [$controllers['settings'], 'update'])->name('update');
    });
