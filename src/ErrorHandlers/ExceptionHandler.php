<?php

namespace Photogabble\Tuppence\ErrorHandlers;

use Psr\Http\Message\RequestInterface;

interface ExceptionHandler
{
    /**
     * @param \Exception $e
     * @param RequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(\Exception $e, RequestInterface $request);
}

