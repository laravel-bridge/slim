<?php

declare(strict_types=1);

namespace LaravelBridge\Slim\Handlers;

use Exception;
use Psr\Http\Message\ResponseInterface as Psr7Response;
use Psr\Http\Message\ServerRequestInterface as Psr7Request;
use Slim\Handlers\NotFound as SlimNotFound;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NotFound extends AbstractHandler
{
    /**
     * @param Psr7Request $request The most recent Request object
     * @param Psr7Response $response The most recent Response object
     * @return Psr7Response
     */
    public function __invoke(Psr7Request $request, Psr7Response $response)
    {
        try {
            $laravelResponse = $this->render(
                $this->createLaravelRequestFromPsr7($request),
                new NotFoundHttpException()
            );
        } catch (Exception $ex) {
            return $this->renderUsingSlimNotFound($request, $response);
        }

        return $this->getContainer()->make(PsrHttpFactory::class)->createResponse($laravelResponse);
    }

    /**
     * @param Psr7Request $request
     * @param Psr7Response $response
     * @return Psr7Response
     */
    private function renderUsingSlimNotFound(Psr7Request $request, Psr7Response $response)
    {
        $instance = new SlimNotFound();

        return $instance($request, $response);
    }
}
