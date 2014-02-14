## Laravel 4 Debugbar

This is a package to integrate [PHP Debug Bar](http://phpdebugbar.com/) with Laravel.
It includes a ServiceProvider to register the debugbar and attach it to the output. You can publish assets and configure it through Laravel.
It bootstraps some Collectors to work with Laravel and implements a couple custom DataCollectors, specific for Laravel.
It is configured to display Redirects and (jQuery) Ajax Requests. (Shown in a dropdown)
Read [the documentation](http://phpdebugbar.com/docs/) for more configuration options.

![Screenshot](http://i.imgur.com/GVc6C9g.png)

Note: Use the DebugBar only in development. It can slow the application down (because it has to gather data). So when experiencing slowness, try disabling some of the collectors.

This package includes some custom collectors:
 - RouteCollector: Show information about the current Route.
 - ViewCollector: Show the currently loaded views. (Optionally: display the shared data)
 - EventsCollector: Show all events
 - LaravelCollector: Show the Laravel version and Environment. (disabled by default)
 - SymfonyRequestCollector: replaces the RequestCollector with more information about the request/response
 - LogsCollector: Show the latest log entries from the storage logs. (disabled by default)
 - FilesCollector: Show the files that are included/required by PHP. (disabled by default)
 - ConfigCollector: Display the values from the config files. (disabled by default)

Bootstraps the following collectors for Laravel:
 - LogCollector: Show all Log messages
 - PdoCollector: Show Database Queries + Bindings
 - TwigCollector: For extra Twig info with barryvdh/laravel-twigbridge
 - SwiftMailCollector and SwiftLogCollector for Mail

And the default collectors:
 - PhpInfoCollector
 - MessagesCollector
 - TimeDataCollector (With Booting and Application timing)
 - MemoryCollector
 - ExceptionsCollector

It also provides a Facade interface for easy logging Messages, Exceptions and Time

## Installation

Require this package in your composer.json and run composer update (or run `composer require barryvdh/laravel-debugbar:dev-master` directly):

    "barryvdh/laravel-debugbar": "1.*"

After updating composer, add the ServiceProvider to the providers array in app/config/app.php

    'Barryvdh\Debugbar\ServiceProvider',

Alias the Debugbar facade by adding it to the aliases array in the app/config/app.php file.

    'Debugbar' => 'Barryvdh\Debugbar\Facade',

You need to publish the assets from this package.

    $ php artisan debugbar:publish

Note: The public assets can change overtime (because of upstream changes), it is recommended to re-publish them after update. You can also add the republish command in composer.json.

    "post-update-cmd": [
        "php artisan debugbar:publish"
    ],

The profiler is enabled by default, if you have app.debug=true. You can override that in the config files.
You can also set in your config if you want to include the vendor files also (FontAwesome and jQuery). If you already use them in your site, set it to false.
You can also only display the js of css vendors, by setting it to 'js' or 'css'.

    $ php artisan config:publish barryvdh/laravel-debugbar

You can also disable/enable the loggers you want. You can also use the IoC container to add extra loggers. (`$app['debugbar']->addCollector(new MyDataCollector)`)

If you want to use the facade to log messages, add this to your facades in app.php:

     'Debugbar' => 'Barryvdh\Debugbar\Facade',

You can now add messages using the Facade, using the PSR-3 levels (debug, info, notice, warning, error, critical, alert, emergency):

    Debugbar::info($object);
    Debugbar::error("Error!");
    Debugbar::warning('Watch out..');
    Debugbar::addMessage('Another message', 'mylabel');

And start/stop timing:

    Debugbar::startMeasure('render','Time for rendering');
    Debugbar::stopMeasure('render');
    Debugbar::addMeasure('now', LARAVEL_START, microtime(true));
    Debugbar::measure('My long operation', function() {
        //Do something..
    });

Or log exceptions:

    try {
        throw new Exception('foobar');
    } catch (Exception $e) {
        Debugbar::addException($e);
    }

If you want you can add your own DataCollectors, through the Container or the Facade:

    Debugbar::addCollector(new DebugBar\DataCollector\MessagesCollector('my_messages'));
    //Or via the App container:
    $debugbar = App::make('debugbar');
    $debugbar->addCollector(new DebugBar\DataCollector\MessagesCollector('my_messages'));

By default, the Debugbar is injected just before `</body>`. If you want to inject the Debugbar yourself,
set the config option 'inject' to false and use the renderer yourself and follow http://phpdebugbar.com/docs/rendering.html

    $renderer = Debugbar::getJavascriptRenderer();

Note: Not using the auto-inject, will disable the Request information, because that is added After the response.
You can add the default_request datacollector in the config as alternative.

## Enabling/Disabling on run time
You can enable or disable the debugbar during run time.

    \Debugbar::enable();
    \Debugbar::disable();

NB. Once enabled, the collectors are added (and could produce extra overhead), so if you want to use the debugbar in production, disable in the config and only enable when needed.
