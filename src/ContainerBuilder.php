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
        'httpFactory' => HttpFactoryProvider::class,
        'notAllowed' => NotAllowedProvider::class,
        'notFound' => NotFoundProvider::class,
        'phpError' => PhpErrorHandlerProvider::class,
    ];

    private $useLaravelSetting = false;

    /**
     * @var Application
     */
    private $container;

    public function __construct(Application $container = null, $useLaravelService = false)
    {
        $this->container = $container ?? new Application();

        if ($useLaravelService) {
            $this->providers = [
                'base' => BaseProvider::class,
                'error' => LaravelErrorHandlerProvider::class,
                'found' => LaravelFoundHandlerProvider::class,
                'http' => LaravelHttpProvider::class,
                'httpFactory' => HttpFactoryProvider::class,
                'notAllowed' => LaravelNotAllowedProvider::class,
                'notFound' => LaravelNotFoundProvider::class,
                'phpError' => LaravelPhpErrorHandlerProvider::class,
            ];
        } else {
            $this->providers = [
                'base' => BaseProvider::class,
                'error' => ErrorHandlerProvider::class,
                'found' => FoundHandlerProvider::class,
                'http' => HttpProvider::class,
                'httpFactory' => HttpFactoryProvider::class,
                'notAllowed' => NotAllowedProvider::class,
                'notFound' => NotFoundProvider::class,
                'phpError' => PhpErrorHandlerProvider::class,
            ];
        }
    }

    /**
     * @return Application
     */
    public function build(): Application
    {
        $this->container->setupProvider(HttpFactoryProvider::class);

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
    public function useLaravelAllHandler(): ContainerBuilder
    {
        $this->useLaravelErrorHandler();
        $this->useLaravelFoundHandler();
        $this->useLaravelHttp();
        $this->useLaravelNotAllowedHandler();
        $this->useLaravelNotFoundHandler();
        $this->useLaravelPhpErrorHandler();
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
        $container->singleton('settings', function () {
            if ($this->useLaravelSetting) {
                return new Repository($this->settings);
            }

            return new Collection($this->settings);
        });
    }
}
