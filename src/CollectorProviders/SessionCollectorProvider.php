<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\CollectorProviders;

use Fruitcake\LaravelDebugbar\DataCollector\SessionCollector;
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
            }, (array) ($options['hiddens'] ?? []));

            $sessionCollector = new SessionCollector($request->getSession());
            $sessionCollector->addMaskedKeys($hiddens);
            $sessionCollector->addMaskedKeys((array) ($options['masked'] ?? []));
            $this->addCollector($sessionCollector);
        }
    }
}
