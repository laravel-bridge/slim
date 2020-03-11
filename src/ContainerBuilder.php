<?php

declare(strict_types=1);

namespace LaravelBridge\Slim;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;
use LaravelBridge\Container\Traits\LaravelBridgeContainerAwareTrait;
use LaravelBridge\Scratch\Application;
use LaravelBridge\Slim\Providers\BaseProvider;
use LaravelBridge\Slim\Providers\CallableResolverProvider;
use LaravelBridge\Slim\Providers\ErrorHandlerProvider;
use LaravelBridge\Slim\Providers\FoundHandlerProvider;
use LaravelBridge\Slim\Providers\HttpFactoryProvider;
use LaravelBridge\Slim\Providers\HttpProvider;
use LaravelBridge\Slim\Providers\Laravel\CallableResolverProvider as LaravelCallableResolverProvider;
use LaravelBridge\Slim\Providers\Laravel\ErrorHandlerProvider as LaravelErrorHandlerProvider;
use LaravelBridge\Slim\Providers\Laravel\FoundHandlerProvider as LaravelFoundHandlerProvider;
use LaravelBridge\Slim\Providers\Laravel\HttpProvider as LaravelHttpProvider;
use LaravelBridge\Slim\Providers\Laravel\NotAllowedProvider as LaravelNotAllowedProvider;
use LaravelBridge\Slim\Providers\Laravel\NotFoundProvider as LaravelNotFoundProvider;
use LaravelBridge\Slim\Providers\Laravel\PhpErrorHandlerProvider as LaravelPhpErrorHandlerProvider;
use LaravelBridge\Slim\Providers\NotAllowedProvider;
use LaravelBridge\Slim\Providers\NotFoundProvider;
use LaravelBridge\Slim\Providers\PhpErrorHandlerProvider;
use LaravelBridge\Slim\Providers\SettingsAwareTrait;
use Slim\Collection;

class ContainerBuilder
{
    use LaravelBridgeContainerAwareTrait;
    use SettingsAwareTrait;

    /**
     * @var array
     */
    private $providers = [
        'base' => BaseProvider::class,
        'httpFactory' => HttpFactoryProvider::class,
    ];

    /**
     * @var array
     */
    private $services;

    /**
     * @var bool
     */
    private $useLaravelSetting = false;

    /**
     * @param Container|array $container
     * @param bool $useLaravelService
     */
    public function __construct($container = [], $useLaravelService = false)
    {
        $this->prepareContainer($container);

        if ($useLaravelService) {
            $this->useLaravelAllProviders();
        } else {
            $this->useSlimAllProviders();
        }
    }

    /**
     * @return Application
     */
    public function build(): Application
    {
        foreach ($this->providers as $provider) {
            $this->container->setupProvider($provider);
        }

        $this->registerSettingProvider($this->container);

        $this->container->setupConfig('settings', $this->settings);

        return $this->container;
    }

    /**
     * @return static
     */
    public function useLaravelAllProviders(): ContainerBuilder
    {
        $this->useLaravelCallableResolver();
        $this->useLaravelErrorHandler();
        $this->useLaravelFoundHandler();
        $this->useLaravelHttp();
        $this->useLaravelNotAllowedHandler();
        $this->useLaravelNotFoundHandler();
        $this->useLaravelPhpErrorHandler();

        $this->useLaravelSettings(true);

        return $this;
    }

    /**
     * @return static
     */
    public function useSlimAllProviders(): ContainerBuilder
    {
        $this->useCustomCallableResolver(CallableResolverProvider::class);
        $this->useCustomErrorHandler(ErrorHandlerProvider::class);
        $this->useCustomFoundHandler(FoundHandlerProvider::class);
        $this->useCustomHttp(HttpProvider::class);
        $this->useCustomHttpFactory(HttpFactoryProvider::class);
        $this->useCustomNotAllowedHandler(NotAllowedProvider::class);
        $this->useCustomNotFoundHandler(NotFoundProvider::class);
        $this->useCustomPhpErrorHandler(PhpErrorHandlerProvider::class);

        $this->useLaravelSettings(false);

        return $this;
    }

