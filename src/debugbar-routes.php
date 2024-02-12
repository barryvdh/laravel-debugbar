<?php

use Barryvdh\Debugbar\Controllers\AssetController;
use Barryvdh\Debugbar\Controllers\CacheController;
use Barryvdh\Debugbar\Controllers\OpenHandlerController;
use Barryvdh\Debugbar\Controllers\TelescopeController;
use Barryvdh\Debugbar\Middleware\DebugbarEnabled;

$routeConfig = [
    'prefix' => app('config')->get('debugbar.route_prefix'),
    'domain' => app('config')->get('debugbar.route_domain'),
    'middleware' => DebugbarEnabled::class,
];

app('router')->group($routeConfig, function ($router) {
    $router->get('open', [OpenHandlerController::class, 'handle'])->name('debugbar.openhandler');
    $router->delete('cache/{key}/{tags?}', [CacheController::class, 'delete'])->name('debugbar.cache.delete');
    $router->get('clockwork/{id}', [OpenHandlerController::class, 'clockwork'])->name('debugbar.clockwork');
    $router->get('assets/stylesheets', [AssetController::class, 'css'])->name('debugbar.assets.css');
    $router->get('assets/javascript', [AssetController::class, 'js'])->name('debugbar.assets.js');

    if (class_exists(\Laravel\Telescope\Telescope::class)) {
        $router->get('telescope/{id}', [TelescopeController::class, 'show'])->name('debugbar.telescope');
    }
});
