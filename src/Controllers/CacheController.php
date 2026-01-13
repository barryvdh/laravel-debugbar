<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\Controllers;

use Illuminate\Http\Request;

class CacheController extends BaseController
{
    /**
     * Forget a cache key
     *
     */
    public function delete(Request $request, $key): \Illuminate\Http\JsonResponse
    {
        $cache = app('cache');

        if ($request->has('tags')) {
            $cache = $cache->tags($request->get('tags'));
        }

        $success = $cache->forget($key);

        return response()->json(compact('success'));
    }
}
