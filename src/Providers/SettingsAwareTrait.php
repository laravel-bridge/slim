<?php

declare(strict_types=1);

namespace LaravelBridge\Slim\Providers;

trait SettingsAwareTrait
{
    /**
     * @var array
     */
    protected $settings = [
        'httpVersion' => '1.1',
        'responseChunkSize' => 4096,
        'outputBuffering' => 'append',
        'determineRouteBeforeAppMiddleware' => false,
        'displayErrorDetails' => false,
        'addContentLengthHeader' => true,
        'routerCacheFile' => false,
    ];

    /**
     * @param array $settings
     * @return static
     */
    public function setSettings(array $settings)
    {
        $this->settings = array_merge($this->settings, $settings);

        return $this;
    }
}
