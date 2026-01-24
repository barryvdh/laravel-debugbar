<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\Renderable;

class SessionCollector extends DataCollector implements DataCollectorInterface, Renderable
{
    /**
     * {@inheritdoc}
     */
    public function collect(): array
    {
        if (!request()->hasSession()) {
            return [];
        }

        $data = $this->hideMaskedValues(request()->session()->all());

        foreach ($data as $key => $value) {
            $data[$key] = is_string($value) ? $value : $this->getDataFormatter()->formatVar($value);
        }

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'session';
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets(): array
    {
        return [
            "session" => [
                "icon" => "archive",
                "widget" => "PhpDebugBar.Widgets.HtmlVariableListWidget",
                "map" => "session",
                "default" => "{}",
            ],
        ];
    }
}
