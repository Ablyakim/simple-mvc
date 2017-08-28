<?php

namespace App;

use Framework\AppAbstract;

/**
 * Class App
 */
class App extends AppAbstract
{
    /**
     * @inheritdoc
     */
    public function getConfigFilePath()
    {
        return __DIR__.'/../../etc/config/base.php';
    }
}