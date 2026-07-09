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
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProjectMemberController;
use App\Http\Controllers\SprintController;

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
    Route::get('/admin/users', [UserController::class, 'index']);
    Route::get('/admin/users/{id}', [UserController::class, 'show']);
    Route::delete('/users/me', [UserController::class, 'destroy']);
    Route::patch('/users/me/role', [UserController::class, 'updateRole']);
    Route::patch('/users/me/plan', [UserController::class, 'updatePlan']);
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
    Route::get('/tasks/{taskId}/comments', [CommentController::class, 'index']);
    Route::post('/comments', [CommentController::class, 'store']);
    Route::put('/comments/{id}', [CommentController::class, 'update']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);
     Route::post('sprints', [SprintController::class, 'store'])->name('sprints.store');
    Route::get('sprints/{sprint}', [SprintController::class, 'show'])->name('sprints.show');
    Route::put('sprints/{sprint}', [SprintController::class, 'update'])->name('sprints.update');
    Route::delete('sprints/{sprint}', [SprintController::class, 'destroy'])->name('sprints.destroy');
    Route::patch('sprints/{sprint}/status', [SprintController::class, 'updateStatus'])->name('sprints.status');
    Route::get('sprints/{sprint}/progress', [SprintController::class, 'progress'])->name('sprints.progress');
});