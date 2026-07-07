<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\SprintResource;
use App\Models\Sprint;

class SprintController extends Controller
{
public function index(Request $request)
{
    $this->authorize('viewAny', Sprint::class);

    $query = Sprint::query();
    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }
    return response()->json([
        'sprints_retrieved' => SprintResource::collection($query->get()),
    ], 200);
}
}
