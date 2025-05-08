<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\TimeDataCollector;
use DebugBar\DataFormatter\HasDataFormatter;
use Illuminate\Cache\Events\{
    CacheFlushed,
    CacheFlushFailed,
    CacheFlushing,
    CacheHit,
    CacheMissed,
    ForgettingKey,
    KeyForgetFailed,
    KeyForgotten,
    KeyWriteFailed,
    KeyWritten,
    RetrievingKey,
    WritingKey,
};
use Illuminate\Events\Dispatcher;

class CacheCollector extends TimeDataCollector
{
    use HasDataFormatter;

    /** @var bool */
    protected $collectValues;

    /** @var array */
    protected $eventStarts = [];

    /** @var array */
    protected $classMap = [
        CacheHit::class => ['hit', RetrievingKey::class],
        CacheMissed::class => ['missed', RetrievingKey::class],
        CacheFlushed::class => ['flushed', CacheFlushing::class],
        CacheFlushFailed::class => ['flush_failed', CacheFlushing::class],
        KeyWritten::class => ['written', WritingKey::class],
        KeyWriteFailed::class => ['write_failed', WritingKey::class],
        KeyForgotten::class => ['forgotten', ForgettingKey::class],
        KeyForgetFailed::class => ['forget_failed', ForgettingKey::class],
    ];

    public function __construct($requestStartTime, $collectValues)
    {
        parent::__construct($requestStartTime);

        $this->collectValues = $collectValues;
    }

    public function onCacheEvent($event)
    {
        $class = get_class($event);
        $params = get_object_vars($event);
        $label = $this->classMap[$class][0];

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
        $startHashKey = $this->getEventHash($this->classMap[$class][1] ?? '', $params);
        $startTime = $this->eventStarts[$startHashKey] ?? $time;
        $this->addMeasure($label . "\t" . ($params['key'] ?? ''), $startTime, $time, $params);
    }

    public function onStartCacheEvent($event)
    {
        $startHashKey = $this->getEventHash(get_class($event), get_object_vars($event));
        $this->eventStarts[$startHashKey] = microtime(true);
    }

    private function getEventHash(string $class, array $params): string
    {
        unset($params['value']);

        return $class . ':' . substr(hash('sha256', json_encode($params)), 0, 12);
    }

    public function subscribe(Dispatcher $dispatcher)
    {
        foreach (array_keys($this->classMap) as $eventClass) {
            $dispatcher->listen($eventClass, [$this, 'onCacheEvent']);
        }

        $startEvents = array_unique(array_filter(array_map(
            fn ($values) => $values[1] ?? null,
            array_values($this->classMap)
        )));

        foreach ($startEvents as $eventClass) {
            $dispatcher->listen($eventClass, [$this, 'onStartCacheEvent']);
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
