<?php
namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\TimeDataCollector;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\DataCollector\Util\ValueExporter;

class EventCollector extends TimeDataCollector
{
    /** @var Dispatcher */
    protected $events;

    /** @var ValueExporter */
    protected $exporter;

    public function __construct($requestStartTime = null)
    {
        parent::__construct($requestStartTime);

        $this->exporter = new ValueExporter();
    }

    public function onWildcardEvent($name = null, $data = [])
    {
        // Pre-Laravel 5.4, using 'firing' to get the current event name.
        if (method_exists($this->events, 'firing')) {
            $name = $this->events->firing();

            // Get the arguments passed to the event
            $data = func_get_args();
        }

        $params = $this->prepareParams($data);
        $time = microtime(true);

        // Find all listeners for the current event
        foreach ($this->events->getListeners($name) as $i => $listener) {

            // Check if it's an object + method name
            if (is_array($listener) && count($listener) > 1 && is_object($listener[0])) {
                list($class, $method) = $listener;

                // Skip this class itself
                if ($class instanceof static) {
                    continue;
                }

                // Format the listener to readable format
                $listener = get_class($class) . '@' . $method;

            // Handle closures
            } elseif ($listener instanceof \Closure) {
                $reflector = new \ReflectionFunction($listener);

                // Skip our own listeners
                if ($reflector->getNamespaceName() == 'Barryvdh\Debugbar') {
                    continue;
                }

                // Format the closure to a readable format
                $filename = ltrim(str_replace(base_path(), '', $reflector->getFileName()), '/');
                $listener = $reflector->getName() . ' (' . $filename . ':' . $reflector->getStartLine() . '-' . $reflector->getEndLine() . ')';
            } else {
                // Not sure if this is possible, but to prevent edge cases
                $listener = $this->formatVar($listener);
            }

            $params['listeners.' . $i] = $listener;
        }
        $this->addMeasure($name, $time, $time, $params);
    }

    public function subscribe(Dispatcher $events)
    {
        $this->events = $events;
        $events->listen('*', [$this, 'onWildcardEvent']);
    }

    protected function prepareParams($params)
    {
        $data = [];
        foreach ($params as $key => $value) {
            if (is_object($value) && Str::is('Illuminate\*\Events\*', get_class($value))) {
                $value =  $this->prepareParams(get_object_vars($value));
            }
            $data[$key] = htmlentities($this->exporter->exportValue($value), ENT_QUOTES, 'UTF-8', false);
        }

        return $data;
    }

    public function collect()
    {
        $data = parent::collect();
        $data['nb_measures'] = count($data['measures']);

        return $data;
    }

    public function getName()
    {
        return 'event';
    }

    public function getWidgets()
    {
        return [
          "events" => [
            "icon" => "tasks",
            "widget" => "PhpDebugBar.Widgets.TimelineWidget",
            "map" => "event",
            "default" => "{}",
          ],
          'events:badge' => [
            'map' => 'event.nb_measures',
            'default' => 0,
          ],
        ];
    }
}
