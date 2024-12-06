# Collectors

## Available Collectors

Laravel Debugbar comes with several collectors that gather different types of information:

### QueryCollector
Logs database queries:
```php
$debugbar->addCollector(new QueryCollector($queries));
```

### TimeDataCollector
Tracks time between points:
```php
$debugbar->addCollector(new TimeDataCollector());
$debugbar->startMeasure('render','Time for rendering');
$debugbar->stopMeasure('render');
```

### ExceptionCollector
Shows exception data:
```php
$debugbar->addCollector(new ExceptionCollector());
```

## Custom Collectors

You can create your own collectors by implementing the `DebugBar\DataCollector\DataCollectorInterface`:

```php
use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;

class MyCollector extends DataCollector implements Renderable
{
    public function collect()
    {
        return [
            'message' => 'Custom data here'
        ];
    }

    public function getName()
    {
        return 'custom';
    }
}
```

Register your collector:

```php
$debugbar->addCollector(new MyCollector());
```