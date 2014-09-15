<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Illuminate\Support\Contracts\ArrayableInterface;

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
            return array(
                'name' => 'Guest',
                'user' => array('guest' => true),
            );
        }

        // The default auth identifer is the ID number, which isn't all that
        // useful. Try username and email.
        $identifier = $user->getAuthIdentifier();
        if (is_numeric($identifier)) {
            try {
                if ($user->username) {
                    $identifier = $user->username;
                } elseif ($user->email) {
                    $identifier = $user->email;
                }
            } catch (\Exception $e) {
            }
        }

        return array(
            'name' => $identifier,
            'user' => $user instanceof ArrayableInterface ? $user->toArray() : $user,
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
        $widgets = array(
            'auth' => array(
                'icon' => 'lock',
                'widget' => 'PhpDebugBar.Widgets.VariableListWidget',
                'map' => 'auth.user',
                'default' => '{}'
            )
        );
        if ($this->showName) {
            $widgets['auth.name'] = array(
                'icon' => 'user',
                'tooltip' => 'Auth status',
                'map' => 'auth.name',
                'default' => '',
            );
        }
        return $widgets;
    }
}
