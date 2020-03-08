<?php

declare(strict_types=1);

namespace LaravelBridge\Slim\Providers;

use Illuminate\Support\ServiceProvider;
use Slim\Collection;

class SlimDefaultServiceProvider extends ServiceProvider
{
    use SettingsAwareTrait;

    public function register()
    {
        if (!$this->app->bound('settings')) {
            $this->app->instance('settings', new Collection($this->settings));
        }

        (new BaseProvider($this->app))->register();
        (new ErrorHandlerProvider($this->app))->register();
        (new FoundHandlerProvider($this->app))->register();
        (new HttpProvider($this->app))->register();
        (new HttpFactoryProvider($this->app))->register();
        (new NotAllowedProvider($this->app))->register();
        (new NotFoundProvider($this->app))->register();
        (new PhpErrorHandlerProvider($this->app))->register();
    }
}
