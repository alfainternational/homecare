<?php

namespace App\Http\Requests\Technician;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class FinalReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        $serviceRequest = $this->route('serviceRequest');
        return Auth::check() && $serviceRequest->technician_id === Auth::id();
    }

    public function rules(): array
    {
        return [
            'work_done'       => 'required|string|max:2000',
            'recommendations' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'work_done.required' => 'يرجى وصف الأعمال المنجزة.',
            'work_done.max'      => 'وصف الأعمال يجب ألا يتجاوز 2000 حرف.',
        ];
    }
}
