<?php

declare(strict_types=1);

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
        return new self($statusCode, ['Content-Type' => 'application/json; charset=utf-8'], $data);
    }

    public function emit(): void
    {
        http_response_code($this->statusCode);

        // Отправляем заголовки всегда (для CORS, кэша и т.д.)
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        // Для 204 No Content не отправляем тело
        if ($this->statusCode === 204) {
            return;
        }

        if ($this->body !== null) {
            $contentType = $this->headers['Content-Type'] ?? '';
            if (str_contains(strtolower($contentType), 'application/json')) {
                echo json_encode($this->body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } else {
                echo $this->body;
            }
        }
    }
}
