<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إتمام الدفع — هوم كير</title>
    @vite(['resources/css/app.css'])
    <style>
        body { background: #f9fafb; font-family: 'Cairo', sans-serif; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
<div class="w-full max-w-md">

    {{-- Logo --}}
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-brand rounded-2xl flex items-center justify-center mx-auto mb-3">
            <svg class="w-9 h-9 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
            </svg>
        </div>
        <h1 class="text-xl font-bold text-accent">إتمام الدفع</h1>
        <p class="text-sm text-gray-500 mt-1">بوابة دفع آمنة</p>
    </div>

    {{-- Amount Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-5 text-center">
        <p class="text-sm text-gray-500">المبلغ المطلوب</p>
        <p class="text-4xl font-black text-accent mt-1">{{ number_format($transaction->amount, 2) }}</p>
        <p class="text-lg text-gray-500 font-medium">{{ $transaction->currency }}</p>
        <p class="text-xs text-gray-400 mt-3 font-mono">{{ $transaction->reference }}</p>
    </div>

    {{-- HyperPay Widget --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <script async src="{{ $widgetUrl }}"
                data-brands="{{ $brands }}"
                data-style="card">
        </script>
        <form action="{{ route('payment.callback', $transaction->reference) }}"
              class="paymentWidgets"
              data-brands="{{ $brands }}">
        </form>
    </div>

    <p class="text-center text-xs text-gray-400 mt-5">
        🔒 جميع المعاملات مشفرة وآمنة
    </p>
</div>
</body>
</html>
