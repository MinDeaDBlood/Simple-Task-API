# Simple Task API

A lightweight REST API for task management built with pure PHP (no frameworks). Features clean architecture with router, controller, repository pattern, and SQLite database.

## Features

- RESTful API endpoints for CRUD operations
- Task filtering by status
- Full-text search in title and description
- Sorting and pagination
- Input validation
- JSON responses with proper HTTP status codes
- SQLite database with PDO
- PSR-4 autoloading
- Clean architecture (Router → Controller → Repository → Database)

## Requirements

- PHP 8.1 or higher
- PDO SQLite extension
- Built-in PHP development server or Apache/Nginx

## Quick Start

1. Clone the repository
2. Run database migration:

```bash
php scripts/migrate.php
```

3. Start the development server:

```bash
php -S localhost:8000 -t public
```

4. Test the API:

```bash
curl http://localhost:8000/tasks
```

## Environment Variables

- `DB_PATH` - Path to SQLite database file (default: `var/database.sqlite`)

## Task Model

| Field | Type | Description |
|-------|------|-------------|
| id | integer | Auto-increment primary key |
| title | string | Task title (required) |
| description | string | Task description (optional) |
| status | string | One of: `pending`, `in_progress`, `done` |
| created_at | datetime | Creation timestamp |
| updated_at | datetime | Last update timestamp |

## API Endpoints

### List Tasks

```
GET /tasks
```

Query parameters:
- `status` - Filter by status (pending, in_progress, done)
- `search` - Search in title and description
- `sort` - Sort field and direction (e.g., `created_at:desc`, `title:asc`)
- `page` - Page number (default: 1)
- `limit` - Items per page (default: 10, max: 100)

Response:

```json
{
  "data": [
    {
      "id": 1,
      "title": "Task title",
      "description": "Task description",
      "status": "pending",
      "created_at": "2026-01-20 10:00:00",
      "updated_at": "2026-01-20 10:00:00"
    }
  ],
  "meta": {
    "total": 1,
    "page": 1,
    "limit": 10,
    "total_pages": 1
  },
  "links": {
    "self": "/tasks?page=1",
    "first": "/tasks?page=1",
    "last": "/tasks?page=1"
  }
}
```

### Create Task

```
POST /tasks
Content-Type: application/json
```

Request body:

```json
{
  "title": "New task",
  "description": "Task description",
  "status": "pending"
}
```

Response: `201 Created`

### Get Single Task

```
GET /tasks/{id}
```

Response: `200 OK` or `404 Not Found`

### Update Task (Partial)

```
PATCH /tasks/{id}
Content-Type: application/json
```

Request body (all fields optional):

```json
{
  "title": "Updated title",
  "status": "in_progress"
}
```

Response: `200 OK` or `404 Not Found`

### Update Task (Full)

```
PUT /tasks/{id}
Content-Type: application/json
```

Request body (all fields required):

```json
{
  "title": "Updated title",
  "description": "Updated description",
  "status": "done"
}
```

Response: `200 OK` or `404 Not Found`

### Delete Task

```
DELETE /tasks/{id}
```

Response: `204 No Content` or `404 Not Found`

## Example Requests

Create a task:

```bash
curl -X POST http://localhost:8000/tasks \
  -H "Content-Type: application/json" \
  -d '{"title":"Buy groceries","description":"Milk, eggs, bread","status":"pending"}'
```

List all tasks:

```bash
curl http://localhost:8000/tasks
```

Filter by status:

```bash
curl "http://localhost:8000/tasks?status=pending"
```

Search tasks:

```bash
curl "http://localhost:8000/tasks?search=groceries"
```

Update task status:

```bash
curl -X PATCH http://localhost:8000/tasks/1 \
  -H "Content-Type: application/json" \
  -d '{"status":"done"}'
```

Delete task:

```bash
curl -X DELETE http://localhost:8000/tasks/1
```

## HTTP Status Codes

| Code | Description |
|------|-------------|
| 200 | OK - Request successful |
| 201 | Created - Resource created successfully |
| 204 | No Content - Resource deleted successfully |
| 400 | Bad Request - Validation error |
| 404 | Not Found - Resource not found |
| 415 | Unsupported Media Type - Content-Type must be application/json |
| 500 | Internal Server Error - Server error |

## Project Structure

```
.
├── config/
│   └── config.php              # Database configuration
├── migrations/
│   └── 001_create_tasks.sql    # Database schema
├── public/
│   └── index.php               # Application entry point
├── scripts/
│   └── migrate.php             # Migration script
├── src/
│   ├── bootstrap.php           # PSR-4 autoloader
│   ├── Database.php            # PDO connection
│   ├── Request.php             # HTTP request parser
│   ├── Response.php            # HTTP response builder
│   ├── Router.php              # Route dispatcher
│   ├── TaskController.php      # Request handlers
│   ├── TaskRepository.php      # Database operations
│   ├── TaskStatus.php          # Status constants
│   └── Validator.php           # Input validation
├── var/
│   └── .gitkeep                # Database directory
└── README.md
```
