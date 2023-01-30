<?php

namespace Photogabble\Tuppence\ErrorHandlers;

use Exception;
use League\Route\Http\Exception\NotFoundException as RouteNotFoundException;
use League\Container\Exception\NotFoundException as ContainerNotFoundException;
use Psr\Http\Message\RequestInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

class DefaultExceptionHandler implements ExceptionHandler
{

    /**
     * Exceptions this handler should ignore and pass through.
     */
    protected array $ignore = [];

    /**
     * @param string $class
     * @return void
     */
    public function addIgnored(string $class): void
    {
        $this->ignore[] = $class;
    }

    /**
     * @param Exception|RouteNotFoundException|ContainerNotFoundException $e
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws RouteNotFoundException
     */
    public function __invoke(Exception|RouteNotFoundException|ContainerNotFoundException $e, RequestInterface $request): ResponseInterface
    {
        if (in_array(get_class($e), $this->ignore)) {
            throw $e;
        }

        return new JsonResponse([
            'message' => $e->getMessage(),
            'trace' => explode(PHP_EOL, $e->getTraceAsString())
        ], (method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500));
    }
}