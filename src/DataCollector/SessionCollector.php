<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\Renderable;

class SessionCollector extends DataCollector implements DataCollectorInterface, Renderable
{
    /** @var  \Symfony\Component\HttpFoundation\Session\SessionInterface $session */
    protected $session;

    /**
     * Create a new SessionCollector
     *
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
     */
    public function __construct($session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function collect()
    {
        $data = array();
        foreach ($this->session->all() as $key => $value) {
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
        return array(
            "session" => array(
                "icon" => "archive",
                "widget" => "PhpDebugBar.Widgets.VariableListWidget",
                "map" => "session",
                "default" => "{}"
            )
        );
    }
}
