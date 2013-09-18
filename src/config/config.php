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
  | Event logging
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
       'mail_log' => false,         // Display full mail log in messages

   )


);
