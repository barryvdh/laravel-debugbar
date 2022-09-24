<?php

namespace Barryvdh\Debugbar\Twig\Node;

/**
 * Represents a stopwatch node. Based on Symfony\Bridge\Twig\Node\StopwatchNode
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class StopwatchNode extends Node
{
    /**
     * @param \Twig_NodeInterface|\Twig\Node\Node $name
     * @param $body
     * @param \Twig_Node_Expression_AssignName|\Twig\Node\Expression\AssignNameExpression $var
     * @param $lineno
     * @param $tag
     */
    public function __construct(
        $name,
        $body,
        $var,
        $lineno = 0,
        $tag = null
    ) {
        parent::__construct(['body' => $body, 'name' => $name, 'var' => $var], [], $lineno, $tag);
    }

    /**
     * @param \Twig_Compiler|\Twig\Compiler $env
     * @return void
     */
    public function compile($compiler)
    {
        // Maintain compatibility with Twig 2 and 3.
        $extension = \Barryvdh\Debugbar\Twig\Extension\Stopwatch::class;
        if (class_exists('\Twig_Node')) {
            $extension = 'stopwatch';
        }

        $compiler
            ->addDebugInfo($this)
            ->write('')
            ->subcompile($this->getNode('var'))
            ->raw(' = ')
            ->subcompile($this->getNode('name'))
            ->write(";\n")
            ->write(sprintf("\$this->env->getExtension('%s')->getDebugbar()->startMeasure(", $extension))
            ->subcompile($this->getNode('var'))
            ->raw(");\n")
            ->subcompile($this->getNode('body'))
            ->write(sprintf("\$this->env->getExtension('%s')->getDebugbar()->stopMeasure(", $extension))
            ->subcompile($this->getNode('var'))
            ->raw(");\n");
    }
}
