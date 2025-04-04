<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\TimeDataCollector;
use DebugBar\DataFormatter\HasDataFormatter;
use Illuminate\Cache\Events\{
    CacheFlushed,
    CacheHit,
    CacheMissed,
    KeyForgetFailed,
    KeyForgotten,
    KeyWriteFailed,
    KeyWritten,
};
use Illuminate\Events\Dispatcher;

class CacheCollector extends TimeDataCollector
{
    use HasDataFormatter;

    /** @var bool */
    protected $collectValues;

    /** @var array */
    protected $classMap = [
        CacheHit::class => 'hit',
        CacheMissed::class => 'missed',
        CacheFlushed::class => 'flushed',
        KeyWritten::class => 'written',
        KeyWriteFailed::class => 'write_failed',
        KeyForgotten::class => 'forgotten',
        KeyForgetFailed::class => 'forget_failed',
    ];

    public function __construct($requestStartTime, $collectValues)
    {
        parent::__construct();

        $this->collectValues = $collectValues;
    }

    public function onCacheEvent($event)
    {
        $class = get_class($event);
        $params = get_object_vars($event);

        $label = $this->classMap[$class];

        if (isset($params['value'])) {
            if ($this->collectValues) {
                if ($this->isHtmlVarDumperUsed()) {
                    $params['value'] = $this->getVarDumper()->renderVar($params['value']);
                } else {
                    $params['value'] = htmlspecialchars($this->getDataFormatter()->formatVar($params['value']));
                }
            } else {
                unset($params['value']);
            }
        }


        if (!empty($params['key'] ?? null) && in_array($label, ['hit', 'written'])) {
            $params['delete'] = route('debugbar.cache.delete', [
                'key' => urlencode($params['key']),
                'tags' => !empty($params['tags']) ? json_encode($params['tags']) : '',
            ]);
        }

        $time = microtime(true);
        $this->addMeasure($label . "\t" . ($params['key'] ?? ''), $time, $time, $params);
    }

    public function subscribe(Dispatcher $dispatcher)
    {
        foreach (array_keys($this->classMap) as $eventClass) {
            $dispatcher->listen($eventClass, [$this, 'onCacheEvent']);
        }
    }

    public function collect()
    {
        $data = parent::collect();
        $data['nb_measures'] = $data['count'] = count($data['measures']);

        return $data;
    }

    public function getName()
    {
        return 'cache';
    }

    public function getWidgets()
    {
        return [
          'cache' => [
            'icon' => 'clipboard',
            'widget' => 'PhpDebugBar.Widgets.LaravelCacheWidget',
            'map' => 'cache',
            'default' => '{}',
          ],
          'cache:badge' => [
            'map' => 'cache.nb_measures',
            'default' => 'null',
          ],
        ];
    }
}
