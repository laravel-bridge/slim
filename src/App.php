<?php

namespace LaravelBridge\Slim;

use Illuminate\Contracts\Container\Container as ContainerContracts;
use Slim\App as SlimApp;

class App extends SlimApp
{
    /**
     * @param ContainerContracts|array $container
     * @param bool $useLaravelService
     */
    public function __construct($container = [], $useLaravelService = true)
    {
        parent::__construct(new Container($container, $useLaravelService));
    }
}
