<?php

namespace Barryvdh\Debugbar\DataFormatter;

use DebugBar\DataFormatter\DataFormatter;

/**
 * Simple DataFormatter based on the deprecated Symfony ValueExporter
 *
 * @see https://github.com/symfony/symfony/blob/v3.4.4/src/Symfony/Component/HttpKernel/DataCollector/Util/ValueExporter.php
 */
#[\AllowDynamicProperties]
class SimpleFormatter extends DataFormatter
{
    /**
     * @param $data
     * @return string
     */
    public function formatVar($data)
    {
        return $this->exportValue($data);
    }

    /**
     * Converts a PHP value to a string.
     *
     * @param mixed $value The PHP value
     * @param int   $depth Only for internal usage
     * @param bool  $deep  Only for internal usage
     *
     * @return string The string representation of the given value
     *  @author Bernhard Schussek <bschussek@gmail.com>
     */
    private function exportValue($value, $depth = 1, $deep = false)
    {
        if ($value instanceof \__PHP_Incomplete_Class) {
            return sprintf('__PHP_Incomplete_Class(%s)', $this->getClassNameFromIncomplete($value));
        }

        if (is_object($value)) {
            if ($value instanceof \DateTimeInterface) {
                return sprintf('Object(%s) - %s', get_class($value), $value->format(\DateTime::ATOM));
            }

            return sprintf('Object(%s)', get_class($value));
        }

        if (is_array($value)) {
            if (empty($value)) {
                return '[]';
            }

            $indent = str_repeat('  ', $depth);

            $a = [];
            foreach ($value as $k => $v) {
                if (is_array($v)) {
                    $deep = true;
                }
                $a[] = sprintf('%s => %s', $k, $this->exportValue($v, $depth + 1, $deep));
            }

            if ($deep) {
                $args = [$indent, implode(sprintf(", \n%s", $indent), $a), str_repeat('  ', $depth - 1)];
                return sprintf("[\n%s%s\n%s]", ...$args);
            }

            $s = sprintf('[%s]', implode(', ', $a));

            if (80 > strlen($s)) {
                return $s;
            }

            return sprintf("[\n%s%s\n]", $indent, implode(sprintf(",\n%s", $indent), $a));
        }

        if (is_resource($value)) {
            return sprintf('Resource(%s#%d)', get_resource_type($value), $value);
        }

        if (null === $value) {
            return 'null';
        }

        if (false === $value) {
            return 'false';
        }

        if (true === $value) {
            return 'true';
        }

        return (string) $value;
    }

    /**
     * @param \__PHP_Incomplete_Class $value
     * @return mixed
     * @author Bernhard Schussek <bschussek@gmail.com>
     */
    private function getClassNameFromIncomplete(\__PHP_Incomplete_Class $value)
    {
        $array = new \ArrayObject($value);

        return $array['__PHP_Incomplete_Class_Name'];
    }
}
