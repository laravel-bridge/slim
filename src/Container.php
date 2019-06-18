<?php

namespace LaravelBridge\Slim;

use LaravelBridge\Support\ContainerBridge;
use LaravelBridge\Support\Pimple\ServiceProviderBridge;
use Pimple\ServiceProviderInterface;

class Container extends ContainerBridge
{
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
}
