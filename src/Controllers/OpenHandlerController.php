<?php namespace Barryvdh\Debugbar\Controllers;

use DebugBar\OpenHandler;
use Illuminate\Foundation\Application;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class OpenHandlerController extends Controller {

    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function handle()
    {
        // Reflash session data
        $this->app['session']->reflash();

        $debugbar = $this->app['debugbar'];

        if(!$debugbar->isEnabled()){
            $this->app->abort('500', 'Debugbar is not enabled');
        }

        $openHandler = new OpenHandler($debugbar);

        $data = $openHandler->handle(null, false, false);

        return new Response($data, 200, array(
                'Content-Type' => 'application/json'
            ));
    }

}
