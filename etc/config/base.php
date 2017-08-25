<?php
/**
 * Base config structure and values
 */
return [
    'db' => [
        'username' => '',
        'password' => ''
    ],
    'routes' => __DIR__ . '/routes.php',
    //array of container configuration files
    'container_compilers' => [
        \Framework\Core\Container\BaseContainerCompiler::class,
    ],
    'compiler_passes' => [
        //add tagged service to event dispatcher
        \Framework\Core\Container\AddEventListenersCompilerPass::class
    ]
];