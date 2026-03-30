<?php

namespace App\Http\Requests\Technician;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class InitialReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        $serviceRequest = $this->route('serviceRequest');
        return Auth::check() && $serviceRequest->technician_id === Auth::id();
    }

    public function rules(): array
    {
        return [
            'problem_description' => 'required|string|max:2000',
            'severity'            => 'required|in:low,medium,high',
            'estimated_duration'  => 'nullable|integer|min:1|max:480',
            'estimated_cost'      => 'nullable|numeric|min:0|max:99999',
            'parts_needed'        => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'problem_description.required'  => 'يرجى وصف المشكلة.',
            'severity.required'             => 'يرجى تحديد درجة الخطورة.',
            'severity.in'                   => 'درجة الخطورة غير صالحة.',
            'estimated_duration.max'        => 'الوقت التقديري لا يتجاوز 480 دقيقة (8 ساعات).',
            'estimated_cost.numeric'        => 'التكلفة التقديرية يجب أن تكون رقماً.',
        ];
    }

    public function parsedParts(): ?array
    {
        $raw = $this->input('parts_needed');
        if (empty($raw)) {
            return null;
        }
        return array_values(array_filter(array_map('trim', explode("\n", $raw))));
    }
}
