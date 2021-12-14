<?php

namespace Barryvdh\Debugbar\Controllers;

use Barryvdh\Debugbar\Support\Clockwork\Converter;
use DebugBar\OpenHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OpenHandlerController extends BaseController
{
    public function handle(Request $request)
    {
        $openHandler = new OpenHandler($this->debugbar);
        $data = $openHandler->handle($request->input(), false, false);

        return new Response(
            $data,
            200,
            [
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

        $openHandler = new OpenHandler($this->debugbar);
        $data = $openHandler->handle($request, false, false);

        // Convert to Clockwork
        $converter = new Converter();
        $output = $converter->convert(json_decode($data, true));

        return response()->json($output);
    }
}
