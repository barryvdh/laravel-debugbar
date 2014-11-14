<?php namespace Barryvdh\Debugbar\Twig\Extension;

use DebugBar\DataFormatter\DataFormatterInterface;
use Twig_Environment;
use Twig_Extension;
use Twig_SimpleFunction;

/**
 * Dump variables using the DataFormatter
 */
class Dump extends Twig_Extension
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
        return array(
            new Twig_SimpleFunction(
                'dump', [$this, 'dump'], array('is_safe' => ['html'], 'needs_context' => true, 'needs_environment' => true)
            ),
        );
    }

    /**
     * Based on Twig_Extension_Debug / twig_var_dump
     * (c) 2011 Fabien Potencier
     *
     * @param Twig_Environment $env
     * @param $context
     *
     * @return string
     */
    public function dump(Twig_Environment $env, $context)
    {
        $output = '';

        $count = func_num_args();
        if (2 === $count) {
            $data = array();
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

        return '<pre>'.$output.'</pre>';
    }
}
