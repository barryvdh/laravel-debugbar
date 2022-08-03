<?php

namespace Barryvdh\Debugbar\Twig\Extension;

// Twig 2 compatibility
if (class_exists('\Twig_Extension')) {
    abstract class Extension extends \Twig_Extension {}
} else {
    abstract class Extension extends \Twig\Extension\AbstractExtension {}
}