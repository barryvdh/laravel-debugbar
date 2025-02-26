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
            $explain = new Explain();

            if ($request->json('mode') === 'visual') {
                return response()->json([
                    'success' => true,
                    'data' => $explain->generateVisualExplain($request->json('connection'), $request->json('query'), $request->json('bindings'), $request->json('hash')),
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $explain->generateRawExplain($request->json('connection'), $request->json('query'), $request->json('bindings'), $request->json('hash')),
                'visual' => $explain->isVisualExplainSupported($request->json('connection')) ? [
                    'confirm' => $explain->confirmVisualExplain($request->json('connection')),
                ] : null,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
