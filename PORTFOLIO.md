# Simple Task API - Portfolio Project

## 🎯 Project Overview

A production-ready REST API for task management built with **pure PHP** (no frameworks), demonstrating clean architecture, best practices, and professional code quality.

## ✨ Key Features

### Architecture
- **Clean Architecture**: Router → Controller → Repository → Database
- **PSR-4 Autoloading**: Custom autoloader without Composer
- **Environment Configuration**: Built-in .env file loader (no dependencies)
- **Separation of Concerns**: Each component has a single responsibility
- **Dependency Injection**: Components receive dependencies via constructor

### REST API Best Practices
- **Proper HTTP Status Codes**: 200, 201, 204, 400, 404, 415, 422, 500
- **Consistent Response Format**: All endpoints use `{"data": ...}` envelope
- **Content Negotiation**: Supports `application/json` with charset
- **Null Handling**: Proper support for nullable fields via `array_key_exists()`

### Security & Validation
- **Input Validation**: Comprehensive validation for all endpoints
- **SQL Injection Protection**: Whitelist approach in PATCH operations
- **Error Handling**: Generic errors to clients, detailed logging server-side
- **Type Safety**: `declare(strict_types=1)` throughout

### Data Management
- **CRUD Operations**: Full Create, Read, Update, Delete support
- **Partial Updates**: PATCH for partial updates, PUT for full replacement
- **Filtering & Search**: Filter by status, full-text search
- **Sorting**: Customizable sorting with validation
- **Pagination**: Offset-based pagination with metadata and links

## 🏗️ Technical Implementation

### Components

#### 1. Router (`src/Router.php`)
- Regex-based route matching
- Named parameter extraction
- Method-based routing (GET, POST, PATCH, PUT, DELETE)

#### 2. Request (`src/Request.php`)
- HTTP request parsing from globals
- JSON body validation with error detection
- Path normalization (trailing slash handling)
- Header extraction including `CONTENT_TYPE`

#### 3. Response (`src/Response.php`)
- JSON response builder
- Proper Content-Type handling
- 204 No Content support (headers sent, no body)

#### 4. Validator (`src/Validator.php`)
- Separate validation for CREATE, PATCH, PUT, LIST
- `array_key_exists()` for proper null handling
- Field length validation (title: 255 chars)
- Query parameter validation with defaults
- Sort parameter validation with whitelist

#### 5. Repository (`src/TaskRepository.php`)
- PDO-based database operations
- Prepared statements for security
- Field whitelist in PATCH operations
- Always returns current state (no null on unchanged updates)
- Pagination with proper total_pages calculation

#### 6. Controller (`src/TaskController.php`)
- Request validation before processing
- Invalid JSON detection (400)
- Validation errors (422)
- Consistent response envelopes
- Proper error messages

## 📊 API Endpoints

| Method | Endpoint | Description | Status Codes |
|--------|----------|-------------|--------------|
| GET | `/tasks` | List all tasks | 200, 422 |
| POST | `/tasks` | Create new task | 201, 400, 415, 422 |
| GET | `/tasks/{id}` | Get single task | 200, 404 |
| PATCH | `/tasks/{id}` | Partial update | 200, 400, 404, 415, 422 |
| PUT | `/tasks/{id}` | Full update | 200, 400, 404, 415, 422 |
| DELETE | `/tasks/{id}` | Delete task | 204, 404 |

## 🎓 What This Demonstrates

### Professional Skills
1. **Clean Code**: Readable, maintainable, well-structured
2. **REST API Design**: Proper HTTP semantics and status codes
3. **Database Design**: Normalized schema with indexes
4. **Error Handling**: Comprehensive error handling and logging
5. **Validation**: Input validation at multiple levels
6. **Security**: SQL injection prevention, error message sanitization
7. **Testing**: Edge cases handled (null values, empty results, etc.)

### PHP Best Practices
- Type declarations (`declare(strict_types=1)`) in **all files**
- Final classes where appropriate
- Proper null handling (`array_key_exists()` vs `isset()`)
- PDO with prepared statements
- Error logging without exposure
- Throwable catching (not just Exception)
- Explicit timestamp handling in queries

### API Design Patterns
- **Resource-based URLs**: `/tasks`, `/tasks/{id}`
- **HTTP verbs**: GET, POST, PATCH, PUT, DELETE
- **Status codes**: Semantic HTTP status codes
- **Response envelopes**: Consistent `{"data": ...}` format
- **Pagination**: Metadata with `total`, `page`, `limit`, `total_pages`
- **HATEOAS**: Links to `self`, `first`, `last`, `prev`, `next`

## 🔍 Edge Cases Handled

1. **Null Values**: Proper support for `{"description": null}` in PATCH/PUT
2. **Unchanged Updates**: PATCH with same values returns task (not null)
3. **Empty Pagination**: Empty list shows `total_pages: 1` (not 0)
4. **Invalid JSON**: Returns 400 with clear error message
5. **Validation Errors**: Returns 422 with field-specific errors
6. **SQL Injection**: Whitelist approach in dynamic field updates
7. **Path Normalization**: `/tasks` and `/tasks/` treated equally
8. **Content-Type**: Supports `application/json; charset=utf-8`
9. **Timestamps**: Explicitly set in all operations (create, update)
10. **Database Constraints**: CHECK constraint for status validation

## 📈 Code Quality

- **No Framework Dependencies**: Pure PHP implementation
- **PSR-4 Autoloading**: Custom autoloader
- **Type Safety**: `declare(strict_types=1)` in all files
- **Database Constraints**: CHECK constraints for data integrity
- **Explicit Operations**: Timestamps explicitly managed
- **Error Handling**: Comprehensive try-catch with logging
- **Documentation**: Detailed README, CHANGELOG, and PORTFOLIO
- **Git Ready**: Proper .gitignore, clean commit history

## 🚀 Quick Start

```bash
# Run migration
php scripts/migrate.php

# Start server
php -S localhost:8000 -t public

# Test API
curl http://localhost:8000/tasks
```

## 📝 What I Learned

1. Building REST APIs without frameworks
2. Proper HTTP status code usage
3. Clean architecture principles
4. PHP type system and null handling
5. SQL injection prevention techniques
6. Error handling and logging strategies
7. API versioning and documentation

## 🎯 Future Enhancements

- Authentication (JWT)
- Rate limiting
- API versioning
- OpenAPI/Swagger documentation
- Unit tests (PHPUnit)
- Docker containerization
- CI/CD pipeline

---

**Tech Stack**: PHP 8.1+, SQLite, PDO  
**Architecture**: Clean Architecture, Repository Pattern  
**API Style**: RESTful with proper HTTP semantics
