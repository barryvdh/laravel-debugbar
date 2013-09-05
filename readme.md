## Laravel Debugbar

Right now this is just a ServiceProvider to add https://github.com/maximebf/php-debugbar via a ServiceProvider, and add the content to the request.

Includes 2 Collectors:
 - RouteCollector: Show information about the current Route
 - ViewCollector: Show the currently loaded views an it's data.
 - LaravelCollector: Show the Laravel version and Environment.

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