<?php

namespace App;

class Validator
{
    public static function validateCreate(mixed $data): array
    {
        if (!is_array($data)) {
            return ['ok' => false, 'errors' => ['Invalid JSON body']];
        }

        $errors = [];

        if (!isset($data['title']) || !is_string($data['title']) || trim($data['title']) === '') {
            $errors[] = 'Field "title" is required and must be a non-empty string';
        }

        if (isset($data['description']) && !is_string($data['description'])) {
            $errors[] = 'Field "description" must be a string';
        }

        if (isset($data['status'])) {
            if (!is_string($data['status']) || !TaskStatus::isValid($data['status'])) {
                $errors[] = 'Field "status" must be one of: ' . implode(', ', TaskStatus::all());
            }
        }

        if (!empty($errors)) {
            return ['ok' => false, 'errors' => $errors];
        }

        return [
            'ok' => true,
            'data' => [
                'title' => trim($data['title']),
                'description' => isset($data['description']) ? trim($data['description']) : null,
                'status' => $data['status'] ?? TaskStatus::PENDING,
            ],
        ];
    }

    public static function validatePatch(mixed $data): array
    {
        if (!is_array($data)) {
            return ['ok' => false, 'errors' => ['Invalid JSON body']];
        }

        if (empty($data)) {
            return ['ok' => false, 'errors' => ['At least one field must be provided']];
        }

        $errors = [];
        $validated = [];

        if (isset($data['title'])) {
            if (!is_string($data['title']) || trim($data['title']) === '') {
                $errors[] = 'Field "title" must be a non-empty string';
            } else {
                $validated['title'] = trim($data['title']);
            }
        }

        if (isset($data['description'])) {
            if (!is_string($data['description'])) {
                $errors[] = 'Field "description" must be a string';
            } else {
                $validated['description'] = trim($data['description']);
            }
        }

        if (isset($data['status'])) {
            if (!is_string($data['status']) || !TaskStatus::isValid($data['status'])) {
                $errors[] = 'Field "status" must be one of: ' . implode(', ', TaskStatus::all());
            } else {
                $validated['status'] = $data['status'];
            }
        }

        if (!empty($errors)) {
            return ['ok' => false, 'errors' => $errors];
        }

        return ['ok' => true, 'data' => $validated];
    }

    public static function validatePut(mixed $data): array
    {
        if (!is_array($data)) {
            return ['ok' => false, 'errors' => ['Invalid JSON body']];
        }

        $errors = [];

        if (!isset($data['title']) || !is_string($data['title']) || trim($data['title']) === '') {
            $errors[] = 'Field "title" is required and must be a non-empty string';
        }

        if (!isset($data['description']) || !is_string($data['description'])) {
            $errors[] = 'Field "description" is required and must be a string';
        }

        if (!isset($data['status']) || !is_string($data['status']) || !TaskStatus::isValid($data['status'])) {
            $errors[] = 'Field "status" is required and must be one of: ' . implode(', ', TaskStatus::all());
        }

        if (!empty($errors)) {
            return ['ok' => false, 'errors' => $errors];
        }

        return [
            'ok' => true,
            'data' => [
                'title' => trim($data['title']),
                'description' => trim($data['description']),
                'status' => $data['status'],
            ],
        ];
    }

    public static function validateListQuery(array $query): array
    {
        $errors = [];
        $validated = [];

        if (isset($query['status'])) {
            if (!TaskStatus::isValid($query['status'])) {
                $errors[] = 'Query parameter "status" must be one of: ' . implode(', ', TaskStatus::all());
            } else {
                $validated['status'] = $query['status'];
            }
        }

        if (isset($query['search'])) {
            $validated['search'] = trim($query['search']);
        }

        if (isset($query['sort'])) {
            $validated['sort'] = $query['sort'];
        }

        if (isset($query['page'])) {
            if (!ctype_digit($query['page']) || (int)$query['page'] < 1) {
                $errors[] = 'Query parameter "page" must be a positive integer';
            } else {
                $validated['page'] = (int)$query['page'];
            }
        }

        if (isset($query['limit'])) {
            if (!ctype_digit($query['limit']) || (int)$query['limit'] < 1 || (int)$query['limit'] > 100) {
                $errors[] = 'Query parameter "limit" must be between 1 and 100';
            } else {
                $validated['limit'] = (int)$query['limit'];
            }
        }

        if (!empty($errors)) {
            return ['ok' => false, 'errors' => $errors];
        }

        return ['ok' => true, 'data' => $validated];
    }
}
