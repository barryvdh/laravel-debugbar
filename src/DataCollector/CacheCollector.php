<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\DataCollector;

use DebugBar\DataCollector\AssetProvider;
use DebugBar\DataCollector\TimeDataCollector;
use Illuminate\Cache\Events\{CacheEvent,
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
    WritingKey};
use Illuminate\Events\Dispatcher;

class CacheCollector extends TimeDataCollector implements AssetProvider
{
    protected bool $collectValues = false;

    protected array $eventStarts = [];

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

    public function __construct(float $requestStartTime, bool $collectValues)
    {
        parent::__construct($requestStartTime);

        $this->collectValues = $collectValues;
        $this->memoryMeasure = true;
    }

    public function onCacheEvent(CacheEvent $event): void
    {
        $class = get_class($event);
        $params = get_object_vars($event);
        $label = $this->classMap[$class][0];

        if (isset($params['value'])) {
            $params['memoryUsage'] = strlen(serialize($params['value'])) * 8;

            if (!$this->collectValues) {
                unset($params['value']);
            }
        }

        $time = microtime(true);
        $startHashKey = $this->getEventHash($this->classMap[$class][1] ?? '', $params);
        $startTime = $this->eventStarts[$startHashKey] ?? $time;

        $this->addMeasure($label . "\t" . ($params['key'] ?? ''), $startTime, $time, $params);

        if (in_array($label, ['hit', 'written'], true)) {
            $measureIndex = array_key_last($this->measures);
            $this->measures[$measureIndex]['delete_url'] = url()->signedRoute('debugbar.cache.delete', [
                'key' => $event->key,
                'tags' => $params['tags'] ?? [],
            ]);
        }
    }

    public function onStartCacheEvent(mixed $event): void
    {
        $startHashKey = $this->getEventHash(get_class($event), get_object_vars($event));
        $this->eventStarts[$startHashKey] = microtime(true);
    }

    protected function getEventHash(string $class, array $params): string
    {
        unset($params['value']);

        return $class . ':' . substr(hash('sha256', json_encode($params)), 0, 12);
    }

    public function subscribe(Dispatcher $dispatcher): void
    {
        foreach (array_keys($this->classMap) as $eventClass) {
            $dispatcher->listen($eventClass, [$this, 'onCacheEvent']);
        }

        $startEvents = array_unique(array_filter(array_map(
            fn($values) => $values[1] ?? null,
            array_values($this->classMap),
        )));

        foreach ($startEvents as $eventClass) {
            $dispatcher->listen($eventClass, [$this, 'onStartCacheEvent']);
        }
    }

    public function collect(): array
    {
        $data = parent::collect();
        $data['nb_measures'] = $data['count'] = count($data['measures']);

        return $data;
    }

    public function getName(): string
    {
        return 'cache';
    }

    public function getWidgets(): array
    {
        return [
            'cache' => [
                'icon' => 'clipboard-text',
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

    public function getAssets(): array
    {
        return [
            'js' => __DIR__ . '/../../resources/cache/widget.js',
        ];
    }
}
