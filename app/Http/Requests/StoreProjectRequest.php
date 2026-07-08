<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('api')->check() && auth('api')->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
        ];
    }

    protected function prepareForValidation()
    {
        $user = auth('api')->user();
        
        if ($user && $user->plan === 'free') {
            $projectCount = \App\Models\Project::where('created_by', $user->id)->count();
            
            if ($projectCount >= 5) {
                abort(response()->json([
                    'error' => 'Free plan allows up to 5 projects. Upgrade to premium for unlimited projects.'
                ], 403));
            }
        }
    }
}