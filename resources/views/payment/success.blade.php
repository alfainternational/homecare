@extends('layouts.app')

@section('title', 'تم الدفع بنجاح')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center p-4">
    <div class="w-full max-w-md text-center space-y-6">

        {{-- Success Animation --}}
        <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto">
            <svg class="w-12 h-12 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <div>
            <h1 class="text-2xl font-black text-accent">تم الدفع بنجاح! 🎉</h1>
            <p class="text-gray-500 mt-2">تمت معالجة دفعتك بنجاح وتأكيد طلبك.</p>
        </div>

        {{-- Transaction Details --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-right space-y-3">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-500">رقم المرجع</span>
                <span class="text-sm font-mono font-bold text-accent">{{ $transaction->reference }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-500">المبلغ المدفوع</span>
                <span class="text-sm font-bold text-green-600">{{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-500">طريقة الدفع</span>
                <span class="text-sm text-accent">{{ $transaction->gateway_code }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-500">وقت الدفع</span>
                <span class="text-sm text-accent">{{ $transaction->paid_at?->format('Y/m/d H:i') }}</span>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('client.dashboard') }}"
                class="flex-1 bg-brand text-white font-bold py-3 rounded-xl hover:bg-brand-dark transition-colors text-sm">
                العودة للوحة التحكم
            </a>
            <a href="{{ route('client.requests.index') }}"
                class="flex-1 border border-gray-200 text-gray-600 font-medium py-3 rounded-xl hover:bg-gray-50 transition-colors text-sm">
                طلباتي
            </a>
        </div>

    </div>
</div>
@endsection
