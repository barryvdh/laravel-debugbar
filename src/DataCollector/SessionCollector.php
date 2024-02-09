<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\Renderable;
use Illuminate\Support\Arr;

class SessionCollector extends DataCollector implements DataCollectorInterface, Renderable
{
    /** @var  \Symfony\Component\HttpFoundation\Session\SessionInterface|\Illuminate\Contracts\Session\Session $session */
    protected $session;
    /** @var array */
    protected $hiddens;

    /**
     * Create a new SessionCollector
     *
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface|\Illuminate\Contracts\Session\Session $session
     * @param array $hiddens
     */
    public function __construct($session, $hiddens = [])
    {
        $this->session = $session;
        $this->hiddens = $hiddens;
    }

    /**
     * {@inheritdoc}
     */
    public function collect()
    {
        $data = $this->session->all();

        foreach ($this->hiddens as $key) {
            if (Arr::has($data, $key)) {
                Arr::set($data, $key, '******');
            }
        }

        foreach ($data as $key => $value) {
            $data[$key] = is_string($value) ? $value : $this->formatVar($value);
        }

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'session';
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets()
    {
        return [
            "session" => [
                "icon" => "archive",
                "widget" => "PhpDebugBar.Widgets.VariableListWidget",
                "map" => "session",
                "default" => "{}"
            ]
        ];
    }
}
