!!! warning

    Debugbar can slow the application down (because it has to gather and render data). So when experiencing slowness, try disabling some of the collectors.

## Collectors
This package includes some custom collectors:

- QueryCollector: Show all queries, including binding + timing
- RouteCollector: Show information about the current Route.
- ViewCollector: Show the currently loaded views. (Optionally: display the shared data)
- EventsCollector: Show all events
- LaravelCollector: Show the Laravel version and Environment. (disabled by default)
- SymfonyRequestCollector: replaces the RequestCollector with more information about the request/response
- LogsCollector: Show the latest log entries from the storage logs. (disabled by default)
- FilesCollector: Show the files that are included/required by PHP. (disabled by default)
- ConfigCollector: Display the values from the config files. (disabled by default)
- CacheCollector: Display all cache events. (disabled by default)

Bootstraps the following collectors for Laravel:
- LogCollector: Show all Log messages
- SymfonyMailCollector for Mail

And the default collectors:
- PhpInfoCollector
- MessagesCollector
- TimeDataCollector (With Booting and Application timing)
- MemoryCollector
- ExceptionsCollector

It also provides a facade interface (Debugbar) for easy logging Messages, Exceptions and Time