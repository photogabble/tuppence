<?php

namespace Photogabble\Tuppence\Events;

use League\Event\HasEventName;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class AfterDispatch implements HasEventName
{
    public RequestInterface $request;
    public ResponseInterface $response;

    public function __construct(RequestInterface $request, ResponseInterface $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function eventName(): string
    {
        return 'after.dispatch';
    }
}