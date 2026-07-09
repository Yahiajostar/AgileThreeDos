<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

// app/Http/Requests/StoreSprintRequest.php
class StoreSprintRequest extends FormRequest
{
    public function authorize()
    {
        return true; // هتستبدليها بـ policy check بعدين
    }

    public function rules()
    {
        return [
            'project_id'  => ['required', 'exists:projects,id'],
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date'  => ['required', 'date'],
            'end_date'    => ['required', 'date', 'after:start_date'],
            'status'      => ['sometimes', 'in:planned,active,completed,cancelled'],
        ];
    }
}