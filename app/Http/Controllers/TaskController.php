<?php

namespace App\Http\Controllers;
use App\Models\Sprint;
use App\Models\User;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Mail\TaskAssignedMail;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\StoreTaskRequest;
use Illuminate\Support\Facades\Cache;
class TaskController extends Controller
{
    public function index(Request $request, $sprint_id)
    {
        $sprint = Sprint::find($sprint_id);
        if (!$sprint) {
            return response()->json(['message' => 'Sprint not found'], 404);
        }

        $query = Task::where('sprint_id', $sprint_id)->with('assignedUser');

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }

        $perPage = $request->query('per_page', 10);
       $paginatedTasks = $query->paginate($perPage);
       $cacheKey = "tasks:sprint:{$sprint_id}:page:{$request->query('page', 1)}:per_page:{$perPage}:search:" . ($request->search ?? '');
        
        $paginatedTasks = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($query, $perPage) {
            return $query->paginate($perPage);
        });
    //$cacheKey = "tasks_sprint_{$sprint_id}";

    //$paginatedTasks = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($query, $perPage) {
      //      return $query->paginate($perPage);
        //});

        $tasksData = collect($paginatedTasks->items())->map(function($task) {
            return [
                'id' => $task->id,
                'title' => $task->title,
                'status' => $task->status,
                'due_date' => $task->due_date,
                'assigned_to' => $task->assignedUser ? [
                    'id' => $task->assignedUser->id,
                    'name' => $task->assignedUser->name
                ] : null
            ];
        });

        return response()->json([
            'data' => $tasksData,
            'pagination' => [
                'page' => $paginatedTasks->currentPage(),
                'per_page' => $paginatedTasks->perPage(),
                'total' => $paginatedTasks->total(),
                'total_pages' => $paginatedTasks->lastPage(),
            ]
        ], 200);
    }

    /**
     * POST
     * Auth Required: Admin
     */
    public function store(StoreTaskRequest  $request, $sprint_id)
    {
        $sprint = Sprint::find($sprint_id);

        if (!$sprint) {
            return response()->json([
                'message' => 'Sprint not found'
            ], 404);
        }
        // if ($request->user()->role !== 'admin') {
        //     return response()->json(['error' => 'Unauthorized. Only admins can create tasks.'], 403);
        // }

        $validated = $request->validated();

        $assignee = User::find($validated['assigned_to']);
        // if ($assignee->role !== 'user') {
        //     return response()->json([
        //         'error' => "not Authorized."
        //     ], 422);
        // }

        $activeTasksCount = Task::where('sprint_id', $sprint_id)
                                ->where('assigned_to', $validated['assigned_to'])
                                ->where('status', '!=', 'completed')
                                ->count();

        $warningMessage = null;
        if ($activeTasksCount >= 3) {
            $warningMessage = 'Warning: This user already has ' . $activeTasksCount . ' active tasks in this sprint!';
        }

            $task = Task::create([
            'sprint_id'   => $sprint_id,
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'due_date'    => $validated['due_date'],
            'status'      => 'in_progress',
            'assigned_to' => $validated['assigned_to'],
        ]);
       // Cache::flush();
       Cache::forget("tasks_sprint_{$sprint_id}");
        Mail::to($assignee->email)->send(new TaskAssignedMail($task));
        return response()->json([
            'message' => 'Task created successfully.',
            'warning' => $warningMessage, 
            'task' => [
                'id'          => $task->id,
                'sprint_id'   => (int)$task->sprint_id,
                'title'       => $task->title,
                'description' => $task->description,
                'due_date'    => $task->due_date,
                'status'      => $task->status,
                'assigned_to' => [
                    'id'   => $assignee->id,
                    'name' => $assignee->name
                ]
            ]
        ], 201);
    }

    /**
     * DELETE 
     * Auth Required: Admin only
     */
    public function destroy(Request $request, $task_id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized. Only admins can delete tasks.'], 403);
        }

        $task = Task::find($task_id);
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }
        //$sprintId = $task->sprint_id;


        $task->delete();
        Cache::flush();
        //Cache::forget("tasks_sprint_{$sprintId}");
        return response()->json([
            'message' => 'Task deleted successfully.'
        ], 200);
    }
     public function show(Request $request, $sprint_id, $task_id)
{
    $task = Cache::remember("task_{$sprint_id}_{$task_id}", 600, function () use ($sprint_id, $task_id) {
        return Task::where('sprint_id', $sprint_id)
                    ->where('id', $task_id)
                    ->with('assignedUser')
                    ->first();
    });

    if (!$task) {
        return response()->json(['message' => 'Task not found'], 404);
    }

    return response()->json([
        'id' => $task->id,
        'sprint_id' => $task->sprint_id,
        'title' => $task->title,
        'description' => $task->description,
        'due_date' => $task->due_date,
        'status' => $task->status,
        'assigned_to' => $task->assignedUser ? [
            'id' => $task->assignedUser->id,
            'name' => $task->assignedUser->name
        ] : null
    ], 200);
}

public function update(Request $request, $sprint_id, $task_id)
{
    $task = Task::where('sprint_id', $sprint_id)
                ->where('id', $task_id)
                ->first();

    if (!$task) {
        return response()->json(['message' => 'Task not found'], 404);
    }
    if ($request->user()->role !== 'admin') {
    return response()->json(['error' => 'Unauthorized. Only admins can update tasks.'], 403);
}

    $validated = $request->validate([
        'status' => 'sometimes|in:pending,in_progress,completed',
        'title' => 'sometimes|string',
        'description' => 'sometimes|string',
        'due_date' => 'sometimes|date',
    ]);

    $task->update($validated);

    Cache::forget("task_{$sprint_id}_{$task_id}");

    return response()->json([
        'message' => 'Task updated successfully.',
        'task' => $task
    ], 200);
}
}
