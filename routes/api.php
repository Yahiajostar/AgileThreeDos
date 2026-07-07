<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::middleware(['auth:api', 'user'])->group(function () {
    Route::get('/sprints/{sprint_id}/tasks', [TaskController::class, 'index']);
    Route::post('/sprints/{sprint_id}/tasks', [TaskController::class, 'store']);
    Route::get('/sprints/{sprint_id}/tasks/{task_id}', [TaskController::class, 'show']);
    Route::put('/sprints/{sprint_id}/tasks/{task_id}', [TaskController::class, 'update']);
    Route::delete('/tasks/{task_id}', [TaskController::class, 'destroy']);
});
