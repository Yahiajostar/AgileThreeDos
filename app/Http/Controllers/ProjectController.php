<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Http\Requests\StoreProjectRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProjectController extends Controller
{
    public function store(StoreProjectRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = auth('api')->id();
        $validated['created_by'] = auth('api')->id();
        $validated['status']     = 'in_progress';

        $project = Project::create($validated);

        Cache::forget("user_projects_" . auth('api')->id() . "_page_*");

        return response()->json([
            'message' => 'Project created successfully.',
            'project' => array_merge($project->toArray(), ['sprint_count' => 0])
        ], 201);
    }

public function index(Request $request)
{
    $user    = auth('api')->user();
    $status  = $request->query('status', 'in_progress');
    $page    = $request->query('page', 1);
    $perPage = $request->query('per_page', 10);

    $cacheKey = "user_projects_{$user->id}_status_{$status}_page_{$page}_per_{$perPage}";

    return Cache::remember($cacheKey, 600, function () use ($user, $status, $perPage) {
        
        if ($user->role === 'admin') {
            $query = Project::where('user_id', $user->id);
        } else {
            $query = Project::whereHas('users', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $projects = $query->where('status', $status)
                          ->withCount('sprints')
                          ->paginate($perPage);

        return response()->json([
            'data' => collect($projects->items())->map(function($project) {
                return [
                    'id'           => $project->id,
                    'name'         => $project->name,
                    'status'       => $project->status,
                    'start_date'   => $project->start_date,
                    'end_date'     => $project->end_date,
                    'sprint_count' => $project->sprints_count,
                ];
            }),
            'pagination' => [
                'page'        => $projects->currentPage(),
                'per_page'    => $projects->perPage(),
                'total'       => $projects->total(),
                'total_pages' => $projects->lastPage(),
            ]
        ], 200);
    });
}
    public function show($id)
    {
        $user = auth('api')->user();

        return Cache::remember("project_detail_{$id}", 600, function () use ($id, $user) {
            $project = Project::with(['sprints' => function ($q) {
                $q->withCount('tasks');
            }])->find($id);

            if (!$project) {
                return response()->json(['error' => 'You do not have access to this project or it does not exist.'], 404);
            }

            $isMember = $project->users()->where('user_id', $user->id)->exists();
            if ($project->user_id !== $user->id && !$isMember) {
                return response()->json(['error' => 'You do not have access to this project or it does not exist.'], 403);
            }

            return response()->json([
                'id'          => $project->id,
                'name'        => $project->name,
                'description' => $project->description,
                'status'      => $project->status,
                'start_date'  => $project->start_date,
                'end_date'    => $project->end_date,
                'created_by'  => $project->created_by,
                'sprints'     => $project->sprints->map(function ($sprint) {
                    return [
                        'id'         => $sprint->id,
                        'name'       => $sprint->name,
                        'status'     => $sprint->status,
                        'task_count' => $sprint->tasks_count,
                    ];
                })
            ], 200);
        });
    }

    public function update(Request $request, $id)
    {
        $project = Project::find($id);

        if (!$project || $project->user_id !== auth('api')->id()) {
            return response()->json(['error' => 'Unauthorized or project not found.'], 403);
        }

        $validated = $request->validate([
            'name'        => 'sometimes|string',
            'description' => 'sometimes|string',
            'end_date'    => 'sometimes|date',
        ]);

        $project->update($validated);

        Cache::forget("project_detail_{$id}");
        $this->clearProjectListCache();

        return response()->json([
            'message' => 'Project updated successfully.',
            'project' => $project->only(['id', 'name', 'end_date', 'status'])
        ], 200);
    }

    public function destroy($id)
    {
        $project = Project::find($id);

        if (!$project || $project->user_id !== auth('api')->id()) {
            return response()->json(['error' => 'Unauthorized or project not found.'], 403);
        }

        $project->delete();

        Cache::forget("project_detail_{$id}");
        Cache::forget("project_members_{$id}");
        $this->clearProjectListCache();

        return response()->json(['message' => 'Project and all its sprints/tasks deleted successfully.'], 200);
    }
private function clearProjectListCache()
{
    Cache::forget('projects_list'); 
}
    
}