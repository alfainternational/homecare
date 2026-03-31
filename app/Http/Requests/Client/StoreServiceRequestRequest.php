<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreServiceRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        $legacyTypes = ['plumbing', 'electrical', 'hvac', 'general'];

        return [
            // Either a legacy service_type OR a service_category_id is required
            'service_type'        => ['nullable', Rule::in($legacyTypes)],
            'type'                => ['nullable', Rule::in($legacyTypes)],
            'service_category_id' => ['nullable', 'integer', 'exists:service_categories,id'],
            'request_type'        => ['nullable', 'in:subscription,on_demand,marketplace'],

            'address_id'          => ['nullable', 'integer', 'exists:addresses,id'],
            'description'         => 'nullable|string|max:2000',
            'client_notes'        => 'nullable|string|max:1000',
            'notes'               => 'nullable|string|max:1000',
            'priority'            => 'nullable|in:low,medium,high',
            'scheduled_at'        => 'nullable|date|after:now',

            'media'               => 'nullable|array|max:8',
            'media.*'             => 'file|mimes:jpg,jpeg,png,webp,mp4,mov,avi|max:51200',

            // Address fields (used when no address_id provided)
            'street'              => 'nullable|string|max:255',
            'district'            => 'nullable|string|max:255',
            'city'                => 'nullable|string|max:100',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $hasLegacyType = $this->filled('service_type') || $this->filled('type');
            $hasCategoryId = $this->filled('service_category_id');

            if (!$hasLegacyType && !$hasCategoryId) {
                $validator->errors()->add('service_type', 'يرجى تحديد نوع الخدمة.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'service_type.in'           => 'نوع الخدمة غير صالح.',
            'service_category_id.exists'=> 'فئة الخدمة المحددة غير موجودة.',
            'scheduled_at.after'        => 'تاريخ الزيارة يجب أن يكون في المستقبل.',
            'media.max'                 => 'لا يمكن رفع أكثر من 8 ملفات.',
            'media.*.mimes'             => 'نوع الملف غير مقبول. المقبول: صور (jpg,png,webp) وفيديو (mp4,mov,avi).',
            'media.*.max'               => 'حجم كل ملف يجب ألا يتجاوز 50 ميغابايت.',
            'address_id.exists'         => 'العنوان المحدد غير موجود في حسابك.',
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
