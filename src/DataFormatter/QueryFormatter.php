<?php

namespace Barryvdh\Debugbar\DataFormatter;

use DebugBar\DataFormatter\DataFormatter;

class QueryFormatter extends DataFormatter
{

    /**
     * Removes extra spaces at the beginning and end of the SQL query and its lines.
     *
     * @param string $sql
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
     * Format query bindings into an ordered list.
     *
     * @param  array  $bindings
     * @return string
     */
    public function formatBindings(array $bindings)
    {
        $bindings = array_map(function ($binding, $index) {
            return '<span class="phpdebugbar-text-muted">' . $index . '.</span> ' . $binding;
        }, $bindings, array_keys($bindings));

        return $this->formatList($bindings);
    }

    /**
     * Format the hints into an list.
     *
     * @param  array  $hints
     * @return string
     */
    public function formatHints(array $hints)
    {
        return $this->formatList($hints);
    }

    /**
     * Format the backtrace sources into an ordered list.
     *
     * @param  array  $sources
     * @return string
     */
    public function formatSources(array $sources)
    {
        $items = [];

        foreach ($sources as $source) {
            $parts = [
                'index' => '<span class="phpdebugbar-text-muted">' . $source->index . '.</span>&nbsp;',
            ];

            if ($source->namespace) {
                $parts['namespace'] = $source->namespace . '::';
            }

            $parts['name'] = $source->name;
            $parts['line'] = '<span class="phpdebugbar-text-muted">:' . $source->line . '</span>';

            $items[] = implode($parts);
        }

        return $this->formatList($items);
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

    /**
     * Generate an array with metadata about the query.
     *
     * @param  array  $query
     * @return object
     */
    public function formatMetadata(array $query)
    {
        $metadata = (object) [];

        if ($query['bindings']) {
            $metadata = $this->addMetadata(
                $metadata, 'bindings', $this->formatBindings($query['bindings'])
            );
        }

        if ($query['hints']) {
            $metadata = $this->addMetadata(
                $metadata, 'hints', $this->formatHints($query['hints'])
            );
        }

        if ($query['source']) {
            $metadata = $this->addMetadata(
                $metadata, 'backtrace', $this->formatSources($query['source'])
            );
        }

        return $metadata;
    }

    /**
     * Append an item to the metadata.
     *
     * @param  object $metadata
     * @param  string $name
     * @param  string $parameter
     * @return object
     */
    public function addMetadata($metadata, $name, $parameter)
    {
        $icons = [
            'bindings' => 'thumb-tack',
            'hints' => 'question-circle',
            'backtrace' => 'list-ul',
        ];

        $icon = isset($icons[$name]) ? $icons[$name] : 'circle';

        $key = ucfirst($name) . ' <i class="phpdebugbar-fa phpdebugbar-fa-' . $icon . ' phpdebugbar-text-muted"></i>';

        $metadata->$key = $parameter;

        return $metadata;
    }

    /**
     * Format an array into a list.
     *
     * @param  array  $items
     * @return string
     */
    protected function formatList(array $items)
    {
        $list = [];

        foreach ($items as $item) {
            $list[] = '<li class="phpdebugbar-widgets-table-list-item">' . $item . '</li>';
        }

        return '<ul class="phpdebugbar-widgets-table-list">' . implode($list) . '</ul>';
    }
}
