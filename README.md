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
- PDO SQLite extension (usually included by default)
- Built-in PHP development server or Apache/Nginx

**Note**: No additional PHP extensions required. The project uses standard PHP functions only.

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

### Automated Testing

Run the comprehensive test suite (PowerShell):

```powershell
.\test-api.ps1
```

This will test all endpoints including:
- CRUD operations
- Validation (400, 422 status codes)
- Null value handling
- Query parameters
- Error responses

## Environment Variables

The API supports configuration via environment variables:

- `DB_PATH` - Path to SQLite database file (default: `var/database.sqlite`)

### Using .env file

Copy `.env.example` to `.env` and customize the values:

```bash
cp .env.example .env
```

The application will automatically load variables from `.env` file if it exists.

### Manual environment variables

Alternatively, set environment variables directly:

```bash
# Windows (PowerShell)
$env:DB_PATH = "C:\path\to\database.sqlite"

# Linux/Mac
export DB_PATH=/path/to/database.sqlite
```

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

### Required by Specification

The following endpoints fulfill the original requirements:

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/tasks` | Create new task (title, description, status) |
| GET | `/tasks` | Returns all tasks |
| GET | `/tasks/{id}` | View single task |
| PUT | `/tasks/{id}` | Update task |
| DELETE | `/tasks/{id}` | Delete task |

**Additional Features** (not required by specification): PATCH for partial updates, filtering (`?status=`), search (`?search=`), sorting (`?sort=`), optional pagination (`?limit=`).

---

### List Tasks

```
GET /tasks
```

**Returns all tasks.** Pagination is enabled when `limit` parameter is provided.

Query parameters:
- `status` - Filter by status (pending, in_progress, done)
- `search` - Search in title and description
- `sort` - Sort field and direction (e.g., `created_at:desc`, `title:asc`)
- `limit` - Items per page (1-100). If not specified, returns all tasks
- `page` - Page number (default: 1, only used when `limit` is specified)

Response without pagination:

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
    "total": 1
  }
}
```

Response with pagination (`?limit=10`):

```json
{
  "data": [...],
  "meta": {
    "total": 25,
    "page": 1,
    "limit": 10,
    "total_pages": 3
  },
  "links": {
    "self": "/tasks?page=1&limit=10",
    "first": "/tasks?page=1&limit=10",
    "last": "/tasks?page=3&limit=10",
    "next": "/tasks?page=2&limit=10"
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
| 400 | Bad Request - Malformed JSON |
| 404 | Not Found - Resource not found |
| 405 | Method Not Allowed - HTTP method not supported for this endpoint |
| 415 | Unsupported Media Type - Content-Type must be application/json |
| 422 | Unprocessable Entity - Validation errors |
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
├── .env.example                # Environment variables example
├── .gitignore                  # Git exclusions
├── LICENSE                     # MIT License
├── test-api.ps1                # Automated test suite
└── README.md
```

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
