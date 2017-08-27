<?php

use App\Controller\IndexController;
use App\Controller\TaskController;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$collection = new RouteCollection();

$collection->add('home', new Route('/', array(
    '_controller' => IndexController::class,
    'action' => 'index'
)));

$collection->add('task_all', new Route('/tasks', array(
    '_controller' => TaskController::class,
    'action' => 'loadTasks'
)));

return $collection;