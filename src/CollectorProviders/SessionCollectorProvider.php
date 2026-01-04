<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\CollectorProviders;

use Illuminate\Http\Request;

class SessionCollectorProvider extends AbstractCollectorProvider
{
    public function __invoke(Request $request, array $options): void {}
}
