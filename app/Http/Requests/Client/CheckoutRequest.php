<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'payment_method' => 'required|in:card,bank,tabby,tamara,wallet',
            'name'           => 'required|string|max:255',
            'phone'          => 'required|string|max:20',
            'address'        => 'required|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.required' => 'يرجى اختيار طريقة الدفع.',
            'payment_method.in'       => 'طريقة الدفع المختارة غير صالحة.',
            'name.required'           => 'يرجى إدخال الاسم الكامل.',
            'phone.required'          => 'يرجى إدخال رقم الجوال.',
            'address.required'        => 'يرجى إدخال عنوان التوصيل.',
        ];
    }
}
