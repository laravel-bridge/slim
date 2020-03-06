<?php

namespace LaravelBridge\Slim\Providers;

use Illuminate\Support\ServiceProvider;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\StreamFactory;
use Laminas\Diactoros\UploadedFileFactory;
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
