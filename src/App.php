<?php

namespace MilesChou\LaravelBridger\Slim;

use Slim\App as SlimApp;

/**
 * @mixin SlimApp
 */
class App
{
    /**
     * @var SlimApp
     */
    private $slimApp;

    /**
     * @param SlimApp $slimApp
     */
    public function __construct(SlimApp $slimApp)
    {
        $this->slimApp = $slimApp;
    }

    public function __call($method, $arguments)
    {
        return $this->slimApp->__call($method, $arguments);
    }
}
