<?php
/**
 * Base configuration
 */
return [
    //database configuration
    'db' => [
        'username' => 'root',
        'password' => '111111',
        'dbname' => 'task-manager',
        'host' => 'localhost',
        'port' => null
    ],

    //path to routes collection
    'routes' => __DIR__ . '/routes.php',

    //twig configuration
    'view' => [
        'template_dir' => __DIR__ . '/../../src/App/view',
        'cache_dir' => __DIR__ . '/../../var/cache/view',
    ],

    'error_controllers' => [
        'page_not_found' => [
            '_controller' => \App\Controller\PageNotFoundController::class,
            'action' => 'execute'
        ]
    ],

    //array of container compilers
    'container_compilers' => [
        \Framework\Core\Container\BaseContainerCompiler::class,
        \App\Container\ServicesCompiler::class
    ],

    //array of container compiler passes
    //applies after container was configured by "container_compilers"
    'compiler_passes' => [
        //add tagged service to event dispatcher
        \Framework\Core\Container\AddEventListenersCompilerPass::class
    ]
];