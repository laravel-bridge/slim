<?php

namespace LaravelBridge\Slim;

use Illuminate\Contracts\Container\Container;
use LaravelBridge\Support\ContainerBridge;
use Psr\Container\ContainerInterface;
use Slim\App as SlimApp;

class App extends SlimApp
{
    /**
     * @param Container $container
     * @param bool $useSlimService
     */
    public function __construct(Container $container, $useSlimService = true)
    {
        if (!$container instanceof ContainerInterface) {
            $container = new ContainerBridge($container);
        }

        if ($useSlimService) {
            (new SlimDefaultServiceProvider($container))->register();
        } else {
            (new LaravelServiceProvider($container))->register();
        }

        parent::__construct($container);
    }
}
