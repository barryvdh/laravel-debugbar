<?php namespace Barryvdh\Debugbar\Controllers;

use DebugBar\OpenHandler;
use Illuminate\Http\Response;

class OpenHandlerController extends BaseController
{
   
    public function handle()
    {
        $debugbar = $this->debugbar;

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
