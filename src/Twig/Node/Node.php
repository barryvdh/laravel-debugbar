<?php

namespace Barryvdh\Debugbar\Twig\Node;

// Twig 2 compatibility
if (class_exists('\Twig_Node')) {
    abstract class Node extends \Twig_Node {}
} else {
    abstract class Node extends \Twig\Node\Node {}
}