<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

// app/Http/Requests/UpdateSprintRequest.php
class UpdateSprintRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'        => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date'  => ['sometimes', 'date'],
            'end_date'    => ['sometimes', 'date', 'after:start_date'],
        ];
    }
}