<?php

use Illuminate\Support\Facades\Config;

return array(

    /*
     |--------------------------------------------------------------------------
     | Debugbar Settings
     |--------------------------------------------------------------------------
     |
     | Debugbar is enabled by default, when debug is set to true in app.php.
     |
     */

    'enabled' => Config::get('app.debug'),

    /*
     |--------------------------------------------------------------------------
     | Storage settings
     |--------------------------------------------------------------------------
     |
     | DebugBar stores data for session/ajax requests.
     | You can disable this, so the debugbar stores data in headers/session,
     | but this can cause problems with large data collectors.
     | By default, file storage (in the storage folder) is used. Redis and PDO
     | can also be used. For PDO, run the package migrations first.
     |
     */
    'storage' => array(
        'enabled' => true,
        'driver' => 'file', // redis, file, pdo
        'path' => storage_path() . '/debugbar', // For file driver
        'connection' => null,   // Leave null for default connection (Redis/PDO)
    ),

    /*
     |--------------------------------------------------------------------------
     | Vendors
     |--------------------------------------------------------------------------
     |
     | Vendor files are included by default, but can be set to false.
     | This can also be set to 'js' or 'css', to only include javascript or css vendor files.
     | Vendor files are for css: font-awesome (including fonts) and highlight.js (css files)
     | and for js: jquery and and highlight.js
     | So if you want syntax highlighting, set it to true.
     | jQuery is set to not conflict with existing jQuery scripts.
     |
     */

    'include_vendors' => true,

    /*
     |--------------------------------------------------------------------------
     | Path prefix for asset routes
     |--------------------------------------------------------------------------
     |
     | In some rare cases (if, for example, your Laravel installation is located in a virtual folder)
     | you may want to make DebugBar use specific path prefix in routing.
     | For example, if you put your Laravel project into /laravel virtual (not physical) folder, Laravel
     | may still omit "/laravel" prefix when building links for you. In this case you can set the prefix manually
     |
     */

    'path_prefix' => '',

    /*
     |--------------------------------------------------------------------------
     | Capture Ajax Requests
     |--------------------------------------------------------------------------
     |
     | The Debugbar can capture Ajax requests and display them. If you don't want this (ie. because of errors),
     | you can use this option to disable sending the data through the headers.
     |
     */

    'capture_ajax' => true,

    /*
     |--------------------------------------------------------------------------
     | Capture Console Commands
     |--------------------------------------------------------------------------
     |
     | The Debugbar can listen to Artisan commands. You can view them with the browse button in the Debugbar.
     |
     */

    'capture_console' => false,

    /*
     |--------------------------------------------------------------------------
     | DataCollectors
     |--------------------------------------------------------------------------
     |
     | Enable/disable DataCollectors
     |
     */

    'collectors' => array(
        'phpinfo'         => true,  // Php version
        'messages'        => true,  // Messages
        'time'            => true,  // Time Datalogger
        'memory'          => true,  // Memory usage
        'exceptions'      => true,  // Exception displayer
        'log'             => true,  // Logs from Monolog (merged in messages if enabled)
        'db'              => true,  // Show database (PDO) queries and bindings
        'views'           => true,  // Views with their data
        'route'           => true,  // Current route information
        'laravel'         => false, // Laravel version and environment
        'events'          => false, // All events fired
        'default_request' => false, // Regular or special Symfony request logger
        'symfony_request' => true,  // Only one can be enabled..
        'mail'            => true,  // Catch mail messages
        'logs'            => false, // Add the latest log messages
        'files'           => false, // Show the included files
        'config'          => false, // Display config settings
        'auth'            => false, // Display Laravel authentication status
        'session'         => false, // Display session data in a separate tab
    ),

    /*
     |--------------------------------------------------------------------------
     | Extra options
     |--------------------------------------------------------------------------
     |
     | Configure some DataCollectors
     |
     */

    'options' => array(
        'auth' => array(
            'show_name' => false,   // Also show the users name/email in the debugbar
        ),
        'db' => array(
            'with_params'       => true,   // Render SQL with the parameters substituted
            'timeline'          => false,  // Add the queries to the timeline
            'backtrace'         => false,  // EXPERIMENTAL: Use a backtrace to find the origin of the query in your files.
            'explain' => array(            // EXPERIMENTAL: Show EXPLAIN output on queries
                'enabled' => false,
                'types' => array('SELECT'), // array('SELECT', 'INSERT', 'UPDATE', 'DELETE'); for MySQL 5.6.3+
            ),
            'hints'             => true,    // Show hints for common mistakes
        ),
        'mail' => array(
            'full_log' => false
        ),
        'views' => array(
            'data' => false,    //Note: Can slow down the application, because the data can be quite large..
        ),
        'route' => array(
            'label' => true  // show complete route on bar
        ),
        'logs' => array(
            'file' => null
        ),
    ),

    /*
     |--------------------------------------------------------------------------
     | Inject Debugbar in Response
     |--------------------------------------------------------------------------
     |
     | Usually, the debugbar is added just before <body>, by listening to the
     | Response after the App is done. If you disable this, you have to add them
     | in your template yourself. See http://phpdebugbar.com/docs/rendering.html
     |
     */

    'inject' => true,

);
