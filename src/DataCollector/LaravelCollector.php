<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Support\Str;

class LaravelCollector extends DataCollector implements Renderable
{
    /**
     * {@inheritDoc}
     */
    public function collect(): array
    {
        $app = app();
        return [
            "version" => Str::of($app->version())->explode('.')->first() . '.x',
            'tooltip' => [
                'Laravel Version' => $app->version(),
                'PHP Version' => phpversion(),
                'Environment' => $app->environment(),
                'Debug Mode' => config('app.debug') ? 'Enabled' : 'Disabled',
                'URL' => Str::of(config('app.url'))->replace(['http://', 'https://'], ''),
                'Timezone' => config('app.timezone'),
                'Locale' => config('app.locale'),
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'laravel';
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets(): array
    {
        return [
            "version" => [
                "icon" => "brand-laravel",
                "map" => "laravel.version",
                "default" => "",
            ],
            "version:tooltip" => [
                "map" => "laravel.tooltip",
                "default" => "{}",
            ],
        ];
    }
}
