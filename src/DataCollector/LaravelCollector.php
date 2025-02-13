<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Foundation\Application;
use Illuminate\Support\Str;

class LaravelCollector extends DataCollector implements Renderable
{
    /**
     * @param Application $app
     */
    public function __construct(protected ApplicationContract $laravel)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function collect()
    {
        return [
            "version" => Str::of($this->laravel->version())->explode('.')->first() . '.x',
            'tooltip' => [
                'Laravel Version' => $this->laravel->version(),
                'PHP Version' => phpversion(),
                'Environment' => $this->laravel->environment(),
                'Debug Mode' => config('app.debug') ? 'Enabled' : 'Disabled',
                'URL' => Str::of(config('app.url'))->replace(['http://', 'https://'], ''),
                'Timezone' => config('app.timezone'),
                'Locale' => config('app.locale'),
            ]
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
                "map" => "laravel.version",
                "default" => ""
            ],
            "version:tooltip" => [
                "map" => "laravel.tooltip",
                "default" => "{}"
            ],
        ];
    }
}
