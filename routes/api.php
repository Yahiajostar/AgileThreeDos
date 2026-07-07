<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/sprints/{sprint_id}/tasks', [TaskController::class, 'index']);

    Route::post('/sprints/{sprint_id}/tasks', [TaskController::class, 'store']);

    Route::delete('/tasks/{task_id}', [TaskController::class, 'destroy']);
});