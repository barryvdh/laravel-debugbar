<?php

namespace Barryvdh\Debugbar\DataCollector;

use Barryvdh\Debugbar\DataFormatter\SimpleFormatter;
use DebugBar\DataCollector\MessagesCollector;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Authenticatable;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Illuminate\Support\Str;

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
        $gate->after(function ($user, $ability, $result, $arguments = []) {
            $this->addCheck($user, $ability, $result, $arguments);
        });
    }

    public function addCheck($user, $ability, $result, $arguments = [])
    {
        $userKey = 'user';
        $userId = null;

        if ($user) {
            $userKey = Str::snake(class_basename($user));
            $userId = $user instanceof Authenticatable ? $user->getAuthIdentifier() : $user->id;
        }

        $label = $result ? 'success' : 'error';

        // Response::allowed() was added in Laravel 6.x
        if ($result instanceof Response && method_exists($result, 'allowed')) {
            $label = $result->allowed() ? 'success' : 'error';
        }

        $this->addMessage([
            'ability' => $ability,
            'result' => $result,
            $userKey => $userId,
            'arguments' => $this->getDataFormatter()->formatVar($arguments),
        ], $label, false);
    }
}
