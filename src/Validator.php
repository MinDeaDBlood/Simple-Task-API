<?php

declare(strict_types=1);

namespace App;

final class Validator
{
    public static function validateCreate(mixed $data): array
    {
        if (!is_array($data)) {
            return ['ok' => false, 'errors' => ['Invalid JSON body']];
        }

        $errors = [];

        // title
        if (!isset($data['title']) || !is_string($data['title']) || trim($data['title']) === '') {
            $errors[] = 'Field "title" is required and must be a non-empty string';
        } elseif (strlen(trim($data['title'])) > 255) {
            $errors[] = 'Field "title" must be <= 255 characters';
        }

        // description: string|null (can be missing)
        if (array_key_exists('description', $data)) {
            if (!is_null($data['description']) && !is_string($data['description'])) {
                $errors[] = 'Field "description" must be a string or null';
            }
        }

        // status: optional
        if (array_key_exists('status', $data)) {
            if (!is_string($data['status']) || !TaskStatus::isValid($data['status'])) {
                $errors[] = 'Field "status" must be one of: ' . implode(', ', TaskStatus::all());
            }
        }

        if ($errors) {
            return ['ok' => false, 'errors' => $errors];
        }

        $desc = null;
        if (array_key_exists('description', $data)) {
            $desc = is_string($data['description']) ? trim($data['description']) : null;
            if ($desc === '') {
                $desc = null;
            }
        }

        return [
            'ok' => true,
            'data' => [
                'title' => trim($data['title']),
                'description' => $desc,
                'status' => $data['status'] ?? TaskStatus::PENDING,
            ],
        ];
    }

    public static function validatePatch(mixed $data): array
    {
        if (!is_array($data)) {
            return ['ok' => false, 'errors' => ['Invalid JSON body']];
        }

        if ($data === []) {
            return ['ok' => false, 'errors' => ['At least one field must be provided']];
        }

        $errors = [];
        $validated = [];

        // title (optional)
        if (array_key_exists('title', $data)) {
            if (!is_string($data['title']) || trim($data['title']) === '') {
                $errors[] = 'Field "title" must be a non-empty string';
            } elseif (strlen(trim($data['title'])) > 255) {
                $errors[] = 'Field "title" must be <= 255 characters';
            } else {
                $validated['title'] = trim($data['title']);
            }
        }

        // description (optional, can be null)
        if (array_key_exists('description', $data)) {
            if (!is_null($data['description']) && !is_string($data['description'])) {
                $errors[] = 'Field "description" must be a string or null';
            } else {
                $desc = is_string($data['description']) ? trim($data['description']) : null;
                if ($desc === '') {
                    $desc = null;
                }
                $validated['description'] = $desc;
            }
        }

        // status (optional)
        if (array_key_exists('status', $data)) {
            if (!is_string($data['status']) || !TaskStatus::isValid($data['status'])) {
                $errors[] = 'Field "status" must be one of: ' . implode(', ', TaskStatus::all());
            } else {
                $validated['status'] = $data['status'];
            }
        }

        if ($errors) {
            return ['ok' => false, 'errors' => $errors];
        }

        if ($validated === []) {
            return ['ok' => false, 'errors' => ['No valid fields to update']];
        }

        return ['ok' => true, 'data' => $validated];
    }

    public static function validatePut(mixed $data): array
    {
        if (!is_array($data)) {
            return ['ok' => false, 'errors' => ['Invalid JSON body']];
        }

        $errors = [];

        // title required
        if (!isset($data['title']) || !is_string($data['title']) || trim($data['title']) === '') {
            $errors[] = 'Field "title" is required and must be a non-empty string';
        } elseif (strlen(trim($data['title'])) > 255) {
            $errors[] = 'Field "title" must be <= 255 characters';
        }

        // description required as a key for PUT contract, but value may be null
        if (!array_key_exists('description', $data)) {
            $errors[] = 'Field "description" is required for PUT (can be null)';
        } elseif (!is_null($data['description']) && !is_string($data['description'])) {
            $errors[] = 'Field "description" must be a string or null';
        }

        // status required
        if (!isset($data['status']) || !is_string($data['status']) || !TaskStatus::isValid($data['status'])) {
            $errors[] = 'Field "status" is required and must be one of: ' . implode(', ', TaskStatus::all());
        }

        if ($errors) {
            return ['ok' => false, 'errors' => $errors];
        }

        $desc = is_string($data['description']) ? trim($data['description']) : null;
        if ($desc === '') {
            $desc = null;
        }

        return [
            'ok' => true,
            'data' => [
                'title' => trim($data['title']),
                'description' => $desc,
                'status' => $data['status'],
            ],
        ];
    }

    public static function validateListQuery(array $query): array
    {
        $errors = [];
        $validated = [];

        // defaults (no limit by default - returns all tasks)
        $validated['page'] = 1;
        $validated['sort'] = 'created_at:desc';

        if (isset($query['status'])) {
            if (!TaskStatus::isValid((string)$query['status'])) {
                $errors[] = 'Query parameter "status" must be one of: ' . implode(', ', TaskStatus::all());
            } else {
                $validated['status'] = (string)$query['status'];
            }
        }

        if (isset($query['search'])) {
            $validated['search'] = trim((string)$query['search']);
        }

        if (isset($query['sort'])) {
            $sort = (string)$query['sort'];

            $allowedFields = ['id', 'title', 'status', 'created_at', 'updated_at'];
            $allowedDirections = ['ASC', 'DESC'];

            $parts = explode(':', $sort);
            $field = $parts[0] ?? '';
            $dir = strtoupper($parts[1] ?? '');

            if ($field === '' || $dir === '' || !in_array($field, $allowedFields, true) || !in_array($dir, $allowedDirections, true)) {
                $errors[] = 'Query parameter "sort" must be like "created_at:desc" and allowed fields are: ' . implode(', ', $allowedFields);
            } else {
                $validated['sort'] = $field . ':' . strtolower($dir);
            }
        }

        if (isset($query['page'])) {
            $page = (string)$query['page'];
            if (!ctype_digit($page) || (int)$page < 1) {
                $errors[] = 'Query parameter "page" must be a positive integer';
            } else {
                $validated['page'] = (int)$page;
            }
        }

        if (isset($query['limit'])) {
            $limit = (string)$query['limit'];
            if (!ctype_digit($limit) || (int)$limit < 1 || (int)$limit > 100) {
                $errors[] = 'Query parameter "limit" must be between 1 and 100';
            } else {
                $validated['limit'] = (int)$limit;
            }
        }

        if ($errors) {
            return ['ok' => false, 'errors' => $errors];
        }

        return ['ok' => true, 'data' => $validated];
    }
}
