<?php

declare(strict_types=1);

namespace LaravelBridge\Slim;

use Illuminate\Container\Container;
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
     * @return Container
     */
    public function build()
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
    public function useLaravelAllHandler()
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
    public function useLaravelErrorHandler()
    {
        $this->providers['error'] = LaravelErrorHandlerProvider::class;

        return $this;
    }

    /**
     * @return static
     */
    public function useLaravelFoundHandler()
    {
        $this->providers['found'] = LaravelFoundHandlerProvider::class;

        return $this;
    }

    /**
     * @return static
     */
    public function useLaravelHttp()
    {
        $this->providers['http'] = LaravelHttpProvider::class;

        return $this;
    }

    /**
     * @return static
     */
    public function useLaravelNotAllowedHandler()
    {
        $this->providers['notAllowed'] = LaravelNotAllowedProvider::class;

        return $this;
    }

    /**
     * @return static
     */
    public function useLaravelNotFoundHandler()
    {
        $this->providers['notFound'] = LaravelNotFoundProvider::class;

        return $this;
    }

    /**
     * @return static
     */
    public function useLaravelPhpErrorHandler()
    {
        $this->providers['phpError'] = LaravelPhpErrorHandlerProvider::class;

        return $this;
    }

    /**
     * @param array $settings
     * @return static
     */
    public function useLaravelSettings($settings = [])
    {
        $this->providers['settings'] = LaravelSettingsProvider::class;

        $this->setSettings($settings);

        return $this;
    }

    /**
     * @param string $class
     * @param Container $container
     * @return ServiceProvider
     */
    private function resolveProvider($class, $container)
    {
        $provider = new $class($container);

        if (method_exists($provider, 'setSettings')) {
            $provider->setSettings($this->settings);
        }

        return $provider;
    }
}
