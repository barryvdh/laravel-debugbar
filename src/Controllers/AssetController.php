<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\Controllers;

use DebugBar\AssetHandler;
use DebugBar\SymfonyHttpDriver;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AssetController extends BaseController
{
    public function getAssets(Request $request)
    {
        $assetHandler = new AssetHandler($this->debugbar);

        $type = (string) $request->get('type');

        $response = new Response();
        $driver = $this->debugbar->getHttpDriver();
        if ($driver instanceof SymfonyHttpDriver) {
            $driver->setResponse($response);
        }

        $assetHandler->handle([
            'type' => $type
        ]);

        return $response;
    }
}
