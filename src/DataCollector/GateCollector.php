<?php

namespace Barryvdh\Debugbar\DataCollector;

use Barryvdh\Debugbar\DataFormatter\SimpleFormatter;
use DebugBar\DataCollector\MessagesCollector;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Authenticatable;
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
        $gate->after(function ($user = null, $ability, $result, $arguments = []) {
            $this->addCheck($user, $ability, $result, $arguments);
        });
    }

    public function addCheck($user = null, $ability, $result, $arguments = [])
    {
        $userKey = 'user';
        $userId = null;

        if ($user) {
            $userKey = snake_case(class_basename($user));
            $userId = $user instanceof Authenticatable ? $user->getAuthIdentifier() : $user->id;
        }

        $label = $result ? 'success' : 'error';

        $this->addMessage([
            'ability' => $ability,
            'result' => $result,
            $userKey => $userId,
            'arguments' => $this->getDataFormatter()->formatVar($arguments),
        ], $label, false);
    }
}
