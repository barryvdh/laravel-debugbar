<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\MessagesCollector;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Symfony\Component\VarDumper\Cloner\VarCloner;

/**
 * Collector for Laravel's Auth provider
 */
class GateCollector extends MessagesCollector
{
    /** @var ValueExporter */
    protected $exporter;
    /**
     * @param Gate $gate
     */
    public function __construct(Gate $gate)
    {
        parent::__construct('gate');
        $this->exporter = new VarCloner();

        $gate->after([$this, 'addCheck']);
    }

    public function addCheck(Authorizable $user = null, $ability, $result, $arguments = [])
    {
        $label = $result ? 'success' : 'error';

        $this->addMessage([
            'ability' => $ability,
            'result' => $result,
            ($user ? snake_case(class_basename($user)) : 'user') => ($user ? $user->id : null),
            'arguments' => $this->exporter->cloneVar($arguments),
        ], $label, false);
    }
}
