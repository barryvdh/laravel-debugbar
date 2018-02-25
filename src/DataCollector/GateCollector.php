<?php

namespace Barryvdh\Debugbar\DataCollector;

use Barryvdh\Debugbar\DataFormatter\SimpleFormatter;
use DebugBar\DataCollector\MessagesCollector;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Symfony\Component\VarDumper\Cloner\VarCloner;

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
        $this->setDataFormatter(new SimpleFormatter());
        $gate->after([$this, 'addCheck']);
    }

    public function addCheck(Authorizable $user = null, $ability, $result, $arguments = [])
    {
        $label = $result ? 'success' : 'error';

        $this->addMessage([
            'ability' => $ability,
            'result' => $result,
            ($user ? snake_case(class_basename($user)) : 'user') => ($user ? $user->id : null),
            'arguments' => $this->getDataFormatter()->formatVar($arguments),
        ], $label, false);
    }
}
