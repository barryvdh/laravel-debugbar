<?php

namespace Barryvdh\Debugbar\Controllers;

use Barryvdh\Debugbar\Support\VisualExplain;
use Exception;
use Illuminate\Http\Request;

class QueriesController extends BaseController
{
    /**
     * Generate visual explain url for query.
     */
    public function visual(Request $request)
    {
        if (!config('debugbar.options.db.explain.enabled', false)) {
            return response()->json([
                'success' => false,
                'message' => 'EXPLAIN is currently disabled in the Debugbar.',
            ], 400);
        }

        try {
            return response()->json([
                'success' => true,
                'url' => (new VisualExplain())->generateUrl($request->json('data')),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
