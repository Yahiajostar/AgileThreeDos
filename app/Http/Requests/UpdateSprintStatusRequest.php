<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

// app/Http/Requests/UpdateSprintStatusRequest.php
class UpdateSprintStatusRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'status' => ['required', 'in:planned,active,completed,cancelled'],
        ];
    }
}