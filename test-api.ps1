# Simple Task API - Test Script
# Comprehensive test suite for all endpoints

# Force UTF-8 output in PowerShell
[Console]::OutputEncoding = [System.Text.UTF8Encoding]::new()
$OutputEncoding = [System.Text.UTF8Encoding]::new()

$base = "http://localhost:8000"
$ErrorActionPreference = "Stop"

# Safe ASCII symbols for cross-platform compatibility
$OK = "[OK]"
$FAIL = "[FAIL]"

Write-Host "================================" -ForegroundColor Cyan
Write-Host "  Simple Task API - Test Suite" -ForegroundColor Cyan
Write-Host "================================" -ForegroundColor Cyan
Write-Host ""

# Helper function for error handling
function Invoke-ApiRequest {
    param(
        [string]$Uri,
        [string]$Method = "Get",
        [object]$Body = $null
    )
    
    try {
        $params = @{
            Uri = $Uri
            Method = $Method
            ContentType = "application/json"
        }
        
        if ($Body) {
            $params.Body = ($Body | ConvertTo-Json)
        }
        
        $response = Invoke-RestMethod @params
        return @{ Success = $true; Data = $response; Status = 200 }
    }
    catch {
        $status = [int]$_.Exception.Response.StatusCode
        $stream = $_.Exception.Response.GetResponseStream()
        $reader = New-Object System.IO.StreamReader($stream)
        $body = $reader.ReadToEnd()
        
        return @{ Success = $false; Status = $status; Error = $body }
    }
}

# Test 1: Create task with timestamps
Write-Host "Test 1: Create task with timestamps" -ForegroundColor Yellow
$payload = @{
    title = "Test task"
    description = "Testing API"
    status = "pending"
}

$result = Invoke-ApiRequest -Uri "$base/tasks" -Method Post -Body $payload

if ($result.Success) {
    $task = $result.Data.data
    $taskId = $task.id
    
    Write-Host "  $OK Status: 201 Created" -ForegroundColor Green
    Write-Host "  $OK ID: $($task.id)" -ForegroundColor Green
    Write-Host "  $OK Created at: $($task.created_at)" -ForegroundColor Green
    Write-Host "  $OK Updated at: $($task.updated_at)" -ForegroundColor Green
    Write-Host "  $OK Has timestamps: $(($task.created_at -ne $null) -and ($task.updated_at -ne $null))" -ForegroundColor Green
} else {
    Write-Host "  $FAIL Failed: Status $($result.Status)" -ForegroundColor Red
    Write-Host "  $($result.Error)" -ForegroundColor Red
    exit 1
}

Write-Host ""

# Test 2: PATCH - Update status
Write-Host "Test 2: PATCH - Update status" -ForegroundColor Yellow
Start-Sleep -Milliseconds 1200  # Ensure updated_at changes
$patchPayload = @{ status = "in_progress" }

$result = Invoke-ApiRequest -Uri "$base/tasks/$taskId" -Method Patch -Body $patchPayload

if ($result.Success) {
    $task = $result.Data.data
    Write-Host "  $OK Status: 200 OK" -ForegroundColor Green
    Write-Host "  $OK New status: $($task.status)" -ForegroundColor Green
    Write-Host "  $OK Updated at: $($task.updated_at)" -ForegroundColor Green
} else {
    Write-Host "  $FAIL Failed: Status $($result.Status)" -ForegroundColor Red
}

Write-Host ""

# Test 3: PATCH - Clear description with null
Write-Host "Test 3: PATCH - Clear description (null)" -ForegroundColor Yellow
$patchNull = @{ description = $null }

$result = Invoke-ApiRequest -Uri "$base/tasks/$taskId" -Method Patch -Body $patchNull

if ($result.Success) {
    $task = $result.Data.data
    Write-Host "  $OK Status: 200 OK" -ForegroundColor Green
    Write-Host "  $OK Description is null: $($task.description -eq $null)" -ForegroundColor Green
} else {
    Write-Host "  $FAIL Failed: Status $($result.Status)" -ForegroundColor Red
}

Write-Host ""

# Test 4: PUT - Full update
Write-Host "Test 4: PUT - Full update" -ForegroundColor Yellow
$putPayload = @{
    title = "Updated task"
    description = "New description"
    status = "done"
}

$result = Invoke-ApiRequest -Uri "$base/tasks/$taskId" -Method Put -Body $putPayload

if ($result.Success) {
    $task = $result.Data.data
    Write-Host "  $OK Status: 200 OK" -ForegroundColor Green
    Write-Host "  $OK Title: $($task.title)" -ForegroundColor Green
    Write-Host "  $OK Description: $($task.description)" -ForegroundColor Green
    Write-Host "  $OK Status: $($task.status)" -ForegroundColor Green
} else {
    Write-Host "  $FAIL Failed: Status $($result.Status)" -ForegroundColor Red
}

