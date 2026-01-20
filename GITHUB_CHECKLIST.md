# GitHub Release Checklist ✅

Before pushing to GitHub, verify all items below:

## Code Quality ✅

- [x] All PHP files have `declare(strict_types=1)`
- [x] No syntax errors (tested with PHP 8.5.1)
- [x] All tests pass (`test-api.ps1`)
- [x] No hardcoded credentials or sensitive data
- [x] Error messages don't expose internal details
- [x] SQL injection protection (prepared statements + whitelist)

## Files & Structure ✅

- [x] `.gitignore` excludes database files and `.env`
- [x] `README.md` is complete and accurate
- [x] `CHANGELOG.md` documents all versions
- [x] `LICENSE` file included (MIT)
- [x] `.env.example` provided
- [x] `test-api.ps1` works correctly
- [x] `git-push.bat` ready for Windows users
- [x] All documentation files present

## Documentation ✅

- [x] README has installation instructions
- [x] README has API documentation
- [x] README has example requests
- [x] README lists all query parameters correctly:
  - `status` - Filter by status
  - `search` - Search in title/description
  - `sort` - Sort by field:direction
  - `page` - Page number
  - `limit` - Items per page
- [x] HTTP status codes documented
- [x] Requirements specified (PHP 8.1+)
- [x] Environment variables documented

## Testing ✅

- [x] Migration script works (`php scripts/migrate.php`)
- [x] Server starts (`php -S localhost:8000 -t public`)
- [x] All 11 automated tests pass
- [x] Manual testing completed:
  - [x] Create task (POST)
  - [x] Get list (GET)
  - [x] Get single (GET)
  - [x] Update partial (PATCH)
  - [x] Update full (PUT)
  - [x] Delete (DELETE)
  - [x] Validation errors (422)
  - [x] Invalid JSON (400)
  - [x] Not found (404)

## API Correctness ✅

- [x] HTTP status codes correct:
  - 200 - OK
  - 201 - Created
  - 204 - No Content
  - 400 - Bad Request (malformed JSON)
  - 404 - Not Found
  - 415 - Unsupported Media Type
  - 422 - Unprocessable Entity (validation)
  - 500 - Internal Server Error
- [x] Response format consistent (`{"data": ...}`)
- [x] Timestamps work (created_at, updated_at)
- [x] Null values supported
- [x] Pagination works with metadata
- [x] Filtering and search work
- [x] Sorting works

## Database ✅

- [x] Migration creates table with indexes
- [x] CHECK constraint for status
- [x] DEFAULT values for timestamps
- [x] Prepared statements used everywhere
- [x] No SQL injection vulnerabilities

## Security ✅

- [x] Input validation on all endpoints
- [x] SQL injection protection
- [x] Error messages sanitized
- [x] No sensitive data in logs/responses
- [x] Type safety (`declare(strict_types=1)`)
- [x] Whitelist approach for dynamic fields

## Git Ready ✅

- [x] Clean commit history (or ready for initial commit)
- [x] No large files committed
- [x] No database files committed (check with `git status`)
- [x] `.gitignore` properly configured
- [x] Repository name chosen
- [x] Repository description prepared

### Important: Database File

Before committing, ensure `var/database.sqlite` is NOT tracked:

```bash
# Check if database is tracked
git status

# If it appears, remove it from git (keeps local file)
git rm --cached var/database.sqlite
git commit -m "Remove database from repository"
```

The `.gitignore` file already excludes it, but if you created the database before initializing git, it might have been added.

## Portfolio Ready ✅

- [x] `PORTFOLIO.md` showcases skills
- [x] `PRODUCTION_READY.md` confirms quality
- [x] Code demonstrates best practices
- [x] Documentation is professional
- [x] Project is easy to understand
- [x] Project is easy to run

## Final Steps

1. **Review all files one last time**
   ```bash
   # Check for any TODO or FIXME comments
   grep -r "TODO\|FIXME" src/
   ```

2. **Run tests one final time**
   ```powershell
   php scripts/migrate.php
   php -S localhost:8000 -t public
   # In another terminal:
   .\test-api.ps1
   ```

3. **Initialize Git (if not done)**
   ```bash
   git init
   git add .
   git commit -m "Initial commit: Simple Task API with CRUD operations"
   ```

4. **Create GitHub repository**
   - Go to https://github.com/new
   - Name: `simple-task-api` (or your choice)
   - Description: "Production-ready REST API for task management built with pure PHP"
   - Public repository
   - Don't add README, .gitignore, or license (we have them)

5. **Push to GitHub**
   ```bash
   git remote add origin https://github.com/YOUR_USERNAME/simple-task-api.git
   git branch -M main
   git push -u origin main
   ```

6. **Add topics to repository** (on GitHub)
   - php
   - rest-api
   - sqlite
   - crud
   - clean-architecture
   - psr-4
   - portfolio

7. **Update repository description** (on GitHub)
   Add a detailed description and website link if deploying

## Post-Release

- [ ] Test clone and setup on fresh machine
- [ ] Add repository link to resume/portfolio
- [ ] Consider adding GitHub Actions for CI/CD
- [ ] Consider adding badges to README
- [ ] Star your own repository 😊

---

**Status**: ✅ READY FOR GITHUB

**Last Updated**: 2026-01-20  
**Version**: 1.5.2
