<?php

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
            return Response::json(['errors' => $validation['errors']], 400);
        }

        $result = $this->repository->list($validation['data']);
        return Response::json($result, 200);
    }

    public function store(Request $request): Response
    {
        if (!$this->isJson($request)) {
            return $this->badJson();
        }

        $validation = Validator::validateCreate($request->getBody());

        if (!$validation['ok']) {
            return Response::json(['errors' => $validation['errors']], 400);
        }

        $task = $this->repository->create($validation['data']);
        return Response::json($task, 201);
    }

    public function show(Request $request, array $params): Response
    {
        $id = (int)$params['id'];
        $task = $this->repository->find($id);

        if (!$task) {
            return Response::json(['error' => 'Task not found'], 404);
        }

        return Response::json($task, 200);
    }

    public function patch(Request $request, array $params): Response
    {
        if (!$this->isJson($request)) {
            return $this->badJson();
        }

        $id = (int)$params['id'];
        $task = $this->repository->find($id);

        if (!$task) {
            return Response::json(['error' => 'Task not found'], 404);
        }

        $validation = Validator::validatePatch($request->getBody());

        if (!$validation['ok']) {
            return Response::json(['errors' => $validation['errors']], 400);
        }

        $updated = $this->repository->patch($id, $validation['data']);
        return Response::json($updated, 200);
    }

    public function put(Request $request, array $params): Response
    {
        if (!$this->isJson($request)) {
            return $this->badJson();
        }

        $id = (int)$params['id'];
        $task = $this->repository->find($id);

        if (!$task) {
            return Response::json(['error' => 'Task not found'], 404);
        }

        $validation = Validator::validatePut($request->getBody());

        if (!$validation['ok']) {
            return Response::json(['errors' => $validation['errors']], 400);
        }

        $updated = $this->repository->put($id, $validation['data']);
        return Response::json($updated, 200);
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
        $contentType = $headers['content-type'] ?? '';
        return str_contains($contentType, 'application/json');
    }

    private function badJson(): Response
    {
        return Response::json(['error' => 'Content-Type must be application/json'], 415);
    }
}
