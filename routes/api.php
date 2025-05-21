<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\ExportImportJobController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/check', [AuthController::class, 'check']);

    // Role Management Routes
    Route::apiResource('roles', RoleController::class);

    // Permission Management Routes
    Route::apiResource('permissions', PermissionController::class);

    // User Management Routes (Admin only)
    Route::middleware('role:Administrator')->group(function () {
        Route::apiResource('users', UserController::class);
    });

    // Library Management Routes
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('authors', AuthorController::class);
    Route::apiResource('books', BookController::class);
    Route::get('books/{book}/download', [BookController::class, 'download']);

    // Audit routes
    Route::get('/audits/{modelType}/{modelId}', [AuditController::class, 'index']);
    Route::get('/audits/{audit}', [AuditController::class, 'show']);

    // Book routes
    Route::get('/books', [BookController::class, 'index']);
    Route::post('/books', [BookController::class, 'store']);
    Route::get('/books/{book}', [BookController::class, 'show']);
    Route::put('/books/{book}', [BookController::class, 'update']);
    Route::delete('/books/{book}', [BookController::class, 'destroy']);
    Route::post('/books/{uuid}/restore', [BookController::class, 'restore']);
    Route::delete('/books/{uuid}/force', [BookController::class, 'forceDelete']);

    // Author routes
    Route::get('/authors', [AuthorController::class, 'index']);
    Route::post('/authors', [AuthorController::class, 'store']);
    Route::get('/authors/{author}', [AuthorController::class, 'show']);
    Route::put('/authors/{author}', [AuthorController::class, 'update']);
    Route::delete('/authors/{author}', [AuthorController::class, 'destroy']);
    Route::post('/authors/{uuid}/restore', [AuthorController::class, 'restore']);
    Route::delete('/authors/{uuid}/force', [AuthorController::class, 'forceDelete']);
    Route::get('/authors/search', [AuthorController::class, 'search']);

    // Category routes
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
    Route::post('/categories/{uuid}/restore', [CategoryController::class, 'restore']);
    Route::delete('/categories/{uuid}/force', [CategoryController::class, 'forceDelete']);
    Route::get('/categories/search', [CategoryController::class, 'search']);

    // Excel Export/Import Routes
    Route::post('/excel/export', [ExcelController::class, 'export']);
    Route::post('/excel/import', [ExcelController::class, 'import']);
    Route::get('/excel/fields', [ExcelController::class, 'getAvailableFields']);

    // Export/Import Job History Routes
    Route::get('/history', [ExportImportJobController::class, 'index']);
    Route::get('/history/{uuid}', [ExportImportJobController::class, 'show']);
    Route::get('/history/{uuid}/download', [ExportImportJobController::class, 'download']);
});
