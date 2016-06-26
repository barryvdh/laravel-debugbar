<?php namespace Barryvdh\Debugbar\Controllers;

use Barryvdh\Debugbar\Support\Clockwork\Converter;
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
            $data, 200, [
                'Content-Type' => 'application/json'
            ]
        );
    }

    /**
     * Return Clockwork output
     *
     * @param $id
     * @return mixed
     * @throws \DebugBar\DebugBarException
     */
    public function clockwork($id)
    {
        $request = [
            'op' => 'get',
            'id' => $id,
        ];

        $debugbar = $this->debugbar;

        if (!$debugbar->isEnabled()) {
            $this->app->abort('500', 'Debugbar is not enabled');
        }

        $openHandler = new OpenHandler($debugbar);

        $data = $openHandler->handle($request, false, false);

        // Convert to Clockwork
        $converter = new Converter();
        $output = $converter->convert(json_decode($data, true));

        return response()->json($output);
    }
}
