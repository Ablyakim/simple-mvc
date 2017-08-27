<?php

use App\Controller\IndexController;
use App\Controller\SecurityController;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$collection = new RouteCollection();

$collection->add('home', new Route('/', array(
    '_controller' => IndexController::class,
    'action' => 'index'
)));

$collection->add('post-login', new Route('/post-login', array(
    '_controller' => SecurityController::class,
    'action' => 'postLogin',
    'method' => 'POST'
)));

$collection->add('login', new Route('/login', array(
    '_controller' => SecurityController::class,
    'action' => 'login'
)));

return $collection;