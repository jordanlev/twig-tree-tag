<?php

namespace Fuz\Jordan\Twig\Extension;

use Fuz\Jordan\Twig\TokenParser\TreeTokenParser;

class TreeExtension extends \Twig_Extension
{
    public function getTokenParsers()
    {
        return array(
            new TreeTokenParser(),
        );
    }

    public function getName() {
        return 'tree';
    }
}
