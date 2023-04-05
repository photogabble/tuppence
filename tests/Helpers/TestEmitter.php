<?php

namespace Photogabble\Tuppence\Tests\Helpers;

use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use Psr\Http\Message\ResponseInterface;

final class TestEmitter implements EmitterInterface
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
