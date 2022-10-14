<?php

namespace Barryvdh\Debugbar\Twig\TokenParser;

// Maintain compatibility with Twig 2 and 3.
if (class_exists('\Twig_TokenParser')) {
    abstract class TokenParser extends \Twig_TokenParser
    {
    }
} else {
    abstract class TokenParser extends \Twig\TokenParser\AbstractTokenParser
    {
    }
}
