<?php

declare(strict_types=1);

namespace LaravelBridge\Slim;

use Illuminate\Container\Container;
use Illuminate\Contracts\Container\Container as ContainerContract;
use Illuminate\Support\ServiceProvider;
use LaravelBridge\Slim\Providers\BaseProvider;
use LaravelBridge\Slim\Providers\ErrorHandlerProvider;
use LaravelBridge\Slim\Providers\FoundHandlerProvider;
use LaravelBridge\Slim\Providers\HttpProvider;
use LaravelBridge\Slim\Providers\Laravel\ErrorHandlerProvider as LaravelErrorHandlerProvider;
use LaravelBridge\Slim\Providers\Laravel\FoundHandlerProvider as LaravelFoundHandlerProvider;
use LaravelBridge\Slim\Providers\Laravel\HttpProvider as LaravelHttpProvider;
use LaravelBridge\Slim\Providers\Laravel\NotAllowedProvider as LaravelNotAllowedProvider;
use LaravelBridge\Slim\Providers\Laravel\NotFoundProvider as LaravelNotFoundProvider;
use LaravelBridge\Slim\Providers\Laravel\PhpErrorHandlerProvider as LaravelPhpErrorHandlerProvider;
use LaravelBridge\Slim\Providers\Laravel\SettingsProvider as LaravelSettingsProvider;
use LaravelBridge\Slim\Providers\NotAllowedProvider;
use LaravelBridge\Slim\Providers\NotFoundProvider;
use LaravelBridge\Slim\Providers\PhpErrorHandlerProvider;
use LaravelBridge\Slim\Providers\SettingsAwareTrait;
use LaravelBridge\Slim\Providers\SettingsProvider;

class ContainerBuilder
{
    use SettingsAwareTrait;

    /**
     * @var array
     */
    private $providers = [
        'base' => BaseProvider::class,
        'error' => ErrorHandlerProvider::class,
        'found' => FoundHandlerProvider::class,
        'http' => HttpProvider::class,
        'notAllowed' => NotAllowedProvider::class,
        'notFound' => NotFoundProvider::class,
        'phpError' => PhpErrorHandlerProvider::class,
        'settings' => SettingsProvider::class,
    ];

    /**
     * @return ContainerContract
     */
    public function build(): ContainerContract
    {
        $container = new Container();

        foreach ($this->providers as $provider) {
            $provider = $this->resolveProvider($provider, $container);
            $provider->register();
        }

        return $container;
    }

    /**
     * @return static
     */
    public function useLaravelAllHandler(): ContainerBuilder
    {
        $this->useLaravelErrorHandler();
        $this->useLaravelFoundHandler();
        $this->useLaravelNotAllowedHandler();
        $this->useLaravelNotFoundHandler();
        $this->useLaravelPhpErrorHandler();

        return $this;
    }

    /**
     * @return static
     */
    public function useLaravelErrorHandler(): ContainerBuilder
    {
        $this->providers['error'] = LaravelErrorHandlerProvider::class;

        return $this;
    }

    /**
     * @return static
     */
    public function useLaravelFoundHandler(): ContainerBuilder
    {
        $this->providers['found'] = LaravelFoundHandlerProvider::class;

        return $this;
    }

    /**
     * @return static
     */
    public function useLaravelHttp(): ContainerBuilder
    {
        $this->providers['http'] = LaravelHttpProvider::class;

        return $this;
    }

    /**
     * @return static
     */
    public function useLaravelNotAllowedHandler(): ContainerBuilder
    {
        $this->providers['notAllowed'] = LaravelNotAllowedProvider::class;

        return $this;
    }

    /**
     * @return static
     */
    public function useLaravelNotFoundHandler(): ContainerBuilder
    {
        $this->providers['notFound'] = LaravelNotFoundProvider::class;

        return $this;
    }

    /**
     * @return static
     */
    public function useLaravelPhpErrorHandler(): ContainerBuilder
    {
        $this->providers['phpError'] = LaravelPhpErrorHandlerProvider::class;

        return $this;
    }

    /**
     * @param array $settings
     * @return static
     */
    public function useLaravelSettings($settings = []): ContainerBuilder
    {
        $this->providers['settings'] = LaravelSettingsProvider::class;

        $this->setSettings($settings);

        return $this;
    }

    /**
     * @param string $class
     * @param ContainerContract $container
     * @return ServiceProvider
     */
    private function resolveProvider($class, $container): ServiceProvider
    {
        $provider = new $class($container);

        if (method_exists($provider, 'setSettings')) {
            $provider->setSettings($this->settings);
        }

        return $provider;
    }
}
