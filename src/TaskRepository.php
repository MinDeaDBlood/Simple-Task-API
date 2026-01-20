<?php

namespace App;

use PDO;

class TaskRepository
{
    private PDO $pdo;

    public function __construct(Database $database)
    {
        $this->pdo = $database->getPdo();
    }

    public function create(array $data): array
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO tasks (title, description, status) VALUES (:title, :description, :status)'
        );

        $stmt->execute([
            'title' => $data['title'],
            'description' => $data['description'],
            'status' => $data['status'],
        ]);

        $id = $this->pdo->lastInsertId();
        return $this->find((int)$id);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tasks WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? $this->map($row) : null;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM tasks WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }

    public function patch(int $id, array $data): ?array
    {
        $fields = [];
        $params = ['id' => $id];

        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $params[$key] = $value;
        }

        $fields[] = 'updated_at = datetime(\'now\')';

        $sql = 'UPDATE tasks SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->rowCount() > 0 ? $this->find($id) : null;
    }

    public function put(int $id, array $data): ?array
    {
        $stmt = $this->pdo->prepare(
            'UPDATE tasks SET title = :title, description = :description, status = :status, updated_at = datetime(\'now\') WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'description' => $data['description'],
            'status' => $data['status'],
        ]);

        return $stmt->rowCount() > 0 ? $this->find($id) : null;
    }

    public function list(array $filters = []): array
    {
        $page = $filters['page'] ?? 1;
        $limit = $filters['limit'] ?? 10;
        $offset = ($page - 1) * $limit;

        $where = [];
        $params = [];

        if (isset($filters['status'])) {
            $where[] = 'status = :status';
            $params['status'] = $filters['status'];
        }

        if (isset($filters['search']) && $filters['search'] !== '') {
            $where[] = '(title LIKE :search OR description LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $countStmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM tasks $whereClause");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetch()['total'];

        $orderBy = $this->parseSort($filters['sort'] ?? 'created_at:desc');

        $sql = "SELECT * FROM tasks $whereClause ORDER BY $orderBy LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        $rows = $stmt->fetchAll();

        $tasks = array_map([$this, 'map'], $rows);

        $totalPages = (int)ceil($total / $limit);

        $links = [
            'self' => $this->buildListLink($filters, $page),
            'first' => $this->buildListLink($filters, 1),
            'last' => $this->buildListLink($filters, max(1, $totalPages)),
        ];

        if ($page > 1) {
            $links['prev'] = $this->buildListLink($filters, $page - 1);
        }

        if ($page < $totalPages) {
            $links['next'] = $this->buildListLink($filters, $page + 1);
        }

        return [
            'data' => $tasks,
            'meta' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'total_pages' => $totalPages,
            ],
            'links' => $links,
        ];
    }

    private function map(array $row): array
    {
        return [
            'id' => (int)$row['id'],
            'title' => $row['title'],
            'description' => $row['description'],
            'status' => $row['status'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
        ];
    }

    private function parseSort(string $sort): string
    {
        $parts = explode(':', $sort);
        $field = $parts[0] ?? 'created_at';
        $direction = strtoupper($parts[1] ?? 'DESC');

        $allowedFields = ['id', 'title', 'status', 'created_at', 'updated_at'];
        $allowedDirections = ['ASC', 'DESC'];

        if (!in_array($field, $allowedFields)) {
            $field = 'created_at';
        }

        if (!in_array($direction, $allowedDirections)) {
            $direction = 'DESC';
        }

        return "$field $direction";
    }

    private function buildListLink(array $filters, int $page): string
    {
        $query = array_merge($filters, ['page' => $page]);
        unset($query['data']);
        return '/tasks?' . http_build_query($query);
    }
}
