<?php

declare(strict_types=1);

namespace LaravelBridge\Slim\Providers\Laravel;

use Illuminate\Config\Repository;
use Illuminate\Support\ServiceProvider;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\StreamFactory;
use Laminas\Diactoros\UploadedFileFactory;
use LaravelBridge\Slim\Providers\BaseProvider;
use LaravelBridge\Slim\Providers\HttpFactoryProvider;
use LaravelBridge\Slim\Providers\SettingsAwareTrait;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;

class LaravelServiceProvider extends ServiceProvider
{
    use SettingsAwareTrait;

    public function register()
    {
        $this->app->instance('settings', new Repository($this->settings));

        (new BaseProvider($this->app))->register();
        (new ErrorHandlerProvider($this->app))->register();
        (new FoundHandlerProvider($this->app))->register();
        (new HttpProvider($this->app))->register();
        (new NotAllowedProvider($this->app))->register();
        (new NotFoundProvider($this->app))->register();
        (new HttpFactoryProvider($this->app))->register();
    }
}
