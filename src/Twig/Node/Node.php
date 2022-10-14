<?php

namespace Barryvdh\Debugbar\Twig\Node;

// Maintain compatibility with Twig 2 and 3.
if (class_exists('\Twig_Node')) {
    abstract class Node extends \Twig_Node
    {
    }
} else {
    abstract class Node extends \Twig\Node\Node
    {
    }
}
