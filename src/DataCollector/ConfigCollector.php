<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\DataCollector;

class ConfigCollector extends \DebugBar\DataCollector\ConfigCollector
{
    public function collect(): array
    {
        // Gather data on collect
        $this->setData(config()->all());

        return parent::collect();
    }
}
