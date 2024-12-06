# Configuration

## Basic Configuration

The configuration file is located at `config/debugbar.php`. Here are the main options:

```php
return [
    'enabled' => env('DEBUGBAR_ENABLED', null),
    'storage' => [
        'enabled'    => true,
        'driver'     => 'file',
        'path'       => storage_path('debugbar'),
        'connection' => null,
        'provider'   => '',
    ],
];
```

## Environment Settings

### Production Environment

In your production environment, you should set:

```env
DEBUGBAR_ENABLED=false
```

### Development Environment

For local development:

```env
DEBUGBAR_ENABLED=true
```

## Collectors Configuration

You can enable/disable specific collectors:

```php
'collectors' => [
    'phpinfo'         => true,  // PHP info
    'messages'        => true,  // Messages
    'time'           => true,  // Time Datalogger
    'memory'         => true,  // Memory usage
    'exceptions'     => true,  // Exception displayer
    'log'            => true,  // Logs from Monolog
    'db'             => true,  // Database operations
    'views'          => true,  // Views with their data
    'route'          => true,  // Current route information
    'auth'           => false, // Display Laravel authentication status
    'gate'           => true,  // Display Laravel Gate checks
    'session'        => true,  // Display session data
],
```

::: tip
You can customize the appearance and behavior of each collector through additional configuration options.
:::