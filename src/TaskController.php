<?php

declare(strict_types=1);

namespace App;

class TaskController
{
    private TaskRepository $repository;

    public function __construct(TaskRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request): Response
    {
        $validation = Validator::validateListQuery($request->getQuery());

        if (!$validation['ok']) {
            return Response::json(['errors' => $validation['errors']], 422);
        }

        $result = $this->repository->list($validation['data']);
        return Response::json($result, 200);
    }

    public function store(Request $request): Response
    {
        if (!$this->isJson($request)) {
            return $this->badJson();
        }

        $body = $request->getBody();
        
        // Проверка на битый JSON
        if (is_array($body) && ($body['__invalid_json'] ?? false)) {
            return Response::json(['error' => 'Invalid JSON: ' . ($body['__json_error'] ?? 'Malformed JSON')], 400);
        }

        $validation = Validator::validateCreate($body);

        if (!$validation['ok']) {
            return Response::json(['errors' => $validation['errors']], 422);
        }

        $task = $this->repository->create($validation['data']);
        return Response::json(['data' => $task], 201);
    }

    public function show(Request $request, array $params): Response
    {
        $id = (int)$params['id'];
        $task = $this->repository->find($id);

        if (!$task) {
            return Response::json(['error' => 'Task not found'], 404);
        }

        return Response::json(['data' => $task], 200);
    }

    public function patch(Request $request, array $params): Response
    {
        if (!$this->isJson($request)) {
            return $this->badJson();
        }

        $body = $request->getBody();
        
        // Проверка на битый JSON
        if (is_array($body) && ($body['__invalid_json'] ?? false)) {
            return Response::json(['error' => 'Invalid JSON: ' . ($body['__json_error'] ?? 'Malformed JSON')], 400);
        }

        $id = (int)$params['id'];
        $task = $this->repository->find($id);

        if (!$task) {
            return Response::json(['error' => 'Task not found'], 404);
        }

        $validation = Validator::validatePatch($body);

        if (!$validation['ok']) {
            return Response::json(['errors' => $validation['errors']], 422);
        }

        $updated = $this->repository->patch($id, $validation['data']);
        return Response::json(['data' => $updated], 200);
    }

    public function put(Request $request, array $params): Response
    {
        if (!$this->isJson($request)) {
            return $this->badJson();
        }

        $body = $request->getBody();
        
        // Проверка на битый JSON
        if (is_array($body) && ($body['__invalid_json'] ?? false)) {
            return Response::json(['error' => 'Invalid JSON: ' . ($body['__json_error'] ?? 'Malformed JSON')], 400);
        }

        $id = (int)$params['id'];
        $task = $this->repository->find($id);

        if (!$task) {
            return Response::json(['error' => 'Task not found'], 404);
        }

        $validation = Validator::validatePut($body);

        if (!$validation['ok']) {
            return Response::json(['errors' => $validation['errors']], 422);
        }

        $updated = $this->repository->put($id, $validation['data']);
        return Response::json(['data' => $updated], 200);
    }

    public function destroy(Request $request, array $params): Response
    {
        $id = (int)$params['id'];
        $deleted = $this->repository->delete($id);

        if (!$deleted) {
            return Response::json(['error' => 'Task not found'], 404);
        }

        return Response::json(null, 204);
    }

    private function isJson(Request $request): bool
    {
        $headers = $request->getHeaders();
        $contentType = strtolower($headers['content-type'] ?? '');
        return str_contains($contentType, 'application/json');
    }

    private function badJson(): Response
    {
        return Response::json(['error' => 'Content-Type must be application/json'], 415);
    }
}