Write-Host ""

# Test 5: GET single task
Write-Host "Test 5: GET single task" -ForegroundColor Yellow
$result = Invoke-ApiRequest -Uri "$base/tasks/$taskId"

if ($result.Success) {
    Write-Host "  $OK Status: 200 OK" -ForegroundColor Green
    Write-Host "  $OK Task retrieved successfully" -ForegroundColor Green
} else {
    Write-Host "  $FAIL Failed: Status $($result.Status)" -ForegroundColor Red
}

Write-Host ""

# Test 6: GET list with filters
Write-Host "Test 6: GET list with filters" -ForegroundColor Yellow
$result = Invoke-ApiRequest -Uri "$base/tasks?status=done&limit=5&page=1&sort=title:asc"

if ($result.Success) {
    $data = $result.Data
    Write-Host "  $OK Status: 200 OK" -ForegroundColor Green
    Write-Host "  $OK Total: $($data.meta.total)" -ForegroundColor Green
    Write-Host "  $OK Page: $($data.meta.page), Limit: $($data.meta.limit)" -ForegroundColor Green
} else {
    Write-Host "  $FAIL Failed: Status $($result.Status)" -ForegroundColor Red
}

Write-Host ""

# Test 7: Invalid JSON (should return 400)
Write-Host "Test 7: Invalid JSON (should return 400)" -ForegroundColor Yellow
try {
    Invoke-RestMethod -Uri "$base/tasks" -Method Post -ContentType "application/json" -Body "{invalid}"
    Write-Host "  $FAIL Should have failed" -ForegroundColor Red
} catch {
    $status = [int]$_.Exception.Response.StatusCode
    if ($status -eq 400) {
        Write-Host "  $OK Status: 400 Bad Request" -ForegroundColor Green
    } else {
        Write-Host "  $FAIL Wrong status: $status (expected 400)" -ForegroundColor Red
    }
}

Write-Host ""

# Test 8: Validation error (should return 422)
Write-Host "Test 8: Validation error (should return 422)" -ForegroundColor Yellow
$invalidPayload = @{ description = "No title" }

$result = Invoke-ApiRequest -Uri "$base/tasks" -Method Post -Body $invalidPayload

if (!$result.Success -and $result.Status -eq 422) {
    Write-Host "  $OK Status: 422 Unprocessable Entity" -ForegroundColor Green
} else {
    Write-Host "  $FAIL Wrong status: $($result.Status) (expected 422)" -ForegroundColor Red
}

Write-Host ""

# Test 9: Query validation (should return 422)
Write-Host "Test 9: Query validation (should return 422)" -ForegroundColor Yellow
try {
    Invoke-RestMethod -Uri "$base/tasks?page=invalid"
    Write-Host "  $FAIL Should have failed" -ForegroundColor Red
} catch {
    $status = [int]$_.Exception.Response.StatusCode
    if ($status -eq 422) {
        Write-Host "  $OK Status: 422 Unprocessable Entity" -ForegroundColor Green
    } else {
        Write-Host "  $FAIL Wrong status: $status (expected 422)" -ForegroundColor Red
    }
}

Write-Host ""

# Test 10: DELETE task (should return 204)
Write-Host "Test 10: DELETE task (should return 204)" -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "$base/tasks/$taskId" -Method Delete
    if ($response.StatusCode -eq 204) {
        Write-Host "  $OK Status: 204 No Content" -ForegroundColor Green
        Write-Host "  $OK Body is empty: $($response.Content.Length -eq 0)" -ForegroundColor Green
    } else {
        Write-Host "  $FAIL Wrong status: $($response.StatusCode)" -ForegroundColor Red
    }
} catch {
    Write-Host "  $FAIL Failed" -ForegroundColor Red
}

Write-Host ""

# Test 11: GET deleted task (should return 404)
Write-Host "Test 11: GET deleted task (should return 404)" -ForegroundColor Yellow
try {
    Invoke-RestMethod -Uri "$base/tasks/$taskId"
    Write-Host "  $FAIL Should have failed" -ForegroundColor Red
} catch {
    $status = [int]$_.Exception.Response.StatusCode
    if ($status -eq 404) {
        Write-Host "  $OK Status: 404 Not Found" -ForegroundColor Green
    } else {
        Write-Host "  $FAIL Wrong status: $status (expected 404)" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "================================" -ForegroundColor Cyan
Write-Host "  All Tests Completed!" -ForegroundColor Cyan
Write-Host "================================" -ForegroundColor Cyan
