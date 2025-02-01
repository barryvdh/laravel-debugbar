<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Illuminate\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class LaravelCollector extends DataCollector implements Renderable
{
    /** @var \Illuminate\Foundation\Application $app */
    protected $app;
    
    /**
     * @param Application $app
     */
    public function __construct(?Application $app = null)
    {
        $this->app = $app ?: app();
    }

    /**
     * {@inheritDoc}
     */
    public function collect()
    {
        return [
            "version" => Str::of($this->app->version())->explode('.')->first(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function gatherData()
    {
        return [
            'Laravel Version' => $this->app->version(),
            'PHP Version' => phpversion(),
            'Environment' => $this->app->environment(),
            'Debug Mode' => config('app.debug') ? 'Enabled' : 'Disabled',
            'URL' => Str::of(config('app.url'))->replace(['http://', 'https://'], ''),
            'Timezone' => config('app.timezone'),
            'Locale' => config('app.locale'),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'laravel';
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets()
    {
        return [
            "version" => [
                "icon" => "laravel phpdebugbar-fab",
                "tooltip" => $this->gatherData(),
                "map" => "laravel.version",
                "default" => ""
            ],
        ];
    }
}
