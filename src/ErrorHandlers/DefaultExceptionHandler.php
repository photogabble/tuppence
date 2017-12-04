<?php

namespace Photogabble\Tuppence\ErrorHandlers;

use Exception;
use League\Route\Http\Exception\NotFoundException as RouteNotFoundException;
use League\Container\Exception\NotFoundException as ContainerNotFoundException;
use Psr\Http\Message\RequestInterface;
use Zend\Diactoros\Response\JsonResponse;

class DefaultExceptionHandler implements ExceptionHandler
{

    /**
     * Exceptions this handler should ignore and pass through.
     *
     * @var array
     */
    protected $ignore = [];

    /**
     * @param string $class
     */
    public function addIgnored($class) {
        array_push($this->ignore, $class);
    }

    /**
     * @param Exception|RouteNotFoundException|ContainerNotFoundException $e
     * @param RequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws Exception
     */
    public function __invoke(Exception $e, RequestInterface $request)
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