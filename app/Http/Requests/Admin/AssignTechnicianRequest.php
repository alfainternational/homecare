<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AssignTechnicianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['admin', 'supervisor']);
    }

    public function rules(): array
    {
        return [
            'technician_id' => 'required|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'technician_id.required' => 'يرجى اختيار فني.',
            'technician_id.exists'   => 'الفني المختار غير موجود.',
        ];
    }
}
