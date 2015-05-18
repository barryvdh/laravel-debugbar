<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Illuminate\Support\Contracts\ArrayableInterface;
use Illuminate\Foundation\Application;

/**
 * Collector for Laravel's Sentry Auth provider
 */
class SentryAuthCollector extends AuthCollector implements Renderable
{
    /**
     * @var boolean
     */
    protected $showName = true;

    public function __construct()
    {
        if ( ! App::bound('sentry') ) {
            throw new Exception("Cartalyst Sentry is not setup properly.");
        }
    }

    /**
     * Set to show the users name/email
     * @param bool $showName
     */
    public function setShowName($showName)
    {
        $this->showName = (bool)$showName;
    }

    /**
     * @{inheritDoc}
     */
    public function collect()
    {
        try {
            $user = \Cartalyst\Sentry\Facades\Laravel\Sentry::getUser();
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
        $identifier = $user->{\Cartalyst\Sentry\Facades\Laravel\Sentry::getLoginAttributeName()};
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
        return 'sentry_auth';
    }

    /**
     * @{inheritDoc}
     */
    public function getWidgets()
    {
        $widgets = array(
            'auth' => array(
                'icon'    => 'lock',
                'widget'  => 'PhpDebugBar.Widgets.VariableListWidget',
                'map'     => 'sentry_auth.user',
                'default' => '{}'
            )
        );
        if ($this->showName) {
            $widgets['auth.name'] = array(
                'icon'    => 'user',
                'tooltip' => 'Auth status',
                'map'     => 'sentry_auth.name',
                'default' => '',
            );
        }
        return $widgets;
    }
}

