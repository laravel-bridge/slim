<?php

namespace LaravelBridge\Slim;

use Recca0120\LaravelBridge\Laravel;
use Slim\App as SlimApp;

class App extends SlimApp
{
    /**
     * @param Laravel $container
     * @param bool $useSlimService
     */
    public function __construct(Laravel $container, $useSlimService = true)
    {
        if ($useSlimService) {
            (new SlimDefaultServiceProvider($container))->register();
        } else {
            (new LaravelServiceProvider($container))->register();
        }

        parent::__construct($container);
    }
}
