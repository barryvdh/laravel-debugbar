<?php

namespace Barryvdh\Debugbar\Controllers;

use Illuminate\Http\Response;

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
