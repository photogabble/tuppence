<?php

namespace Photogabble\Tuppence\ErrorHandlers;

use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface ExceptionHandler
{
    /**
     * @param Exception $e
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function __invoke(Exception $e, RequestInterface $request): ResponseInterface;
}

