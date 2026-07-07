<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::middleware('auth:api')->group(function () {
    Route::get('/sprints/{sprint_id}/tasks', [TaskController::class, 'index']);

    Route::post('/sprints/{sprint_id}/tasks', [TaskController::class, 'store']);

    Route::delete('/tasks/{task_id}', [TaskController::class, 'destroy']);
});
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;

Route::middleware(['auth:api','user'])->group(function () {
Route::resource('/projects', ProjectController::class);
});


Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);
Route::post('/forgot-password',[AuthController::class,'forgetPassword']);
Route::get('/reset-password/{token}', function (Request $request, string $token) {
    return response()->json([
        'token' => $token,
        'email' => $request->email,
    ]);
})->name('password.reset');
Route::post('/reset-password',[AuthController::class,'resetPassword']);

Route::middleware('auth:api')->group(function () {
Route::post('/logout',[AuthController::class,'logout']);
Route::post('/refresh',[AuthController::class,'refresh']);
Route::get('/user',[AuthController::class,'me']);
}
);
