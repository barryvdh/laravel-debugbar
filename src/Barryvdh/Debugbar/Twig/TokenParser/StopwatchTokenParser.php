<?php namespace Barryvdh\Debugbar\Twig\TokenParser;

use Barryvdh\Debugbar\Twig\Node\StopwatchNode;
/**
 * Token Parser for the stopwatch tag. Based on Symfony\Bridge\Twig\TokenParser\StopwatchTokenParser;
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class StopwatchTokenParser extends \Twig_TokenParser
{
    protected $debugbarAvailable;

    public function __construct($debugbarAvailable)
    {
        $this->debugbarAvailable = $debugbarAvailable;
    }

    public function parse(\Twig_Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        // {% stopwatch 'bar' %}
        $name = $this->parser->getExpressionParser()->parseExpression();

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        // {% endstopwatch %}
        $body = $this->parser->subparse(array($this, 'decideStopwatchEnd'), true);
        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        if ($this->debugbarAvailable) {
            return new StopwatchNode($name, $body, new \Twig_Node_Expression_AssignName($this->parser->getVarName(), $token->getLine()), $lineno, $this->getTag());
        }

        return $body;
    }

    public function decideStopwatchEnd(\Twig_Token $token)
    {
        return $token->test('endstopwatch');
    }

    public function getTag()
    {
        return 'stopwatch';
    }
}
