<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
public function index(Request $request){
    if ($request->user()->role !== 'admin'){
        return response()->json([
            'error' => 'Unauthorized. Only admins can view users.'
        ], 403);
    }

    $perPage = $request->query('per_page', 10);

    $cacheKey = "users:page:" . $request->query('page', 1) . ":per_page:" . $perPage;

    $users = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($perPage){
        return User::paginate($perPage);
    });
    return response()->json([
        'data' => $users->items(),
        'pagination' => [
             'page' => $users->currentPage(),
             'per_page' => $users->perPage(),
             'total' => $users->total(),
             'total_pages' => $users->lastPage(),
        ]
    ], 200);
}

public function show(Request $request, $id){
    if ($request->user()->role !== 'admin'){
        return response()->json([
            'error' => 'Unauthorized. Only admins can view users.'
        ], 403);
    }

    $user = Cache::remember("user_$id", now()->addMinutes(10), function () use ($id){
        return User::find($id);
    });

    if (!$user){
        return response()->json([
            'message' => 'User not found'
        ], 404);
    }
    return response()->json($user, 200);
}

public function destroy(Request $request, $id){
    if ($request->user()->role !== 'admin'){
        return response()->json([
            'error' => 'Unauthorized. Only admins can delete users.'
        ], 403);
    }

    $user = User::find($id);

    if (!$user){
        return response()->json([
            'message' => 'User not found'
        ], 404);
    }

    $user->delete();

    Cache::forget("user_$id");
    Cache::flush();

    return response()->json([
        'message' => 'User deleted successfully.'
    ], 200);
}

public function updateRole(Request $request){
    $validated = $request->validate([
        'role' => 'required|in:team_leader,team_member',
    ]);

    $user = auth('api')->user();

    $user->update([
        'role' => $validated['role']
    ]);

    if ($validated['role'] === 'team_leader'){
        return response()->json([
            'message' => 'Role updated successfully.',
            'next_step' => 'SELECT_PLAN'
        ], 200);
    }
    return response()->json([
        'message' => 'Role updated successfully.',
        'next_step' => 'HOME'
    ], 200);
}

public function updatePlan(Request $request){
    $validated = $request->validate([
        'plan' => 'required|in:free,premium',
    ]);

    $user = auth('api')->user();

    if ($user->role !== 'team_leader'){
        return response()->json([
            'error' => 'Only team leaders can select a subscription plan.'
        ], 403);
    }
    $user->update([
        'plan' => $validated['plan']
    ]);
    return response()->json([
        'message' => 'Plan selected successfully.',
        'next_step' => 'HOME'
    ], 200);
}
}