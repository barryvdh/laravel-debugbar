<?php

namespace Barryvdh\Debugbar\DataCollector;

use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Str;

/**
 * Collector for Laravel's Auth provider
 */
class MultiAuthCollector extends AuthCollector
{
    /** @var array $guards */
    protected $guards;

    /**
     * @param \Illuminate\Auth\AuthManager $auth
     * @param array $guards
     */
    public function __construct($auth, $guards)
    {
        parent::__construct($auth);
        $this->guards = $guards;
    }


    /**
     * @{inheritDoc}
     */
    public function collect()
    {
        $data = [];
        $names = '';

        foreach($this->guards as $guardName) {
            $user = $this->resolveUser($this->auth->guard($guardName));

            $data['guards'][$guardName] = $this->getUserInformation($user);

            if(!is_null($user)) {
                $names .= $guardName . ": " . $data['guards'][$guardName]['name'] . ', ';
            }
        }

        foreach ($data['guards'] as $key => $var) {
            if (!is_string($data['guards'][$key])) {
                $data['guards'][$key] = $this->formatVar($var);
            }
        }

        $data['names'] = rtrim($names, ', ');

        return $data;
    }

    private function resolveUser(Guard $guard)
    {
        // if we're logging in using remember token
        // then we must resolve user „manually”
        // to prevent csrf token regeneration

        $recaller = $guard instanceof SessionGuard
            ? $guard->getRequest()->cookies->get($guard->getRecallerName())
            : null;

        if (is_string($recaller) && Str::contains($recaller, '|')) {
            $segments = explode('|', $recaller);
            if (count($segments) == 2 && trim($segments[0]) !== '' && trim($segments[1]) !== '') {
                return $guard->getProvider()->retrieveByToken($segments[0], $segments[1]);
            }
        }
        return $guard->user();
    }

    /**
     * @{inheritDoc}
     */
    public function getWidgets()
    {
        $widgets = [
            "auth" => [
                "icon" => "lock",
                "widget" => "PhpDebugBar.Widgets.VariableListWidget",
                "map" => "auth.guards",
                "default" => "{}"
            ]
        ];

        if ($this->showName) {
            $widgets['auth.name'] = [
                'icon' => 'user',
                'tooltip' => 'Auth status',
                'map' => 'auth.names',
                'default' => '',
            ];
        }

        return $widgets;
    }
}
