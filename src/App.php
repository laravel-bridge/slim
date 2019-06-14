<?php

namespace MilesChou\LaravelBridger\Slim;

use Recca0120\LaravelBridge\Laravel;
use Slim\App as SlimApp;

/**
 * @mixin SlimApp
 */
class App extends SlimApp
{
    use PrepareContainerTrait;

    /**
     * @param Laravel $container
     */
    public function __construct(Laravel $container)
    {
        $this->prepareLaravelContainer($container);

        parent::__construct($container);
    }
}
