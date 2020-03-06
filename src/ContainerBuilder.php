<?php

declare(strict_types=1);

namespace LaravelBridge\Slim;

use Illuminate\Config\Repository;
use LaravelBridge\Scratch\Application;
use LaravelBridge\Slim\Providers\BaseProvider;
use LaravelBridge\Slim\Providers\ErrorHandlerProvider;
use LaravelBridge\Slim\Providers\FoundHandlerProvider;
use LaravelBridge\Slim\Providers\HttpFactoryProvider;
use LaravelBridge\Slim\Providers\HttpProvider;
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
    ];

    private $useLaravelSetting = false;

    /**
     * @return Application
     */
    public function build(): Application
    {
        $container = new Application();

        $container->setupProvider(HttpFactoryProvider::class);

        foreach ($this->providers as $provider) {
            $container->setupProvider($provider);
        }

        $this->registerSettingProvider($container);

        $container->setupConfig('settings', $this->settings);

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
        $this->useLaravelHttp();
        $this->useLaravelSettings();

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
     * @return static
     */
    public function useLaravelSettings(): ContainerBuilder
    {
        $this->useLaravelSetting = true;

        return $this;
    }

    private function registerSettingProvider(Application $container): void
    {
        if ($this->useLaravelSetting) {
            $container->instance('settings', new Repository($this->settings));
        } else {
            $container->instance('settings', new Collection($this->settings));
        }
    }
}
