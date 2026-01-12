# Upgrade Guide

## 2.x to 3.x

### php-debugbar 3.x
The php-debugbar dependency has been updated to 3.x. This removes jQuery and font-awesome.
It should not impact your application, unless you are using custom collectors.

### Updated namespace

The new namespace is `Fruitcake\LaravelDebugbar` instead of `Barryvdh\Debugbar`. You usually do not need to change this,
unless you are manually registering the service provider / facade. The packge install is now `fruitcake/laravel-debugbar`.

### Removed 

 - SocketStorage (no longer maintained)
 - Lumen support (no longer maintained)
 - FileCollector (no longer useful)

### Other changes
 - The Query Collector now extends the php-debugbar widget. The bindings parameter has been removed in favor of 'params'.
 - Instead of 'hiddens', we now have an option 'masked' which uses the keys, not array paths.

