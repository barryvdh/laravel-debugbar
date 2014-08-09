<?php namespace Barryvdh\Debugbar\Twig\Node;

/**
 * Represents a stopwatch node. Based on Symfony\Bridge\Twig\Node\StopwatchNode
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class StopwatchNode extends \Twig_Node
{
    public function __construct(\Twig_NodeInterface $name, $body, \Twig_Node_Expression_AssignName $var, $lineno = 0, $tag = null)
    {
        parent::__construct(array('body' => $body, 'name' => $name, 'var' => $var), array(), $lineno, $tag);
    }

    public function compile(\Twig_Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write('')
            ->subcompile($this->getNode('var'))
            ->raw(' = ')
            ->subcompile($this->getNode('name'))
            ->write(";\n")
            ->write("\$this->env->getExtension('stopwatch')->getDebugbar()->startMeasure(")
            ->subcompile($this->getNode('var'))
            ->raw(");\n")
            ->subcompile($this->getNode('body'))
            ->write("\$this->env->getExtension('stopwatch')->getDebugbar()->stopMeasure(")
            ->subcompile($this->getNode('var'))
            ->raw(");\n")
        ;
    }
}
