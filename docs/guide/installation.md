# Installation

## Using Composer

```bash
composer require barryvdh/laravel-debugbar --dev
```

## Laravel Setup

The Debugbar ServiceProvider will be discovered and registered automatically.

If you want to manually register the service provider, add the following to your `config/app.php`:

```php
Barryvdh\Debugbar\ServiceProvider::class,
```

### Facade (Optional)

You can register the facade in your `config/app.php`:

```php
'Debugbar' => Barryvdh\Debugbar\Facades\Debugbar::class,
```

## Configuration

To publish the config file to `config/debugbar.php`, run:

```bash
php artisan vendor:publish --provider="Barryvdh\Debugbar\ServiceProvider"
```

::: warning
Make sure you configure your environment properly. The debugbar adds overhead, so it should be disabled in production!