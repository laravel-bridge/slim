<?php

declare(strict_types=1);

namespace LaravelBridge\Slim\Providers\Laravel;

use Illuminate\Config\Repository;
use Illuminate\Support\ServiceProvider;
use LaravelBridge\Slim\Providers\BaseProvider;
use LaravelBridge\Slim\Providers\HttpFactoryProvider;
use LaravelBridge\Slim\Providers\SettingsAwareTrait;

class LaravelServiceProvider extends ServiceProvider
{
    use SettingsAwareTrait;

    public function register()
    {
        if (!$this->app->bound('settings')) {
            $this->app->instance('settings', new Repository($this->settings));
        }

        (new BaseProvider($this->app))->register();
        (new ErrorHandlerProvider($this->app))->register();
        (new FoundHandlerProvider($this->app))->register();
        (new HttpProvider($this->app))->register();
        (new NotAllowedProvider($this->app))->register();
        (new NotFoundProvider($this->app))->register();
        (new HttpFactoryProvider($this->app))->register();
    }
}
