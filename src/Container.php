<?php

namespace LaravelBridge\Slim;

use Illuminate\Contracts\Container\Container as ContainerContracts;
use Illuminate\Support\ServiceProvider;
use LaravelBridge\Slim\Providers\Laravel\LaravelServiceProvider;
use LaravelBridge\Slim\Providers\SlimDefaultServiceProvider;
use LaravelBridge\Support\ContainerBridge;
use LaravelBridge\Support\Pimple\ServiceProviderBridge;
use Pimple\ServiceProviderInterface;

class Container extends ContainerBridge
{
    /**
     * @var array
     */
    protected $settings = [
        'httpVersion' => '1.1',
        'responseChunkSize' => 4096,
        'outputBuffering' => 'append',
        'determineRouteBeforeAppMiddleware' => false,
        'displayErrorDetails' => false,
        'addContentLengthHeader' => true,
        'routerCacheFile' => false,
    ];

    /**
     * @param ContainerContracts|array $values
     * @param bool $useLaravelService
     */
    public function __construct($values = [], $useLaravelService = false)
    {
        if ($values instanceof ContainerContracts) {
            parent::__construct($values);

            $this->createProvider($useLaravelService)->register();
        } else {
            parent::__construct();

            $this->provisionService($values);

            $this->createProvider($useLaravelService, $this->resolveSettings($values))->register();
        }
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
     * @param array $settings
     * @return ServiceProvider
     */
    private function createProvider($useLaravelService, $settings = [])
    {
        if ($useLaravelService) {
            $provider = new LaravelServiceProvider($this);
        } else {
            $provider = new SlimDefaultServiceProvider($this);
        }

        $provider->setSettings($settings);

        return $provider;
    }

    /**
     * @param array $services
     */
    private function provisionService(array $services)
    {
        foreach ($services as $abstract => $concrete) {
            if ('settings' === $abstract && is_array($concrete)) {
                continue;
            }

            if (is_callable($concrete) || (is_string($concrete) && class_exists($concrete))) {
                $this->container->singleton($abstract, $concrete);
            } else {
                $this->container->instance($abstract, $concrete);
            }
        }
    }

    /**
     * @param mixed $values
     * @return array
     */
    protected function resolveSettings($values)
    {
        if (isset($values['settings']) && is_array($values['settings'])) {
            return array_merge($this->settings, $values['settings']);
        }

        return [];
    }
}
