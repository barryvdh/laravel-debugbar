<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\Controllers;

class CacheController extends BaseController
{
    /**
     * Forget a cache key
     *
     */
    public function delete($key, $tags = '')
    {
        $cache = app('cache');

        if (!empty($tags)) {
            $tags = json_decode($tags, true);
            $cache = $cache->tags($tags);
        } else {
            unset($tags);
        }

        $success = $cache->forget($key);

        return response()->json(compact('success'));
    }
}
