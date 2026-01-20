<?php

declare(strict_types=1);

namespace App;

class Request
{
    private string $method;
    private string $path;
    private array $query;
    private array $headers;
    private mixed $body;

    public function __construct(string $method, string $path, array $query = [], array $headers = [], mixed $body = null)
    {
        $this->method = strtoupper($method);
        $this->path = $path;
        $this->query = $query;
        $this->headers = $headers;
        $this->body = $body;
    }

    public static function fromGlobals(): self
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $path = rtrim($path, '/');
        $path = $path === '' ? '/' : $path;
        $query = $_GET;

        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $headerName = str_replace('_', '-', substr($key, 5));
                $headers[strtolower($headerName)] = $value;
            }
        }

        // Content-Type и Content-Length не имеют префикса HTTP_
        if (isset($_SERVER['CONTENT_TYPE'])) {
            $headers['content-type'] = $_SERVER['CONTENT_TYPE'];
        }
        if (isset($_SERVER['CONTENT_LENGTH'])) {
            $headers['content-length'] = $_SERVER['CONTENT_LENGTH'];
        }

        $body = null;
        $contentType = $headers['content-type'] ?? '';
        if (str_contains(strtolower($contentType), 'application/json')) {
            $rawBody = file_get_contents('php://input') ?: '';
            
            if ($rawBody !== '') {
                $body = json_decode($rawBody, true);
                
                // Проверка на битый JSON
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $body = ['__invalid_json' => true, '__json_error' => json_last_error_msg()];
                }
            }
        }

        return new self($method, $path, $query, $headers, $body);
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getQuery(): array
    {
        return $this->query;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): mixed
    {
        return $this->body;
    }
}
