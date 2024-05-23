<?php 

// The Response class will be responsible for sending the response to the client
// It will be used in the frontdoor controller to send the response to the client

namespace RWFramework\Framework\Http;

class Response {
    public function __construct(
        private string $content = '',
        private int $status = 200,
        private array $headers = []
    )
    {
        // Must be set before sending content

        // So the best to create on instantiation like here
        http_response_code($this->status);
    }

    public function send(): void {
        echo $this->content;
    }
}