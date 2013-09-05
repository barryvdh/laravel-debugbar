<?php namespace Barryvdh\Debugbar;


class ServiceProvider extends \Illuminate\Support\ServiceProvider {

    protected $package = 'barryvdh/laravel-debugbar';
	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{

	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{

        $this->package($this->package);

        $this->app['debugbar'] = $this->app->share(function () {
                return new \DebugBar\StandardDebugBar;
            });

        $this->app['debugbar.renderer'] = $this->app->share(function ($app) {
                
                /** @var \DebugBar\StandardDebugBar $debugbar */
                $debugbar = $app['debugbar'];
                $renderer = $debugbar->getJavascriptRenderer();
                $renderer->setBaseUrl(asset('packages/'.$this->package));
                $renderer->setIncludeVendors($this->app['config']->get('laravel-debugbar::config.include_vendors', true));

                return $renderer;
            });

        $this->addListener();
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('debugbar', 'debugbar.renderer');
	}

    protected function addListener(){

        // Check console isn't running and profiler is enabled
        $enabled = (!$this->app->runningInConsole() and !$this->app['request']->ajax()) ? $this->app['config']->get('laravel-debugbar::config.enabled') : false;

        if ($enabled)
        {
            $app = $this->app;
            $this->app['router']->after(function ($request, $response) use($app)
                {
                    // Do not display profiler on non-HTML responses.
                    if (\Str::startsWith($response->headers->get('Content-Type'), 'text/html'))
                    {
                        $content = $response->getContent();

                        $renderer = $app['debugbar.renderer'];
                        $output = $renderer->renderHead() . $renderer->render();

                        $body_position = strripos($content, '</body>');

                        if ($body_position !== FALSE)
                        {
                            $content = substr($content, 0, $body_position) . $output . substr($content, $body_position);
                        }
                        else
                        {
                            $content .= $output;
                        }

                        $response->setContent($content);
                    }
                });
        }
    }
}