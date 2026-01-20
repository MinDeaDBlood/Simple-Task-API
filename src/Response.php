<?php

namespace App;

class Response
{
    private int $statusCode;
    private array $headers;
    private mixed $body;

    public function __construct(int $statusCode = 200, array $headers = [], mixed $body = null)
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->body = $body;
    }

    public static function json(mixed $data, int $statusCode = 200): self
    {
        return new self($statusCode, ['Content-Type' => 'application/json'], $data);
    }

    public function emit(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        if ($this->body !== null) {
            if (isset($this->headers['Content-Type']) && $this->headers['Content-Type'] === 'application/json') {
                echo json_encode($this->body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } else {
                echo $this->body;
            }
        }
    }
}
