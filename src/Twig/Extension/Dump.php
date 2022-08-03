<?php

namespace Barryvdh\Debugbar\Twig\Extension;

use DebugBar\DataFormatter\DataFormatterInterface;

/**
 * Dump variables using the DataFormatter
 */
class Dump extends Extension
{
    /**
     * @var \DebugBar\DataFormatter\DataFormatter
     */
    protected $formatter;

    /**
     * Create a new auth extension.
     *
     * @param \DebugBar\DataFormatter\DataFormatterInterface $formatter
     */
    public function __construct(DataFormatterInterface $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'Laravel_Debugbar_Dump';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        // Maintain compatibility with Twig 2 and 3.
        $simpleFunction = '\Twig_SimpleFunction';

        if (!class_exists($simpleFunction)) {
            $simpleFunction = '\Twig\TwigFunction';
        }

        return [
            new $simpleFunction(
                'dump',
                [$this, 'dump'],
                ['is_safe' => ['html'], 'needs_context' => true, 'needs_environment' => true]
            ),
        ];
    }

    /**
     * Based on Twig_Extension_Debug / twig_var_dump
     * (c) 2011 Fabien Potencier
     *
     * @param \Twig_Environment|\Twig\Environment $env
     * @param $context
     *
     * @return string
     */
    public function dump($env, $context)
    {
        $output = '';

        $count = func_num_args();
        if (2 === $count) {
            $data = [];
            foreach ($context as $key => $value) {
                if (is_object($value)) {
                    if (method_exists($value, 'toArray')) {
                        $data[$key] = $value->toArray();
                    } else {
                        $data[$key] = "Object (" . get_class($value) . ")";
                    }
                } else {
                    $data[$key] = $value;
                }
            }
            $output .= $this->formatter->formatVar($data);
        } else {
            for ($i = 2; $i < $count; $i++) {
                $output .= $this->formatter->formatVar(func_get_arg($i));
            }
        }

        return '<pre>' . $output . '</pre>';
    }
}
