<?php
namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProjectMemberController extends Controller
{
    public function addMember(Request
     $request, $projectId)
    {
        $user    = auth('api')->user();
        $project = Project::find($projectId);

        if (!$project || $project->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized or project not found.'], 403);
        }

        if ($user->plan !== 'premium') {
            return response()->json([
                'error' => 'Managing explicit project memberships is a premium feature. Please upgrade your plan.'
            ], 403);
        }

        $request->validate(['user_id' => 'required|exists:users,id']);

        $project->users()->syncWithoutDetaching([$request->user_id]);

        Cache::forget("project_members_{$projectId}");

        return response()->json(['message' => 'Member added to the project successfully.'], 200);
    }

    public function removeMember($projectId, $userId)
    {
        $user    = auth('api')->user();
        $project = Project::find($projectId);

        if (!$project || $project->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized or project not found.'], 403);
        }

        if ($user->plan !== 'premium') {
            return response()->json([
                'error' => 'Managing explicit project memberships is a premium feature. Please upgrade your plan.'
            ], 403);
        }

        $project->users()->detach($userId);

        Cache::forget("project_members_{$projectId}");

        return response()->json(['message' => 'Member removed from the project successfully.'], 200);
    }

    public function getMembers($projectId)
    {
        $user    = auth('api')->user();
        $project = Project::find($projectId);

        if (!$project) {
            return response()->json(['error' => 'Project not found.'], 404);
        }

        $isMember = $project->users()->where('user_id', $user->id)->exists();
        if ($project->user_id !== $user->id && !$isMember) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        return Cache::remember("project_members_{$projectId}", 600, function () use ($project) {
            return response()->json($project->users()->select('users.id', 'users.name', 'users.email')->get(), 200);
        });
    }
}