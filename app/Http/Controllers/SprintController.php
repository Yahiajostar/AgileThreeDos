<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreSprintRequest;
use App\Models\Sprint;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Requests\UpdateSprintRequest;
use App\Http\Requests\UpdateSprintStatusRequest;

// app/Http/Controllers/SprintController.php
class SprintController extends Controller
{
    // GET /api/projects/{project}/sprints (or /api/sprints?project_id=)
    public function index(Project $project)
    {
        $sprints = $project->sprints()->withCount('tasks')->get();

        return response()->json([
            'status' => 'success',
            'data'   => $sprints,
        ]);
    }

    // POST /api/sprints
    public function store(StoreSprintRequest $request)
    {
        $sprint = Sprint::create($request->validated());

        return response()->json([
            'status'  => 'success',
            'message' => 'Sprint created successfully',
            'data'    => $sprint,
        ], 201);
    }

    // GET /api/sprints/{sprint}
    public function show(Sprint $sprint)
    {
        $sprint->load(['project', 'tasks']);

        return response()->json([
            'status' => 'success',
            'data'   => $sprint,
        ]);
    }

    // PUT/PATCH /api/sprints/{sprint}
    public function update(UpdateSprintRequest $request, Sprint $sprint)
    {
        $sprint->update($request->validated());

        return response()->json([
            'status'  => 'success',
            'message' => 'Sprint updated successfully',
            'data'    => $sprint,
        ]);
    }

    // DELETE /api/sprints/{sprint}
    public function destroy(Sprint $sprint)
    {
        $sprint->delete(); // soft delete

        return response()->json([
            'status'  => 'success',
            'message' => 'Sprint deleted successfully',
        ]);
    }

    // PATCH /api/sprints/{sprint}/status
    public function updateStatus(UpdateSprintStatusRequest $request, Sprint $sprint)
    {
        $sprint->update(['status' => $request->validated()['status']]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Sprint status updated',
            'data'    => $sprint,
        ]);
    }

    // GET /api/sprints/{sprint}/progress
    public function progress(Sprint $sprint)
    {
        $total     = $sprint->tasks()->count();
        $completed = $sprint->tasks()->where('status', 'done')->count();

        $percentage = $total > 0 ? round(($completed / $total) * 100, 2) : 0;

        return response()->json([
            'status' => 'success',
            'data' => [
                'sprint_id'        => $sprint->id,
                'total_tasks'      => $total,
                'completed_tasks'  => $completed,
                'progress_percent' => $percentage,
            ],
        ]);
    }
}