<?php

namespace App\Http\Middleware;

use App\Models\Project;
use App\Models\Sprint;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SprintAuthorization
{
    /**
     * Roles allowed to hit each sprint route.
     * 'member' can only read. 'admin' and 'owner' can manage.
     */
    protected array $permissions = [
        'sprints.index'    => ['owner', 'admin', 'member'],
        'sprints.store'    => ['owner', 'admin'],
        'sprints.show'     => ['owner', 'admin', 'member'],
        'sprints.update'   => ['owner', 'admin'],
        'sprints.destroy'  => ['owner', 'admin'],
        'sprints.status'   => ['owner', 'admin'],
        'sprints.progress' => ['owner', 'admin', 'member'],
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $routeName = $request->route()->getName();

        if (!array_key_exists($routeName, $this->permissions)) {
            // Route wasn't wired into the permissions map - fail closed, not open.
            return response()->json([
                'status'  => 'error',
                'message' => 'This route is not covered by sprint authorization rules.',
            ], 500);
        }

        $projectId = $this->resolveProjectId($request);

        if (!$projectId) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Sprint or project not found.',
            ], 404);
        }

        $membership = $user->projects()
            ->where('projects.id', $projectId)
            ->first();

        if (!$membership) {
            return response()->json([
                'status'  => 'error',
                'message' => 'You are not a member of this project.',
            ], 403);
        }

        $role = $membership->pivot->role;
        $allowedRoles = $this->permissions[$routeName];

        if (!in_array($role, $allowedRoles, true)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'You do not have permission to perform this action.',
            ], 403);
        }

        return $next($request);
    }

    /**
     * Resolve the project ID from whichever route parameter is bound:
     * - {sprint} for show/update/destroy/status/progress
     * - {project} for index
     * - request body 'project_id' for store (no route binding yet)
     */
    protected function resolveProjectId(Request $request): ?int
    {
        $sprint = $request->route('sprint');
        if ($sprint instanceof Sprint) {
            return $sprint->project_id;
        }

        $project = $request->route('project');
        if ($project instanceof Project) {
            return $project->id;
        }

        return $request->input('project_id') ? (int) $request->input('project_id') : null;
    }
}