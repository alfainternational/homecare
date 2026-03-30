<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class RateServiceRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        $serviceRequest = $this->route('serviceRequest');
        return Auth::check()
            && $serviceRequest->client_id === Auth::id()
            && $serviceRequest->status === 'completed'
            && $serviceRequest->rating === null;
    }

    public function rules(): array
    {
        return [
            'rating'  => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required' => 'يرجى اختيار تقييم من 1 إلى 5.',
            'rating.between'  => 'التقييم يجب أن يكون بين 1 و 5.',
        ];
    }
}
