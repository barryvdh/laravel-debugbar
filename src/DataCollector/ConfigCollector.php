<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\AssetProvider;
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
use Illuminate\Config\Repository;
use Illuminate\Events\Dispatcher;

class ConfigCollector extends \DebugBar\DataCollector\ConfigCollector
{
    protected Repository $config;

    public function __construct(Repository $config, $name = 'config')
    {
        $this->config = $config;
        parent::__construct([], $name);
    }

    public function collect(): array
    {
        // Gather data on collect
        $this->setData($this->config->all());
        return parent::collect();
    }
}
