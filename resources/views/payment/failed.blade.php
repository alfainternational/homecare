@extends('layouts.app')

@section('title', 'فشل الدفع')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center p-4">
    <div class="w-full max-w-md text-center space-y-6">

        {{-- Fail Icon --}}
        <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto">
            <svg class="w-12 h-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>

        <div>
            <h1 class="text-2xl font-black text-accent">لم يتم الدفع</h1>
            <p class="text-gray-500 mt-2">
                {{ $message ?? 'حدث خطأ أثناء معالجة الدفع. لم يتم خصم أي مبلغ من حسابك.' }}
            </p>
        </div>

        @if(isset($transaction))
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-right space-y-3">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-500">رقم المرجع</span>
                <span class="text-sm font-mono text-accent">{{ $transaction->reference }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-500">المبلغ</span>
                <span class="text-sm text-accent">{{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}</span>
            </div>
        </div>
        @endif

        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-right">
            <p class="text-sm text-yellow-800 font-semibold mb-1">أسباب محتملة:</p>
            <ul class="text-sm text-yellow-700 space-y-1 list-disc list-inside">
                <li>رصيد غير كافٍ في البطاقة</li>
                <li>بيانات البطاقة غير صحيحة</li>
                <li>البطاقة غير مفعّلة للدفع الإلكتروني</li>
                <li>انتهت صلاحية الجلسة</li>
            </ul>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            @if(isset($transaction) && $transaction->status !== 'paid')
            <a href="{{ route('payment.callback', $transaction->reference) }}?retry=1"
                class="flex-1 bg-brand text-white font-bold py-3 rounded-xl hover:bg-brand-dark transition-colors text-sm">
                المحاولة مجدداً
            </a>
            @endif
            <a href="{{ route('client.dashboard') }}"
                class="flex-1 border border-gray-200 text-gray-600 font-medium py-3 rounded-xl hover:bg-gray-50 transition-colors text-sm">
                العودة للرئيسية
            </a>
        </div>

        <p class="text-xs text-gray-400">
            إذا استمرت المشكلة، تواصل معنا على
            <a href="tel:+966500000000" class="text-brand">+966 50 000 0000</a>
        </p>

    </div>
</div>
@endsection
