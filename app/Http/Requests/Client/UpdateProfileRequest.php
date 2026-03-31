<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'name'  => 'required|string|max:255',
            'phone' => 'required|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'يرجى إدخال الاسم الكامل.',
            'phone.required' => 'يرجى إدخال رقم الجوال.',
            'phone.regex'    => 'رقم الجوال يحتوي على أحرف غير صالحة.',
            'email.required' => 'يرجى إدخال البريد الإلكتروني.',
            'email.email'    => 'البريد الإلكتروني غير صالح.',
            'email.unique'   => 'هذا البريد الإلكتروني مسجل لحساب آخر.',
        ];
    }
}
