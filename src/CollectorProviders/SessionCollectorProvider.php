<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\CollectorProviders;

use Barryvdh\Debugbar\DataCollector\SessionCollector;
use Illuminate\Http\Request;

class SessionCollectorProvider extends AbstractCollectorProvider
{
    public function __invoke(Request $request, array $options): void
    {
        if ($request->hasSession()) {

            // Legacy hidden values, using array path
            $hiddens = array_map(function ($value) {
                if (str_contains($value, '.')) {
                    return substr($value, strrpos($value, '.') + 1);
                }
                return $value;
            }, $options['hiddens'] ?? []);

            $sessionCollector = new SessionCollector($request->getSession());
            $sessionCollector->addMaskedKeys($hiddens);
            $sessionCollector->addMaskedKeys($options['masked'] ?? []);
            $this->addCollector($sessionCollector);
        }
    }
}
