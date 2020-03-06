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

            $services = [];
        } else {
            parent::__construct(new Container());

            $services = $container;
        }

        $this->provisionService($services);

        $this->createProvider($useLaravelService, $this->resolveSettings($services))->register();
    }

    /**
     * @param bool $useLaravelService
     * @param array $settings
     * @return ServiceProvider
     */
    private function createProvider($useLaravelService, $settings = []): ServiceProvider
    {
        /** @var ContainerContracts $container */
        $container = $this->getContainer();

        if ($useLaravelService) {
            $provider = new LaravelServiceProvider($container);
        } else {
            $provider = new SlimDefaultServiceProvider($container);
        }

        $provider->setSettings($settings);

        return $provider;
    }

    /**
     * @param array $services
     */
    private function provisionService(array $services): void
    {
        /** @var ContainerContracts $container */
        $container = $this->getContainer();

        foreach ($services as $abstract => $concrete) {
            if ('settings' === $abstract && is_array($concrete)) {
                continue;
            }

            if (is_callable($concrete) || (is_string($concrete) && class_exists($concrete))) {
                $container->singleton($abstract, $concrete);
            } else {
                $container->instance($abstract, $concrete);
            }
        }
    }

    /**
     * @param mixed $values
     * @return array
     */
    private function resolveSettings($values): array
    {
        /** @var ContainerContracts $container */
        $container = $this->getContainer();

        if (isset($values['settings']) && is_array($values['settings'])) {
            return array_merge($container['settings'], $values['settings']);
        }

        return [];
    }
}
