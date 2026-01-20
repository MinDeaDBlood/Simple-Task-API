<?php

declare(strict_types=1);

namespace App;

class Router
{
    private array $routes = [];

    public function add(string $method, string $pattern, callable $handler): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }

    public function dispatch(Request $request): Response
    {
        $method = $request->getMethod();
        $path = $request->getPath();
        $allowedMethodsForPath = [];

        foreach ($this->routes as $route) {
            // Сначала проверяем совпадение path с pattern
            if (!preg_match($route['pattern'], $path, $matches)) {
                continue;
            }

            // Путь совпал — запоминаем, какие методы тут разрешены
            $allowedMethodsForPath[] = $route['method'];

            // Если метод совпал — вызываем handler
            if ($route['method'] === $method) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                return call_user_func($route['handler'], $request, $params);
            }
        }

        // Путь найден, но метод не разрешён → 405 + Allow
        if ($allowedMethodsForPath !== []) {
            $allowedMethodsForPath = array_values(array_unique($allowedMethodsForPath));
            sort($allowedMethodsForPath);
            
            return new Response(
                405,
                [
                    'Content-Type' => 'application/json; charset=utf-8',
                    'Allow' => implode(', ', $allowedMethodsForPath),
                ],
                ['error' => 'Method Not Allowed']
            );
        }

        // Вообще ничего не совпало → 404
        return Response::json(['error' => 'Not Found'], 404);
    }
}
