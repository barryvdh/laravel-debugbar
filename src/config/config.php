<?php

return array(


 /*
  |--------------------------------------------------------------------------
  | Debugbar Settings
  |--------------------------------------------------------------------------
  |
  | Debugbar is enabled by default, when debug is set to true in app.php.
  |
  */
  'enabled' => \Config::get('app.debug'),

  /*
   |--------------------------------------------------------------------------
   | Vendors
   |--------------------------------------------------------------------------
   |
   | Vendor files are included by default, but can be set to false.
   | This can also be set to 'js' or 'css', to only include javascript or css vendor files.
   | Vendor files are for css: font-awesome (including fonts) en for js: jquery 1.8.3
   |
   */
  'include_vendors' => true,

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
  | DataCollectors
  |--------------------------------------------------------------------------
  |
  | Enable/disable DataCollectors
  |
  */
   'collectors' => array(
       'phpinfo' => true,           // Php version
       'messages' => true,          // Messages
       'time' => true,              // Time Datalogger
       'memory' => true,            // Memory usage
       'exceptions' => true,        // Exception displayer
       'log' => true,               // Logs from Monolog (merged in messages if enabled)
       'db' => true,                // Show database (PDO) queries and bindings
       'views' => true,             // Views with their data
       'route' => true,             // Current route information
       'laravel' => false,          // Laravel version and environment
       'events' => false,           // All events fired
       'twig' => false,             // Twig, requires barryvdh/laravel-twigbridge
       'default_request'=> false,   // Regular or special Symfony request logger
       'symfony_request'=> true,    // Only one can be enabled..
       'mail' => true,              // Catch mail messages
       'logs' => false,             // Add the latest log messages
       'files' => false,            // Show the included files
       'config' => false,           // Display config settings

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
        'pdo' => array(
            'with_params'   => true,    // Render SQL with the parameters substituted
            'quotation_char' => "'",    // The character to surround params
            'extra_connections' => array(   //Add extra connections to the PDO Collector
                // 'mysql',
            )
        ),
        'mail' => array(
            'full_log' => false
        ),

        'views' => array(
            'data' => true,
        ),
        'route' =>array(
            'label' => true   // show complete route on bar
        )
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
