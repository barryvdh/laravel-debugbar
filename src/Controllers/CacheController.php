<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\Controllers;

class CacheController extends BaseController
{
    /**
     * Forget a cache key
     *
     */
    public function delete($key, ?string $tags): \Illuminate\Http\JsonResponse
    {
        $cache = app('cache');

        if ($tags) {
            $tags = json_decode($tags, true);
            $cache = $cache->tags($tags);
        } else {
            unset($tags);
        }

        $success = $cache->forget($key);

        return response()->json(compact('success'));
    }
}