    /**
     * @param ServiceProvider|string $provider
     * @return static
     */
    public function useCustomCallableResolver($provider): ContainerBuilder
    {
        $this->providers['callableResolver'] = $provider;

        return $this;
    }

    /**
     * @param ServiceProvider|string $provider
     * @return static
     */
    public function useCustomErrorHandler($provider): ContainerBuilder
    {
        $this->providers['error'] = $provider;

        return $this;
    }

    /**
     * @param ServiceProvider|string $provider
     * @return static
     */
    public function useCustomFoundHandler($provider): ContainerBuilder
    {
        $this->providers['found'] = $provider;

        return $this;
    }

    /**
     * @param ServiceProvider|string $provider
     * @return static
     */
    public function useCustomHttp($provider): ContainerBuilder
    {
        $this->providers['http'] = $provider;

        return $this;
    }

    /**
     * @param ServiceProvider|string $provider
     * @return static
     */
    public function useCustomHttpFactory($provider): ContainerBuilder
    {
        $this->providers['httpFactory'] = $provider;

        return $this;
    }

    /**
     * @param ServiceProvider|string $provider
     * @return static
     */
    public function useCustomNotAllowedHandler($provider): ContainerBuilder
    {
        $this->providers['notAllowed'] = $provider;

        return $this;
    }

    /**
     * @param ServiceProvider|string $provider
     * @return static
     */
    public function useCustomNotFoundHandler($provider): ContainerBuilder
    {
        $this->providers['notFound'] = $provider;

        return $this;
    }

    /**
     * @param ServiceProvider|string $provider
     * @return static
     */
    public function useCustomPhpErrorHandler($provider): ContainerBuilder
    {
        $this->providers['phpError'] = $provider;

        return $this;
    }

    /**
     * @return static
     */
    public function useLaravelCallableResolver(): ContainerBuilder
    {
        return $this->useCustomCallableResolver(LaravelCallableResolverProvider::class);
    }

    /**
     * @return static
     */
    public function useLaravelErrorHandler(): ContainerBuilder
    {
        return $this->useCustomErrorHandler(LaravelErrorHandlerProvider::class);
    }

    /**
     * @return static
     */
    public function useLaravelFoundHandler(): ContainerBuilder
    {
        return $this->useCustomFoundHandler(LaravelFoundHandlerProvider::class);
    }

    /**
     * @return static
     */
    public function useLaravelHttp(): ContainerBuilder
    {
        return $this->useCustomHttp(LaravelHttpProvider::class);
    }

    /**
     * @return static
     */
    public function useLaravelNotAllowedHandler(): ContainerBuilder
    {
        return $this->useCustomNotAllowedHandler(LaravelNotAllowedProvider::class);
    }

    /**
     * @return static
     */
    public function useLaravelNotFoundHandler(): ContainerBuilder
    {
        return $this->useCustomNotFoundHandler(LaravelNotFoundProvider::class);
    }

    /**
     * @return static
     */
    public function useLaravelPhpErrorHandler(): ContainerBuilder
    {
        return $this->useCustomPhpErrorHandler(LaravelPhpErrorHandlerProvider::class);
    }

    /**
     * @param bool $use
     * @return static
     */
    public function useLaravelSettings(bool $use = true): ContainerBuilder
    {
        $this->useLaravelSetting = $use;

        return $this;
    }

    /**
     * @param $container
     * @return Application
     */
    private function prepareContainer($container): Application
    {
        if ($container instanceof Container) {
            return $this->container = Application::createFromBase($container);
        }

        $this->container = new Application();

        $this->prepareServices($container);

        return $this->container;
    }

    /**
     * @param array $services
     */
    private function prepareServices(array $services): void
    {
        $this->setSettingsByServices($services);

        // Ensure settings key is unset
        unset($services['settings']);

        foreach ($services as $abstract => $concrete) {
            if (is_callable($concrete) || (is_string($concrete) && class_exists($concrete))) {
                $this->container->singleton($abstract, $concrete);
            } else {
                $this->container->instance($abstract, $concrete);
            }
        }
    }

    private function registerSettingProvider(Application $container): void
    {
        $container->bindIf('settings', function () {
            if ($this->useLaravelSetting) {
                return new Repository($this->settings);
            }

            return new Collection($this->settings);
        }, true);
    }
}
