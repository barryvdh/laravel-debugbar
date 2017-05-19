<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Collector for Laravel's Auth provider
 */
class AuthCollector extends DataCollector implements Renderable
{
    /** @var \Illuminate\Auth\AuthManager */
    protected $auth;
    /** @var bool */
    protected $showName = false;

    /**
     * @param \Illuminate\Auth\AuthManager $auth
     */
    public function __construct($auth)
    {
        $this->auth = $auth;
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
        try {
            $user = $this->auth->user();
        } catch (\Exception $e) {
            $user = null;
        }
        return $this->getUserInformation($user);
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

        // We try to display first the username or the email attribute of the User object
        // Otherwise, the identifier, which is most likely the ID number, will be displayed as a fallback.
        try {
            if ($user->username) {
                $identifier = $user->username;
            } elseif ($user->email) {
                $identifier = $user->email;
            }
        } catch (\Exception $e) {
            $identifier = $user->getAuthIdentifier();
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
            'auth' => [
                'icon' => 'lock',
                'widget' => 'PhpDebugBar.Widgets.VariableListWidget',
                'map' => 'auth.user',
                'default' => '{}'
            ]
        ];
        if ($this->showName) {
            $widgets['auth.name'] = [
                'icon' => 'user',
                'tooltip' => 'Auth status',
                'map' => 'auth.name',
                'default' => '',
            ];
        }
        return $widgets;
    }
}
