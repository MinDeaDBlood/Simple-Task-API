<?php

require_once __DIR__ . '/../src/bootstrap.php';

use App\Database;
use App\Request;
use App\Response;
use App\Router;
use App\TaskController;
use App\TaskRepository;

try {
    $config = require __DIR__ . '/../config/config.php';

    $database = new Database($config);
    $repository = new TaskRepository($database);
    $controller = new TaskController($repository);

    $request = Request::fromGlobals();
    $router = new Router();

    $router->add('GET', '#^/tasks$#', [$controller, 'index']);
    $router->add('POST', '#^/tasks$#', [$controller, 'store']);
    $router->add('GET', '#^/tasks/(?<id>\d+)$#', [$controller, 'show']);
    $router->add('PATCH', '#^/tasks/(?<id>\d+)$#', [$controller, 'patch']);
    $router->add('PUT', '#^/tasks/(?<id>\d+)$#', [$controller, 'put']);
    $router->add('DELETE', '#^/tasks/(?<id>\d+)$#', [$controller, 'destroy']);

    $response = $router->dispatch($request);
    $response->emit();

} catch (\Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Internal Server Error', 'message' => $e->getMessage()]);
}
