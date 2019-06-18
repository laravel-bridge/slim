<?php

namespace LaravelBridge\Slim;

use Illuminate\Container\Container as IlluminateContainer;
use Illuminate\Contracts\Container\Container as ContainerContracts;
use Psr\Container\ContainerInterface;
use Slim\App as SlimApp;

class App extends SlimApp
{
    /**
     * @param ContainerContracts|array $container
     * @param bool $useSlimService
     */
    public function __construct($container = [], $useSlimService = true)
    {
        if (is_array($container)) {
            $container = $this->buildContainer($container);
        }

        if (!$container instanceof Container) {
            $container = new Container($container);
        }

        if ($useSlimService) {
            (new SlimDefaultServiceProvider($container))->register();
        } else {
            (new LaravelServiceProvider($container))->register();
        }

        parent::__construct($container);
    }

    /**
     * @param array $containerAssociation
     * @return ContainerContracts
     */
    protected function buildContainer(array $containerAssociation)
    {
        $container = new Container();

        foreach ($containerAssociation as $abstract => $concrete) {
            if (is_callable($concrete) || (is_string($concrete) && class_exists($concrete))) {
                $container->singleton($abstract, $concrete);
            } else {
                $container->instance($abstract, $concrete);
            }
        }

        return $container;
    }
}
