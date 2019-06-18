<?php

namespace LaravelBridge\Slim;

use Illuminate\Contracts\Container\Container as ContainerContracts;
use LaravelBridge\Support\ContainerBridge;
use LaravelBridge\Support\Pimple\ServiceProviderBridge;
use Pimple\ServiceProviderInterface;

class Container extends ContainerBridge
{
    /**
     * @param ContainerContracts|array $values
     * @param bool $useLaravelService
     */
    public function __construct($values = [], $useLaravelService = false)
    {
        if ($values instanceof ContainerContracts) {
            parent::__construct($values);
        } else {
            parent::__construct();
            $this->provisionService($values);
        }

        $provider = $this->createProvider($useLaravelService);

        if (isset($values['settings']) && is_array($values['settings'])) {
            $provider->setSettings($values['settings']);
        }

        $provider->register();
    }

    /**
     * Simulate Pimple::register
     *
     * @param ServiceProviderInterface $provider
     * @return static
     */
    public function register($provider)
    {
        (new ServiceProviderBridge($this))->register($provider);

        return $this;
    }

    /**
     * @param bool $useLaravelService
     * @return LaravelServiceProvider|SlimDefaultServiceProvider
     */
    private function createProvider($useLaravelService)
    {
        if ($useLaravelService) {
            $provider = new LaravelServiceProvider($this);
        } else {
            $provider = new SlimDefaultServiceProvider($this);
        }

        return $provider;
    }

    /**
     * @param array $services
     */
    private function provisionService(array $services)
    {
        foreach ($services as $abstract => $concrete) {
            if (is_callable($concrete) || (is_string($concrete) && class_exists($concrete))) {
                $this->container->singleton($abstract, $concrete);
            } else {
                $this->container->instance($abstract, $concrete);
            }
        }
    }
}
