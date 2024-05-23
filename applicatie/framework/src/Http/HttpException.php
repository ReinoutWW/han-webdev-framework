<?php

namespace RWFramework\Framework\Http;

// HttpException is also responsible for carrying the status code.
class HttpException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, \Throwable $previous = null)
    {
        $message = 'RWFramework 1.0.0 | ' . '' . $message;
        parent::__construct($message, $code, $previous);
    }

    private int $statusCode = 400;

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }
}