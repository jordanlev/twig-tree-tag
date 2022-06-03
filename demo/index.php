<?php

$loader = require __DIR__.'/../vendor/autoload.php';

$twig = new \Twig\Environment(
    new \Twig\Loader\FilesystemLoader(__DIR__)
);

$twig->addExtension(new JordanLev\TwigTreeTag\Twig\Extension\TreeExtension());

/*
 * we are building an array representing a directory tree.
 * - each key is a file or a directory name
 * - each value is another array of the same format if the key is a directory
 */

$dir = __DIR__.'/../vendor/twig/twig/lib/Twig';

function createTree($dir) {
    $glob = glob($dir.'/*');
    $nodes = array();
    foreach ($glob as $path) {
        if (is_dir($path)) {
            $nodes[basename($path)] = createTree($path);
        } else {
            $nodes[basename($path)] = null;
        }
    }

    return $nodes;
}

echo $twig->render('full_implementation.twig', array(
    'files' => createTree($dir)
));

