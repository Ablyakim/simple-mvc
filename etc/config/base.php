<?php
/**
 * Base configuration
 */
return [
    'db' => [
        'username' => '',
        'password' => ''
    ],

    'routes' => __DIR__ . '/routes.php',
    'view' => [
        'template_dir' => __DIR__ . '/../../src/App/view',
        'cache_dir' => __DIR__ . '/../../var/cache/view',
    ],

    //array of container configuration files
    'container_compilers' => [
        \Framework\Core\Container\BaseContainerCompiler::class,
    ],
    'compiler_passes' => [
        //add tagged service to event dispatcher
        \Framework\Core\Container\AddEventListenersCompilerPass::class
    ]
];