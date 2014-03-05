<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Illuminate\Auth\AuthManager;
use Illuminate\Auth\UserInterface;

/**
 * Collector for Laravel's Auth provider
 */
class IlluminateAuthCollector extends DataCollector implements Renderable
{
    /**
     * @var \Illuminate\Auth\Guard
     */
    protected $auth;

    /**
     * @param \Illuminate\Auth\AuthManager $auth
     */
    public function __construct(AuthManager $auth)
    {
        // Get the driver behind the AuthManager (i.e. the Guard instance)
        $this->auth = $auth->driver();
    }

    /**
     * @{inheritDoc}
     */
    public function collect()
    {
        return $this->getUserInformation($this->auth->user());
    }

    /**
     * Get displayed user information
     * @return array
     */
    protected function getUserInformation(UserInterface $user = null)
    {
        // Defaults
        if (is_null($user)) {
            return array(
                'user' => 'Guest',
                'is_guest' => true,
            );
        }

        // The default auth identifer is the ID number, which isn't all that
        // useful. Try username and email.
        $identifier = $user->getAuthIdentifier();
        if (is_numeric($identifier)) {
            if ($user->username) {
                $identifier = $user->username;
            }else if ($user->email) {
                $identifier = $user->email;
            }
        }

        return array(
            'user' => $identifier,
            'is_guest' => false,
        );
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
        return array(
            'user' => array(
                'icon' => 'user',
                'tooltip' => 'Auth status',
                'map' => 'auth.user',
                'default' => '',
            ),
        );
    }
}
