<?php 

// The Response class will be responsible for sending the response to the client
// It will be used in the frontdoor controller to send the response to the client

namespace RWFramework\Framework\Http;

class Response {
    public const HTTP_INTERNAL_SERVER_ERROR = 500;

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
        // Start output buffering
        ob_start();

        // Send headers
        foreach ($this->headers as $key => $value) {
            header($key . ': ' . $value);
        }

        // This will actually add the content to the output buffer
        echo $this->content;

        // Flush the bugger, send the content
        ob_end_flush();
    }

    public function setContent(?string $content): void {
        $this->content = $content;
    }

    public function getStatus(): int {
        return $this->status;
    }

    public function setStatus(int $status): void {
        $this->status = $status;
    }

    public function getHeader(string $name): mixed {
        return $this->headers[$name] ?? null;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeader($key, $value): void
    {
        $this->headers[$key] = $value;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }
}