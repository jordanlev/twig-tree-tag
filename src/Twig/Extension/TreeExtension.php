<?php

namespace QEEP\TwigTreeTag\Twig\Extension;

use QEEP\TwigTreeTag\Twig\TokenParser\TreeTokenParser;

class TreeExtension extends \Twig\Extension\AbstractExtension
{
    public function __construct()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            throw new \LogicException('The {%tree%} Twig tag requires PHP version 5.4 or higher');
        }
    }

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
