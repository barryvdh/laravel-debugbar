<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\Support;

use DebugBar\RequestIdGeneratorInterface;
use Illuminate\Support\Str;

class RequestIdGenerator implements RequestIdGeneratorInterface
{
    public function generate(): string
    {
        return (string) Str::ulid();
    }
}
