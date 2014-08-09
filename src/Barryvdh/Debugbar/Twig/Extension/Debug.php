<?php namespace Barryvdh\Debugbar\Twig\Extension;

use Twig_Extension;
use Twig_Environment;
use Twig_SimpleFunction;
use Illuminate\Foundation\Application;

/**
 * Access Laravels auth class in your Twig templates.
 */
class Debug extends Twig_Extension
{
    /**
     * @var \Barryvdh\Debugbar\LaravelDebugbar
     */
    protected $debugbar;

    /**
     * Create a new auth extension.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    public function __construct(Application $app)
    {
         if($app->bound('debugbar')){
            $this->debugbar = $app['debugbar'];
        }else{
            $this->debugbar = null;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'Laravel_Debugbar_Debug';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction('dump', [$this, 'dump'], array('needs_context' => true, 'needs_environment' => true)),
        );
    }

    /**
     * Based on Twig_Extension_Debug / twig_var_dump
     * (c) 2011 Fabien Potencier
     *
     * @param Twig_Environment $env
     * @param $context
     */
    public function dump(Twig_Environment $env, $context)
    {
        if (!$env->isDebug() || !$this->debugbar) {
            return;
        }

        $count = func_num_args();
        if (2 === $count) {
            $data = array();
            foreach ($context as $key => $value) {
                if (is_object($value)) {
                    if (method_exists($value, 'toArray')) {
                        $data[$key] = $value->toArray();
                    } else {
                        $data[$key] = "Object (". get_class($value).")";
                    }
                } else {
                    $data[$key] = $value;
                }
            }
            $this->debugbar->addMessage($data);
        } else {
            for ($i = 2; $i < $count; $i++) {
                $this->debugbar->addMessage(func_get_arg($i));
            }
        }

        return;
    }
}
