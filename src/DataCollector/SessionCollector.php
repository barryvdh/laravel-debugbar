<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\Renderable;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionCollector extends DataCollector implements DataCollectorInterface, Renderable
{
    protected SessionInterface $session;
    /** @var array */
    protected $hiddens;

    public function __construct(SessionInterface $session, array $hiddens = [])
    {
        $this->session = $session;
        $this->hiddens = $hiddens;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(): array
    {
        $data = $this->session->all();

        foreach ($this->hiddens as $key) {
            if (Arr::has($data, $key)) {
                Arr::set($data, $key, '******');
            }
        }

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
                "widget" => "PhpDebugBar.Widgets.VariableListWidget",
                "map" => "session",
                "default" => "{}",
            ],
        ];
    }
}
