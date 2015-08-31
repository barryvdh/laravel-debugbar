<?php
namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\TimeDataCollector;
use Illuminate\Events\Dispatcher;
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

    public function onWildcardEvent()
    {
        $name = $this->events->firing();
        $time = microtime(true);

        // Get the arguments passed to the event
        $params = $this->prepareParams(func_get_args());

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
        $events->listen('*', array($this, 'onWildcardEvent'));
    }

    protected function prepareParams($params)
    {
        $data = array();
        foreach ($params as $key => $value) {
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
        return array(
          "events" => array(
            "icon" => "tasks",
            "widget" => "PhpDebugBar.Widgets.TimelineWidget",
            "map" => "event",
            "default" => "{}",
          ),
          'events:badge' => array(
            'map' => 'event.nb_measures',
            'default' => 0,
          ),
        );
    }
}
