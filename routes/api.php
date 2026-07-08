<?php
// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\TaskController;

// Route::middleware('auth:api')->group(function ()) {
// Route::middleware(['auth:api', 'user'])->group(function () {
//     Route::get('/sprints/{sprint_id}/tasks', [TaskController::class, 'index']);
//     Route::post('/sprints/{sprint_id}/tasks', [TaskController::class, 'store']);
//     Route::get('/sprints/{sprint_id}/tasks/{task_id}', [TaskController::class, 'show']);
//     Route::put('/sprints/{sprint_id}/tasks/{task_id}', [TaskController::class, 'update']);
//     Route::delete('/tasks/{task_id}', [TaskController::class, 'destroy']);
// })};
// //use Illuminate\Support\Facades\Route;

// Route::middleware(['auth:api','user'])->group(function () {
// Route::resource('/projects', ProjectController::class);
// });


// Route::post('/register',[AuthController::class,'register']);
// Route::post('/login',[AuthController::class,'login']);
// Route::post('/forgot-password',[AuthController::class,'forgetPassword']);
// Route::get('/reset-password/{token}', function (Request $request, string $token) {
//     return response()->json([
//         'token' => $token,
//         'email' => $request->email,
//     ]);
// })->name('password.reset');
// Route::post('/reset-password',[AuthController::class,'resetPassword']);

// Route::middleware('auth:api')->group(function () {
// Route::post('/logout',[AuthController::class,'logout']);
// Route::post('/refresh',[AuthController::class,'refresh']);
// Route::get('/user',[AuthController::class,'me']);
// }
// );


use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommentController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgetPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::get('/reset-password/{token}', function (Request $request, string $token) {
    return response()->json([
        'token' => $token,
        'email' => $request->email,
    ]);
})->name('password.reset');

// Route::middleware('auth:api')->group(function ()) {

Route::middleware(['auth:api'])->group(function () {
    
    Route::get('/sprints/{sprint_id}/tasks', [TaskController::class, 'index']);
    Route::post('/sprints/{sprint_id}/tasks', [TaskController::class, 'store']);
    Route::get('/sprints/{sprint_id}/tasks/{task_id}', [TaskController::class, 'show']);
    Route::put('/sprints/{sprint_id}/tasks/{task_id}', [TaskController::class, 'updateTask']);
    Route::patch('/sprints/{sprint_id}/tasks/{task_id}/status', [TaskController::class, 'updateStatus']);
    Route::delete('/tasks/{task_id}', [TaskController::class, 'destroy']);
    
    Route::resource('/projects', ProjectController::class);
    
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user', [AuthController::class, 'me']);

    Route::get('/admin/users', [UserController::class, 'index']);
    Route::get('/admin/users/{id}', [UserController::class, 'show']);
    Route::delete('/users/me', [UserController::class, 'destroy']);
    Route::patch('/users/me/role', [UserController::class, 'updateRole']);
    Route::patch('/users/me/plan', [UserController::class, 'updatePlan']);

    Route::get('/tasks/{taskId}/comments', [CommentController::class, 'index']);
    Route::post('/comments', [CommentController::class, 'store']);
    Route::put('/comments/{id}', [CommentController::class, 'update']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);
});