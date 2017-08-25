<?php

require __DIR__ . '/../vendor/autoload.php';

$autoloadNamespaces = [
    'App' => __DIR__ . '/../src',
    'Framework' => __DIR__ . '/../src',
];

spl_autoload_register(function ($class) use ($autoloadNamespaces) {
    foreach ($autoloadNamespaces AS $namespace => $dirs) {
        if (strpos($class, $namespace) === 0) {
            $file = str_replace("\\", DIRECTORY_SEPARATOR, $class) . ".php";

            foreach ((array)$dirs AS $dir) {
                if (is_file($dir . DIRECTORY_SEPARATOR . $file)) {
                    require $dir . DIRECTORY_SEPARATOR . $file;
                    return true;
                }
            }
        }
    }
});