<?php

namespace Photogabble\Tuppence\ErrorHandlers;

use Exception;
use League\Route\Http\Exception\NotFoundException as RouteNotFoundException;
use League\Container\Exception\NotFoundException as ContainerNotFoundException;
use Zend\Diactoros\Response\JsonResponse;

class DefaultExceptionHandler implements ExceptionHandler
{
    /**
     * @param Exception|RouteNotFoundException|ContainerNotFoundException $e
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Exception $e)
    {
        return new JsonResponse([
            'message' => $e->getMessage(),
            'trace' => explode(PHP_EOL, $e->getTraceAsString())
        ], (method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500));
    }
}