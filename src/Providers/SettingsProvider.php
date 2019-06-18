<?php

namespace LaravelBridge\Slim\Providers;

use Illuminate\Support\ServiceProvider;
use Slim\Collection;

class SettingsProvider extends ServiceProvider
{
    use SettingsAwareTrait;

    public function register()
    {
        $this->app->bindIf('settings', function () {
            return new Collection($this->settings);
        }, true);
    }
}
