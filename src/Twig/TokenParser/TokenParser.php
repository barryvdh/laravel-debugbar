<?php

namespace Barryvdh\Debugbar\Twig\TokenParser;

// Twig 2 compatibility
if (class_exists('\Twig_TokenParser')) {
    abstract class TokenParser extends \Twig_TokenParser {}
} else {
    abstract class TokenParser extends \Twig\TokenParser\AbstractTokenParser {}
}
