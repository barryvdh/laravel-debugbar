<?php

$routeConfig = [
    'namespace' => 'Barryvdh\Debugbar\Controllers',
    'prefix' => app('config')->get('debugbar.route_prefix'),
    'domain' => app('config')->get('debugbar.route_domain'),
    'middleware' => array_merge(app('config')->get('debugbar.route_middleware', []), [\Barryvdh\Debugbar\Middleware\DebugbarEnabled::class]),
];

app('router')->group($routeConfig, function ($router) {
    $router->get('open', [
        'uses' => 'OpenHandlerController@handle',
        'as' => 'debugbar.openhandler',
    ]);

    $router->get('clockwork/{id}', [
        'uses' => 'OpenHandlerController@clockwork',
        'as' => 'debugbar.clockwork',
    ]);

    if (class_exists(\Laravel\Telescope\Telescope::class)) {
        $router->get('telescope/{id}', [
            'uses' => 'TelescopeController@show',
            'as' => 'debugbar.telescope',
        ]);
    }

    $router->get('assets/stylesheets', [
        'uses' => 'AssetController@css',
        'as' => 'debugbar.assets.css',
    ]);

    $router->get('assets/javascript', [
        'uses' => 'AssetController@js',
        'as' => 'debugbar.assets.js',
    ]);

    $router->delete('cache/{key}/{tags?}', [
        'uses' => 'CacheController@delete',
        'as' => 'debugbar.cache.delete',
    ]);
});
