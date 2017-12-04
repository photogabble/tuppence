<?php

namespace Photogabble\Tuppence\ErrorHandlers;

interface ExceptionHandler
{
    /**
     * @param \Exception $e
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(\Exception $e);
}

