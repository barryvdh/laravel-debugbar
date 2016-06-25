<?php

namespace Barryvdh\Debugbar\DataCollector;

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
            $user = $this->auth->guard($guardName)->user();
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
