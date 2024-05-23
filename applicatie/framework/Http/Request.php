<?php 
namespace RWFramework\Framework\Http;

class Request {
    public function __construct(
        // $_GET, $_POST, $_COOKIE, $_FILES, $_SERVER,
        public readonly array $getParams, 
        public readonly array $postParams,
        public readonly array $cookies,
        public readonly array $files,
        public readonly array $server
    )
    {
        
    }

    public static function createFromGlobals(): static {
        return new static(
            $_GET, $_POST, $_COOKIE, $_FILES, $_SERVER
        );
    }

    // Will ignore query parameters and return the path info
    public function getPathInfo(): string {
        return strtok($this->server['REQUEST_URI'], '?');
    }

    public function getMethod(): string {
        return $this->server['REQUEST_METHOD'];
    }
}