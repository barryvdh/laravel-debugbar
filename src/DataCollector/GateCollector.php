<?php

namespace Barryvdh\Debugbar\DataCollector;

use Barryvdh\Debugbar\DataFormatter\SimpleFormatter;
use DebugBar\DataCollector\MessagesCollector;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
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

    /**
     * {@inheritDoc}
     */
    protected function customizeMessageHtml($messageHtml, $message)
    {
        $pos = strpos((string) $messageHtml, 'array:5');
        if ($pos !== false) {

            $name = $message['ability'] .' ' . $message['target'] ?? '';

            $messageHtml = substr_replace($messageHtml, $name, $pos, 7);
        }

        return parent::customizeMessageHtml($messageHtml, $message);
    }

    public function addCheck($user, $ability, $result, $arguments = [])
    {
        $userKey = 'user';
        $userId = null;

        if ($user) {
            $userKey = Str::snake(class_basename($user));
            $userId = $user instanceof Authenticatable ? $user->getAuthIdentifier() : $user->getKey();
        }

        $label = $result ? 'success' : 'error';

        if ($result instanceof Response) {
            $label = $result->allowed() ? 'success' : 'error';
        }

        $target = null;
        if (isset($arguments[0])) {
            if ($arguments[0] instanceof Model) {
                $model = $arguments[0];
                $target = get_class($model) . '(' . $model->getKeyName() .'=' . $model->getKey().')';
            } else if (is_string($arguments[0])) {
                $target = $arguments[0];
            }
        }

        $this->addMessage([
            'ability' => $ability,
            'target' => $target,
            'result' => $result,
            $userKey => $userId,
            'arguments' => $this->getDataFormatter()->formatVar($arguments),
        ], $label, false);
    }
}
