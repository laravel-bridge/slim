<?php

declare(strict_types=1);

namespace LaravelBridge\Slim\Providers\Laravel;

use Illuminate\Config\Repository;
use Illuminate\Support\ServiceProvider;
use LaravelBridge\Slim\Providers\SettingsAwareTrait;

class SettingsProvider extends ServiceProvider
{
    use SettingsAwareTrait;

    public function register()
    {
        $this->app->bindIf('settings', function () {
            return new Repository($this->settings);
        }, true);
    }
}
