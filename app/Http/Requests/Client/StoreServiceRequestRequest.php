<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreServiceRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'service_type' => 'nullable|in:plumbing,electrical,hvac,general',
            'type'         => 'nullable|in:plumbing,electrical,hvac,general',
            'description'  => 'nullable|string|max:2000',
            'client_notes' => 'nullable|string|max:1000',
            'notes'        => 'nullable|string|max:1000',
            'media'        => 'nullable|array|max:8',
            'media.*'      => 'file|mimes:jpg,jpeg,png,mp4,mov|max:20480',
            'street'       => 'nullable|string|max:255',
            'district'     => 'nullable|string|max:255',
            'city'         => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'service_type.in' => 'نوع الخدمة غير صالح.',
            'description.max' => 'الوصف يجب ألا يتجاوز 2000 حرف.',
            'media.max'       => 'لا يمكن رفع أكثر من 8 ملفات.',
            'media.*.mimes'   => 'نوع الملف غير مقبول. المقبول: صور وفيديو فقط.',
            'media.*.max'     => 'حجم كل ملف يجب ألا يتجاوز 20 ميغابايت.',
        ];
    }

    public function resolvedServiceType(): ?string
    {
        return $this->input('service_type') ?? $this->input('type');
    }

    public function resolvedNotes(): ?string
    {
        return $this->input('client_notes') ?? $this->input('notes');
    }
}
