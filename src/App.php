<?php

declare(strict_types=1);

namespace LaravelBridge\Slim;

use Illuminate\Container\Container;
use Illuminate\Contracts\Container\Container as ContainerContracts;
use Illuminate\Support\ServiceProvider;
use LaravelBridge\Slim\Providers\Laravel\LaravelServiceProvider;
use LaravelBridge\Slim\Providers\SlimDefaultServiceProvider;
use Slim\App as SlimApp;

class App extends SlimApp
{
    /**
     * @param ContainerContracts|array $container
     * @param bool $useLaravelService
     */
    public function __construct($container = [], $useLaravelService = false)
    {
        if ($container instanceof ContainerContracts) {
            parent::__construct($container);

            $this->registerDefaultProvider($useLaravelService);
        } else {
            parent::__construct(new Container());

            $this->provisionService($container);

            $this->createProvider($useLaravelService)->register();
        }
    }

    private function registerDefaultProvider(bool $useLaravelService): void
    {
        $this->createProvider($useLaravelService)->register();
    }

    /**
     * @param bool $useLaravelService
     * @return ServiceProvider
     */
    private function createProvider($useLaravelService): ServiceProvider
    {
        /** @var ContainerContracts $container */
        $container = $this->getContainer();

        if ($useLaravelService) {
            return new LaravelServiceProvider($container);
        }

        return new SlimDefaultServiceProvider($container);
    }

    /**
     * @param array $services
     */
    private function provisionService(array $services): void
    {
        /** @var ContainerContracts $container */
        $container = $this->getContainer();

        foreach ($services as $abstract => $concrete) {
            if (is_callable($concrete) || (is_string($concrete) && class_exists($concrete))) {
                $container->singleton($abstract, $concrete);
            } else {
                $container->instance($abstract, $concrete);
            }
        }
    }
}
