<?php

namespace Barryvdh\Debugbar\Controllers;

use Barryvdh\Debugbar\Support\Explain;
use Exception;
use Illuminate\Http\Request;

class QueriesController extends BaseController
{
    /**
     * Generate explain data for query.
     */
    public function explain(Request $request)
    {
        if (!config('debugbar.options.db.explain.enabled', false)) {
            return response()->json([
                'success' => false,
                'message' => 'EXPLAIN is currently disabled in the Debugbar.',
            ], 400);
        }

        try {
            $data = match ($request->json('mode')) {
                'visual' => (new Explain())->generateVisualExplain($request->json('data')),
                default => (new Explain())->generateRawExplain($request->json('data')),
            };

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
