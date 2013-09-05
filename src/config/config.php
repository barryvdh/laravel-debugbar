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
  | Events aren't logged by default, but this can be enabled, by setting to true.
  |
  */
  'log_events' => false,


);
