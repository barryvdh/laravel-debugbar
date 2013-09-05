## Laravel Debugbar

Right now this is just a ServiceProvider to add https://github.com/maximebf/php-debugbar via a ServiceProvider, and add the content to the request.

Includes 2 Collectors:
 - RouteCollector: Show information about the current Route
 - ViewsCollector: Show the currently loaded views an it's data.

Require this package in your composer.json and run composer update (or run `composer require barryvdh/laravel-debugbar:dev-master` directly):

    "barryvdh/laravel-debugbar": "dev-master"

After updating composer, add the ServiceProvider to the providers array in app/config/app.php

    'Barryvdh\Debugbar\ServiceProvider',