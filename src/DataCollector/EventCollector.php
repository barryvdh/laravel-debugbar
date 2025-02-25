<?php

namespace Barryvdh\Debugbar\DataCollector;

use Barryvdh\Debugbar\DataFormatter\SimpleFormatter;
use DebugBar\DataCollector\TimeDataCollector;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Str;
use Symfony\Component\VarDumper\Cloner\VarCloner;

class EventCollector extends TimeDataCollector
{
    /** @var Dispatcher */
    protected $events;

    /** @var integer */
    protected $previousTime;

    /** @var bool */
    protected $collectValues;

    public function __construct($requestStartTime = null, $collectValues = false)
    {
        parent::__construct($requestStartTime);
        $this->collectValues = $collectValues;
        $this->setDataFormatter(new SimpleFormatter());
    }

    public function onWildcardEvent($name = null, $data = [])
    {
        $currentTime = microtime(true);

        if (! $this->collectValues) {
            $this->addMeasure($name, $currentTime, $currentTime, [], null, 'Events');

            return;
        }

        $params = $this->prepareParams($data);

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
                $lines = $reflector->getStartLine() . '-' . $reflector->getEndLine();
                $listener = $reflector->getName() . ' (' . $filename . ':' . $lines . ')';
            } else {
                // Not sure if this is possible, but to prevent edge cases
                $listener = $this->getDataFormatter()->formatVar($listener);
            }

            $params['listeners.' . $i] = $listener;
        }
        $this->addMeasure($name, $currentTime, $currentTime, $params, null, 'Events');
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
            $data[$key] = htmlentities($this->getDataFormatter()->formatVar($value), ENT_QUOTES, 'UTF-8', false);
        }

        return $data;
    }

    public function collect()
    {
        $data = parent::collect();
        $data['nb_measures'] = $data['count'] = count($data['measures']);

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
