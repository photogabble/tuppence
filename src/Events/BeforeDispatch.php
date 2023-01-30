<?php

namespace Photogabble\Tuppence\Events;

use League\Event\HasEventName;
use Psr\Http\Message\RequestInterface;

class BeforeDispatch implements HasEventName
{
    public RequestInterface $request;

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    public function eventName(): string
    {
        return 'before.dispatch';
    }
}