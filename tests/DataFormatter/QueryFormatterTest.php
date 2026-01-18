<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\Tests\DataFormatter;

use DebugBar\DataFormatter\QueryFormatter;
use Fruitcake\LaravelDebugbar\Tests\TestCase;

class QueryFormatterTest extends TestCase
{
    public function testItFormatsArrayBindings()
    {

        $bindings = [
            'some string',
            [
                'string',
                "Another ' string",
                [
                    'nested',
                    'array',
                ],
            ],
        ];

        $queryFormatter = new QueryFormatter();

        $output = $queryFormatter->checkBindings($bindings);

        static::assertSame($output, ["some string", "[string,Another ' string,[nested,array]]"]);
    }

    public function testItFormatsObjectBindings()
    {
        $object = new \StdClass();
        $object->attribute1 = 'test';

        $bindings = [
            'some string',
            $object,
        ];

        $queryFormatter = new QueryFormatter();

        $output = $queryFormatter->checkBindings($bindings);

        static::assertSame($output, ['some string', '{"attribute1":"test"}']);
    }
}
