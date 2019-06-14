<?php

namespace MilesChou\LaravelBridger\Slim;

use Recca0120\LaravelBridge\Laravel;
use Slim\App as SlimApp;

class App extends SlimApp
{
    /**
     * @param Laravel $container
     */
    public function __construct(Laravel $container)
    {
        (new SlimDefaultServiceProvider($container))->register();

        parent::__construct($container);
    }
}
