<?php

namespace Barryvdh\Debugbar\DataFormatter;

use DebugBar\DataFormatter\DataFormatter;

class QueryFormatter extends DataFormatter
{

    /**
     * Removes extra spaces at the beginning and end of the SQL query and its lines.
     *
     * @param  string $sql
     * @return string
     */
    public function formatSql($sql)
    {
        return trim(preg_replace("/\s*\n\s*/", "\n", $sql));
    }

    /**
     * Check bindings for illegal (non UTF-8) strings, like Binary data.
     *
     * @param $bindings
     * @return mixed
     */
    public function checkBindings($bindings)
    {
        foreach ($bindings as &$binding) {
            if (is_string($binding) && !mb_check_encoding($binding, 'UTF-8')) {
                $binding = '[BINARY DATA]';
            }
        }

        return $bindings;
    }

    /**
     * Make the bindings safe for outputting.
     *
     * @param array $bindings
     * @return array
     */
    public function escapeBindings($bindings)
    {
        foreach ($bindings as &$binding) {
            $binding = htmlentities($binding, ENT_QUOTES, 'UTF-8', false);
        }

        return $bindings;
    }

    /**
     * Format a source object.
     *
     * @param  object|null  $source  If the backtrace is disabled, the $source will be null.
     * @return string
     */
    public function formatSource($source)
    {
        if (! is_object($source)) {
            return '';
        }

        $parts = [];

        if ($source->namespace) {
            $parts['namespace'] = $source->namespace . '::';
        }

        $parts['name'] = $source->name;
        $parts['line'] = ':' . $source->line;

        return implode($parts);
    }
}
