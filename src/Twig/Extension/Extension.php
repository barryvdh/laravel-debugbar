<?php

namespace Barryvdh\Debugbar\Twig\Extension;

// Maintain compatibility with Twig 2 and 3.
if (class_exists('\Twig_Extension')) {
    abstract class Extension extends \Twig_Extension
    {
    }
} else {
    abstract class Extension extends \Twig\Extension\AbstractExtension
    {
    }
}
