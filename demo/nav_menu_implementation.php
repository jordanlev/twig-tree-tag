<?php

$loader = require __DIR__.'/../vendor/autoload.php';

$twig = new \Twig_Environment(
    new \Twig_Loader_Filesystem(__DIR__)
);

$twig->addExtension(new Fuz\Jordan\Twig\Extension\TreeExtension());

class MenuItem {
    public $name;
    public $url;
    public $children = array();
    
    public function __construct($name, $url) {
        $this->name = $name;
        $this->url = $url;
    }
}

$menu = array();
$menu[1] = new MenuItem('Home', '/');
$menu[2] = new MenuItem('Products', '/products');
$menu[2]->children[1] = new MenuItem('First Product', '/products/1');
$menu[2]->children[1]->children[1] = new MenuItem('Option A', '/products/1/a');
$menu[2]->children[1]->children[2] = new MenuItem('Option B', '/products/1/b');
$menu[2]->children[2] = new MenuItem('Second Product', '/products/2');
$menu[2]->children[2]->children[1] = new MenuItem('Option C', '/products/2/c');
$menu[2]->children[2]->children[2] = new MenuItem('Option D', '/products/2/d');
$menu[2]->children[2]->children[3] = new MenuItem('Option E', '/products/2/e');
$menu[2]->children[3] = new MenuItem('Third Product', '/products/3');
$menu[3] = new MenuItem('Locations', '/locations');
$menu[3]->children[1] = new MenuItem('Main Location', '/locations/main');
$menu[3]->children[2] = new MenuItem('Downtown', '/locations/downtown');
$menu[3]->children[3] = new MenuItem('Airport', '/locations/airport');
$menu[4] = new MenuItem('About Us', '/about');

echo $twig->render('nav_menu_implementation.twig', array('menu' => $menu));
