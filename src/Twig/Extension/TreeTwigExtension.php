<?php

namespace Fuz\Jordan\Twig\Extension;

use Fuz\Jordan\TokenParser\TreeTokenParser;

class TreeTwigExtension extends \Twig_Extension
{
    public function getTokenParsers()
    {
        return [
            new TreeTokenParser(),
        ];
    }

    public function getName() {
        return 'jordan_tree';
    }
}
