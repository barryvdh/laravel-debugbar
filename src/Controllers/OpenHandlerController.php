<?php

namespace Barryvdh\Debugbar\Controllers;

use Barryvdh\Debugbar\Support\Clockwork\Converter;
use DebugBar\DebugBarException;
use DebugBar\OpenHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OpenHandlerController extends BaseController
{
    /**
     * Check if the storage is open for inspecting.
     *
     * @param Request $request
     * @return bool
     */
    protected function isStorageOpen(Request $request)
    {
        $open = config('debugbar.storage.open');

        if (is_callable($open)) {
            return call_user_func($open, [$request]);
        }

        return $open;
    }

    public function handle(Request $request)
    {
        if ($request->input('op') === 'get' || $this->isStorageOpen($request)) {
            $openHandler = new OpenHandler($this->debugbar);
            $data = $openHandler->handle($request->input(), false, false);
        } else {
            $data = [
                [
                    'datetime' => date("Y-m-d H:i:s"),
                    'id' => null,
                    'ip' => $request->getClientIp(),
                    'method' => 'ERROR',
                    'uri' => '!! To enable public access to previous requests, set debugbar.storage.open to true in your config, or enable DEBUGBAR_OPEN_STORAGE if you did not publish the config. !!',
                    'utime' => microtime(true),
                ]
            ];
        }

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
    public function clockwork(Request $request, $id)
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
