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
        
    }

    public function send(): void {
        echo $this->content;
    }
}