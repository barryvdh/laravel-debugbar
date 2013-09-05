<?php namespace Barryvdh\Debugbar;

use DebugBar\StandardDebugBar;
use DebugBar\Bridge\MonologCollector;
use DebugBar\DataCollector\PDO\PDOCollector;
use DebugBar\DataCollector\PDO\TraceablePDO;
use DebugBar\Bridge\SwiftMailer\SwiftLogCollector;
use DebugBar\Bridge\SwiftMailer\SwiftMailCollector;
use DebugBar\DataCollector\MessagesCollector;
use Barryvdh\Debugbar\DataCollector\ViewCollector;
use Barryvdh\Debugbar\DataCollector\RouteCollector;
use Barryvdh\Debugbar\DataCollector\LaravelCollector;

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
        $debugbar = $this->app['debugbar'];
        $this->addListener();
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{

        $this->package($this->package);

        $this->app['debugbar'] = $this->app->share(function ($app) {

                $debugbar = new StandardDebugBar;

                $events = $app['events'];

                $debugbar->addCollector(new LaravelCollector());

                if($this->app['config']->get('laravel-debugbar::config.log_events', false)){
                    $debugbar->addCollector(new MessagesCollector('events'));
                    $events->listen('*', function() use($debugbar){
                            $args = func_get_args();
                            $event = end($args);
                            $debugbar['events']->info("Received event: ". $event);
                        });
                }

                $debugbar->addCollector(new ViewCollector());
                $events->listen('composing:*', function($view) use($debugbar){
                        $debugbar['views']->addView($view);
                    });

                $debugbar->addCollector(new RouteCollector());

                if($log = $app['log']){
                    $debugbar->addCollector(new MonologCollector( $log->getMonolog() ));
                }

                if($db = $app['db']){
                    try{
                        $pdo = new TraceablePDO( $db->getPdo() );
                        $debugbar->addCollector(new PDOCollector( $pdo ));
                    }catch(\PDOException $e){
                        //Not connection set..
                    }
                }



                return $debugbar;
            });



        $this->app['debugbar.renderer'] = $this->app->share(function ($app) {
                
                /** @var \DebugBar\StandardDebugBar $debugbar */
                $debugbar = $app['debugbar'];
                $renderer = $debugbar->getJavascriptRenderer();
                $renderer->setBaseUrl(asset('packages/'.$this->package));
                $renderer->setIncludeVendors($this->app['config']->get('laravel-debugbar::config.include_vendors', true));

                return $renderer;
            });


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