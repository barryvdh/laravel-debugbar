<?php

namespace Barryvdh\Debugbar\Support;

use DebugBar\RequestIdGeneratorInterface;
use Illuminate\Support\Str;

class RequestIdGenerator implements RequestIdGeneratorInterface
{
    public function generate(): string
    {
        return (string) Str::ulid();
    }
}
