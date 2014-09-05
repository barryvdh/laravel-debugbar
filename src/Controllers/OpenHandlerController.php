<?php namespace Barryvdh\Debugbar\Controllers;

use DebugBar\OpenHandler;
use Illuminate\Http\Response;

class OpenHandlerController extends BaseController
{

    public function handle()
    {
        // Reflash session data
        $this->app['session']->reflash();

        $debugbar = $this->app['debugbar'];

        if (!$debugbar->isEnabled()) {
            $this->app->abort('500', 'Debugbar is not enabled');
        }

        $openHandler = new OpenHandler($debugbar);

        $data = $openHandler->handle(null, false, false);

        return new Response(
            $data, 200, array(
                'Content-Type' => 'application/json'
            )
        );
    }
}
