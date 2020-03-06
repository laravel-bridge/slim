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

            $this->createProvider($container, $useLaravelService)->register();
        } else {
            $values = $container;
            $container = new Container();

            parent::__construct($container);

            $this->provisionService($container, $values);

            $this->createProvider(
                $container,
                $useLaravelService,
                $this->resolveSettings($container, $values)
            )->register();
        }
    }

    /**
     * @param ContainerContracts $container
     * @param bool $useLaravelService
     * @param array $settings
     * @return ServiceProvider
     */
    private function createProvider(ContainerContracts $container, $useLaravelService, $settings = [])
    {
        if ($useLaravelService) {
            $provider = new LaravelServiceProvider($container);
        } else {
            $provider = new SlimDefaultServiceProvider($container);
        }

        $provider->setSettings($settings);

        return $provider;
    }

    /**
     * @param ContainerContracts $container
     * @param array $services
     */
    private function provisionService(ContainerContracts $container, array $services)
    {
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
     * @param ContainerContracts $container
     * @param mixed $values
     * @return array
     */
    private function resolveSettings(ContainerContracts $container, $values)
    {
        if (isset($values['settings']) && is_array($values['settings'])) {
            return array_merge($container->settings, $values['settings']);
        }

        return [];
    }
}
