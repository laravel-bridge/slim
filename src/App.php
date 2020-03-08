<?php

declare(strict_types=1);

namespace LaravelBridge\Slim;

use Illuminate\Container\Container;
use Slim\App as SlimApp;

class App extends SlimApp
{
    /**
     * @param Container|array $container
     * @param bool $useLaravelService
     */
    public function __construct($container = [], $useLaravelService = false)
    {
        $containerBuilder = new ContainerBuilder($container, $useLaravelService);

        parent::__construct($containerBuilder->build());
    }
}
