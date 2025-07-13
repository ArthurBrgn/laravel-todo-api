<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::post('/login', LoginController::class)->name('auth.login');
Route::post('/register', RegisterController::class)->name('auth.register');

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('projects')->group(function () {
        Route::get('/', [ProjectController::class, 'index']);
        Route::get('/{project}/tasks', [TaskController::class, 'projectTasks']);
        Route::post('/{project}/tasks', [TaskController::class, 'store'])->name('tasks.store');
        Route::get('/{project}/tags/search', [TagController::class, 'search']);
        Route::post('/{project}/users/{user}/associate', [ProjectController::class, 'associateUser']);
    });

    Route::apiResource('projects.tags', TagController::class)
        ->shallow()->except(['show']);

    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskController::class, 'currentUserTasks']);
        Route::get('/search', [TaskController::class, 'search']);
        Route::patch('/{task}/status', [TaskController::class, 'updateStatus']);
        Route::post('/{task}/assign', [TaskController::class, 'assign']);
        Route::post('/{task}/unassign', [TaskController::class, 'unassign']);
    });

    Route::post('/logout', LogoutController::class)->name('auth.logout');
});
