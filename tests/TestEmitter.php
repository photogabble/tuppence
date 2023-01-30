<?php

namespace Photogabble\Tuppence\Tests;

use Psr\Http\Message\ResponseInterface;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;

class TestEmitter implements EmitterInterface
{
    private ResponseInterface $response;

    public function emit(ResponseInterface $response): bool
    {
        $this->response = $response;
        return true;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
