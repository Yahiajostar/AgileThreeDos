<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DeleteTaskRequest extends FormRequest
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
    if (!auth()->check() || auth()->user()->role !== 'admin') {
        return false;
    }

    $sprintId = $this->input('sprint_id') ?? $this->route('sprint_id');

    $sprint = \App\Models\Sprint::with('project')->find($sprintId);

    return $sprint && $sprint->project && $sprint->project->created_by === auth()->id();
}
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }
}
