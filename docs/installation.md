# Installation

!!! danger

    Use the DebugBar only in development. Do not use Debugbar on publicly accessible websites, as it will leak information from stored requests (by design).


Require this package with composer. It is recommended to only require the package for development.

```shell
composer require barryvdh/laravel-debugbar --dev
```

Laravel uses Package Auto-Discovery, so doesn't require you to manually add the ServiceProvider.

The Debugbar will be enabled when `APP_DEBUG` is `true`.

> If you use a catch-all/fallback route, make sure you load the Debugbar ServiceProvider before your own App ServiceProviders.

### Without auto-discovery

If you don't use auto-discovery, add the ServiceProvider to the providers list. For Laravel 11 or newer, add the ServiceProvider in bootstrap/providers.php. For Laravel 10 or older, add the ServiceProvider in config/app.php.

```php
Barryvdh\Debugbar\ServiceProvider::class,
```

If you want to use the facade to log messages, add this within the `register` method of `app/Providers/AppServiceProvider.php` class:

```php
public function register(): void
{
    $loader = \Illuminate\Foundation\AliasLoader::getInstance();
    $loader->alias('Debugbar', \Barryvdh\Debugbar\Facades\Debugbar::class);
}
```

The profiler is enabled by default, if you have APP_DEBUG=true. You can override that in the config (`debugbar.enabled`) or by setting `DEBUGBAR_ENABLED` in your `.env`. See more options in `config/debugbar.php`
You can also set in your config if you want to include/exclude the vendor files also (FontAwesome, Highlight.js and jQuery). If you already use them in your site, set it to false.
You can also only display the js or css vendors, by setting it to 'js' or 'css'. (Highlight.js requires both css + js, so set to `true` for syntax highlighting)

### Publish config

```shell
php artisan vendor:publish --provider="Barryvdh\Debugbar\ServiceProvider"
```

### With Octane

Make sure to add LaravelDebugbar to your flush list in `config/octane.php`.

```php
    'flush' => [
        \Barryvdh\Debugbar\LaravelDebugbar::class,
    ],
```

### With Lumen

For Lumen, register a different Provider in `bootstrap/app.php`:

```php
if (env('APP_DEBUG')) {
 $app->register(Barryvdh\Debugbar\LumenServiceProvider::class);
}
```

To change the configuration, copy the file to your config folder and enable it:

```php
$app->configure('debugbar');
```