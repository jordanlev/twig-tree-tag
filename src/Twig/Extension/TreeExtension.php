<?php

namespace JordanLev\TwigTreeTag\Twig\Extension;

use JordanLev\TwigTreeTag\Twig\TokenParser\TreeTokenParser;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Extension\ExtensionInterface;

class TreeExtension extends AbstractExtension implements ExtensionInterface
{
    public function __construct()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            throw new \LogicException('The {%tree%} Twig tag requires PHP version 5.4 or higher');
        }
    }

    public function getTokenParsers(): array
    {
        return array(
            new TreeTokenParser(),
        );
    }

//    public function getName() {
//        return 'tree';
//    }
    public function getOperators()
    {
        return [];
    }
}
