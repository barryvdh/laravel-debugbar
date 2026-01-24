# Upgrade Guide

## 3.x to 4.x

### php-debugbar 3.x
The php-debugbar dependency has been updated to 3.x. This removes jQuery and font-awesome.
This should not impact your application, unless you are using custom collectors.

### Updated namespace

The new namespace is `Fruitcake\LaravelDebugbar` instead of `Barryvdh\Debugbar`. You usually do not need to change this,
unless you are manually registering the service provider / facade. The packge install is now `fruitcake/laravel-debugbar`.

### Removed 

 - SocketStorage (no longer maintained)
 - Lumen support (no longer maintained)
 - FileCollector (no longer useful)
 - `start_measure()`, `add_measure()`, `stop_measure()`, `measure()` helper methods (use `debugbar()->startMeasure()` etc. instead)

### Other changes
 - The Query Collector now extends the php-debugbar widget. The bindings parameter has been removed in favor of 'params'.
 - Instead of 'hiddens', we now have an option 'masked' which uses the keys, not array paths.
 - Ineratia has its own collector / config. Update your config accordingly.

### For packages extending Laravel Debugbar
 - modifyResponse has changed to handleResponse, and is now done with a listener instead of middleware
 - the HttpDriver is now session-less, and uses cookies.
 - Octane keeps the LaravelDebugbar state, so has to reset collectors. You can remove Laravel Debugbar from the flush config.
