<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\DataCollector;

use Illuminate\Config\Repository;

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
