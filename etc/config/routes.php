<?php

use App\Controller\ImageController;
use App\Controller\IndexController;
use App\Controller\SecurityController;
use App\Controller\TaskController;
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

$collection->add('logout', new Route('/logout', array(
    '_controller' => SecurityController::class,
    'action' => 'logout'
)));

$collection->add('task_create', new Route('/task/create', array(
    '_controller' => TaskController::class,
    'action' => 'create'
)));

$collection->add('task_edit', new Route('/task/edit/{id}', array(
    '_controller' => TaskController::class,
    'action' => 'edit'
)));

$collection->add('task_done', new Route('/task/done/{id}', array(
    '_controller' => TaskController::class,
    'action' => 'done',
    'method' => 'POST'
)));

$collection->add('task_preview', new Route('/task/preview', array(
    '_controller' => TaskController::class,
    'action' => 'preview',
    'method' => 'POST'
)));

$collection->add(
    'task_save',
    new Route(
        '/task/save',
        [
            '_controller' => TaskController::class,
            'action' => 'save',
            'method' => 'POST'
        ]
    )
);

$collection->add(
    'task_update',
    new Route(
        '/task/save/{id}',
        [
            '_controller' => TaskController::class,
            'action' => 'save',
            'method' => 'POST',
            'page' => 1
        ],
        ['page' => '\d+']
    )
);

return $collection;