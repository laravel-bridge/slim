<?php

declare(strict_types=1);

namespace LaravelBridge\Slim\Handlers;

use Exception;
use Psr\Http\Message\ResponseInterface as Psr7Response;
use Psr\Http\Message\ServerRequestInterface as Psr7Request;
use Slim\Handlers\Error as SlimError;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;

class Error extends AbstractHandler
{
    /**
     * @param Psr7Request $request The most recent Request object
     * @param Psr7Response $response The most recent Response object
     * @param Exception $exception The caught Exception object
     * @return Psr7Response
     */
    public function __invoke(Psr7Request $request, Psr7Response $response, Exception $exception)
    {
        $this->report($exception);

        try {
            $laravelResponse = $this->render($this->createLaravelRequestFromPsr7($request), $exception);
        } catch (Exception $ex) {
            return $this->renderExceptionWithSlim($request, $response, $exception);
        }

        return (new DiactorosFactory())->createResponse($laravelResponse);
    }

    /**
     * @param Psr7Request $request
     * @param Psr7Response $response
     * @param Exception $e
     * @return Psr7Response
     */
    private function renderExceptionWithSlim(Psr7Request $request, Psr7Response $response, Exception $e)
    {
        $slimError = new SlimError($this->displayErrorDetails());

        return $slimError($request, $response, $e);
    }
}
