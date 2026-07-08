<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // public function authorize(): bool
    // {
    //     return auth()->check() && auth()->user()->role === 'admin';
    // }
    public function authorize(): bool
{
    if (!auth('api')->check() || auth('api')->user()->role !== 'admin') {
        return false;
    }

    $sprintId = $this->route('sprint') ?? $this->route('sprint_id') ?? $this->input('sprint_id');
    $sprint = \App\Models\Sprint::with('project')->find($sprintId);

    if (!$sprint || !$sprint->project) {
        return false;
    }

    return (int) $sprint->project->user_id === (int) auth('api')->id();
}
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date'    => 'required|date',
            'assigned_to' => 'required|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Task title is required.',
            'due_date.required' => 'Due date is required.',
            'assigned_to.required' => 'You must assign the task to a user.',
            'assigned_to.exists' => 'Selected user does not exist.',
        ];
    }
}
