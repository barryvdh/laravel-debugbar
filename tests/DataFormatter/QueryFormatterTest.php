<?php

namespace Barryvdh\Debugbar\Tests\DataFormatter;

use Barryvdh\Debugbar\DataFormatter\QueryFormatter;
use Barryvdh\Debugbar\Tests\TestCase;

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
                    'array'
                ]
            ],
        ];

        $queryFormatter = new QueryFormatter();

        $output = $queryFormatter->checkBindings($bindings);

        $this->assertSame($output, ["some string", "[string,Another ' string,[nested,array]]"]);
    }

    public function testItFormatsObjectBindings()
    {
        $object = new \StdClass();
        $object->attribute1 = 'test';

        $bindings = [
            'some string',
            $object
        ];

        $queryFormatter = new QueryFormatter();

        $output = $queryFormatter->checkBindings($bindings);

        $this->assertSame($output, ['some string', '{"attribute1":"test"}']);
    }
}
