<?php

namespace LaravelBridge\Slim\Traits;

use Illuminate\Http\Request as LaravelRequest;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface as Psr7Request;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

trait HttpTransformerTrait
{
    /**
     * @param mixed $request
     * @return LaravelRequest
     */
    protected function createLaravelRequest($request)
    {
        if ($request instanceof LaravelRequest) {
            return $request;
        }

        if ($request instanceof Psr7Request) {
            $request = (new HttpFoundationFactory)->createRequest($request);
        }

        if ($request instanceof SymfonyRequest) {
            return LaravelRequest::createFromBase($request);
        }

        throw new InvalidArgumentException('Unknown request type');
    }
}
