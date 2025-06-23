<?php

declare(strict_types=1);

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::prefix('projects')->group(function () {
    Route::get('/', [ProjectController::class, 'index']);
    Route::get('/{project}/tasks', [ProjectController::class, 'tasks']);
});

Route::apiResource('projects.tags', TagController::class)
    ->shallow()->except(['show']);

Route::get('/tasks/search', [TaskController::class, 'search']);
