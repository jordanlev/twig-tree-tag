<?php

$loader = require __DIR__.'/../vendor/autoload.php';

$twig = new \Twig_Environment(
    new \Twig_Loader_Filesystem(__DIR__)
);

$twig->addExtension(new Fuz\Jordan\Twig\Extension\TreeExtension());

/*
 * we are building an array representing a directory tree.
 * - each value is an object containing:
 * --- name: the file or directory name
 * --- children: files of the directory (assuming it is a directory!)
 */

$dir = __DIR__.'/../vendor/twig/twig/lib/Twig';

function createTree($dir) {
    $glob = glob($dir.'/*');
    $nodes = array();
    foreach ($glob as $path) {
        $object           = new \stdClass();
        $object->name     = basename($path);
        $object->children = null;

        if (is_dir($path)) {
            $object->children = createTree($path);
        }

        $nodes[] = $object;
    }

    return $nodes;
}

echo $twig->render('jordan_implementation.twig', array(
    'items' => createTree($dir)
));

