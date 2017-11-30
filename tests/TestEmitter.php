<?php

namespace Tests;

use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\EmitterInterface;

class TestEmitter implements EmitterInterface
{
    /**
     * @var ResponseInterface
     */
    private $response;

    public function emit(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }
}
