<?php

namespace LaravelBridge\Slim\Providers;

use Http\Factory\Slim\ResponseFactory;
use Http\Factory\Slim\ServerRequestFactory;
use Http\Factory\Slim\StreamFactory;
use Http\Factory\Slim\UploadedFileFactory;
use Illuminate\Support\ServiceProvider;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;

class HttpFactoryProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bindIf(ServerRequestFactoryInterface::class, ServerRequestFactory::class);
        $this->app->bindIf(StreamFactoryInterface::class, StreamFactory::class);
        $this->app->bindIf(UploadedFileFactoryInterface::class, UploadedFileFactory::class);
        $this->app->bindIf(ResponseFactoryInterface::class, ResponseFactory::class);
    }
}
