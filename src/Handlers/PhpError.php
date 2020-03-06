<?php

declare(strict_types=1);

namespace LaravelBridge\Slim\Handlers;

use Exception;
use Psr\Http\Message\ResponseInterface as Psr7Response;
use Psr\Http\Message\ServerRequestInterface as Psr7Request;
use Slim\Handlers\PhpError as SlimPhpError;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Throwable;

class PhpError extends AbstractHandler
{
    /**
     * @param Psr7Request $request The most recent Request object
     * @param Psr7Response $response The most recent Response object
     * @param Throwable $error The caught Throwable object
     * @return Psr7Response
     */
    public function __invoke(Psr7Request $request, Psr7Response $response, Throwable $error)
    {
        $exception = new FatalThrowableError($error);

        $this->report($exception);

        try {
            $laravelResponse = $this->render($this->createLaravelRequestFromPsr7($request), $exception);
        } catch (Exception $ex) {
            return $this->renderSlimPhpError($request, $response, $exception);
        }

        return $this->getContainer()->make(PsrHttpFactory::class)->createResponse($laravelResponse);
    }

    /**
     * @param Psr7Request $request
     * @param Psr7Response $response
     * @param Throwable $error
     * @return Psr7Response
     */
    private function renderSlimPhpError(Psr7Request $request, Psr7Response $response, Throwable $error)
    {
        $slimError = new SlimPhpError($this->displayErrorDetails());

        return $slimError($request, $response, $error);
    }
}
