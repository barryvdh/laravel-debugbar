<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Illuminate\Auth\Recaller;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Str;
use Illuminate\Contracts\Support\Arrayable;


/**
 * Collector for Laravel's Auth provider
 */
class MultiAuthCollector extends DataCollector implements Renderable
{
    /** @var array $guards */
    protected $guards;

    /** @var \Illuminate\Auth\AuthManager */
    protected $auth;

    /** @var bool */
    protected $showName = false;

    /**
     * @param \Illuminate\Auth\AuthManager $auth
     * @param array $guards
     */
    public function __construct($auth, $guards)
    {
        $this->auth = $auth;
        $this->guards = $guards;
    }

    /**
     * Set to show the users name/email
     * @param bool $showName
     */
    public function setShowName($showName)
    {
        $this->showName = (bool) $showName;
    }

    /**
     * @{inheritDoc}
     */
    public function collect()
    {
        $data = [];
        $names = '';

        foreach($this->guards as $guardName => $config) {
            try {
                $user = $this->resolveUser($this->auth->guard($guardName), $config);
            } catch (\Exception $e) {
                continue;
            }

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

    private function resolveUser(Guard $guard, array $config)
    {
        // if we're logging in using remember token
        // then we must resolve user „manually”
        // to prevent csrf token regeneration
        if ($guard instanceof SessionGuard) {

            $recaller = new Recaller($guard->getRequest()->cookies->get($guard->getRecallerName()));
            $provider = $this->auth->createUserProvider($config['provider']);

            $user = $provider->retrieveByToken($recaller->id(), $recaller->token());
            if ($user) {
                return $user;
            }
        }

        return $guard->user();
    }

    /**
     * Get displayed user information
     * @param \Illuminate\Auth\UserInterface $user
     * @return array
     */
    protected function getUserInformation($user = null)
    {
        // Defaults
        if (is_null($user)) {
            return [
                'name' => 'Guest',
                'user' => ['guest' => true],
            ];
        }

        // The default auth identifer is the ID number, which isn't all that
        // useful. Try username and email.
        $identifier = $user instanceof Authenticatable ? $user->getAuthIdentifier() : $user->id;
        if (is_numeric($identifier)) {
            try {
                if (isset($user->username)) {
                    $identifier = $user->username;
                } elseif (isset($user->email)) {
                    $identifier = $user->email;
                }
            } catch (\Throwable $e) {
            }
        }

        return [
            'name' => $identifier,
            'user' => $user instanceof Arrayable ? $user->toArray() : $user,
        ];
    }

    /**
     * @{inheritDoc}
     */
    public function getName()
    {
        return 'auth';
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
