## Laravel Debugbar

This is a simple package to integrate PHP Debug Bar (https://github.com/maximebf/php-debugbar) with Laravel.
It includes a ServiceProvider to register the debugbar and attach it to the output. You can publish assets and configure it through Laravel.
It bootstraps some Collectors to work with Laravel and implements a couple custom DataCollectors, specific for Laravel.

This includes 3 custom collectors:
 - RouteCollector: Show information about the current Route
 - ViewCollector: Show the currently loaded views an it's data.
 - LaravelCollector: Show the Laravel version and Environment.

And implements the Monolog Collector for Laravel's Logger.
I also provides a Facade interface for logging Messages, Exceptions and Time

## Installation

Require this package in your composer.json and run composer update (or run `composer require barryvdh/laravel-debugbar:dev-master` directly):

    "barryvdh/laravel-debugbar": "dev-master"

After updating composer, add the ServiceProvider to the providers array in app/config/app.php

    'Barryvdh\Debugbar\ServiceProvider',

You need to publish the assets from this package.

    $ php artisan asset:publish barryvdh/laravel-debugbar

The profiler is enabled by default, if you have app.debug=true. You can override that in the config files.
You can also set in your config if you want to include the vendor files also (FontAwesome and jQuery). If you have them, set it to false.
You can also only display the js of css vendors, but setting it to 'js' or 'css'

    $ php artisan config:publish barryvdh/laravel-debugbar

You can also set to show all events (disabled by default)

If you want to use the facade to log messages, add this to your facades in app.php:

     'Debugbar' => 'Barryvdh\Debugbar\Facade',

You can now add messages using the Facade, using the PSR-3 levels (debug, info, notice, warning, error, critical, alert, emergency):

    Debugbar::info($object);
    Debugbar::alert("Error!");
    Debugbar::warning('Watch out..');
    Debugbar::addMessage('Another message', 'mylabel');

And start/stop timing:

    \Debugbar::startMeasure('render','Time for rendering');
    \Debugbar::stopMeasure('render');
    \Debugbar::measure('My long operation', function() {
        //Do something..
    });

Or log exceptions:

    try {
        throw new Exception('foobar');
    } catch (Exception $e) {
        \Debugbar::addException($e);
    }