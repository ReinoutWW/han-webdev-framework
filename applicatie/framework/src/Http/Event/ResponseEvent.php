<?php

namespace RWFramework\Framework\Http\Event;

use RWFramework\Framework\EventDispatcher\Event;
use RWFramework\Framework\Http\Request;
use RWFramework\Framework\Http\Response;

class ResponseEvent extends Event
{
    public function __construct(
        private Request $request,
        private Response $response
    ) {
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }
}