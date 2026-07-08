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
use App\Http\Controllers\ProjectMemberController;
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
});


Route::middleware('auth:api')->group(function () {
    Route::post('/projects', [ProjectController::class, 'store']);
    Route::get('/projects', [ProjectController::class, 'index']);
    Route::get('/projects/{project_id}', [ProjectController::class, 'show']);
    Route::put('/projects/{project_id}', [ProjectController::class, 'update']);
    Route::delete('/projects/{project_id}', [ProjectController::class, 'destroy']);

    Route::post('/projects/{project_id}/members', [ProjectMemberController::class, 'addMember']);
    Route::delete('/projects/{project_id}/members/{user_id}', [ProjectMemberController::class, 'removeMember']);
    Route::get('/projects/{project_id}/members', [ProjectMemberController::class, 'getMembers']);
});