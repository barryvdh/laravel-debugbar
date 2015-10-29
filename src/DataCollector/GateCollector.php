<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\MessagesCollector;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Collector for Laravel's Auth provider
 */
class GateCollector extends MessagesCollector
{
    /**
     * @param Gate $gate
     */
    public function __construct(Gate $gate)
    {
        parent::__construct('gate');

        if (method_exists($gate, 'after')) {
            $gate->after([$this, 'addCheck']);
        }
    }

    public function addCheck(Authenticatable $user, $ability, $result, $arguments = [])
    {
        $label = $result ? 'success' : 'error';

        $this->addMessage([
            'ability' => $ability,
            'result' => $result,
            'user' => $user->getAuthIdentifier(),
            'arguments' => $arguments,
        ], $label, false);
    }
}
