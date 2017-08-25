<?php

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use App\Controller\IndexController;

$collection = new RouteCollection();

$collection->add('home', new Route('/', array(
    '_controller' => IndexController::class,
    'action' => 'index'
)));


return $collection;