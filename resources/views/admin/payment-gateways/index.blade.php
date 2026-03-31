@extends('layouts.admin')

@section('title', 'بوابات الدفع')
@section('page-title', 'بوابات الدفع')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-accent">إدارة بوابات الدفع</h2>
            <p class="text-sm text-gray-500 mt-1">تفعيل/تعطيل طرق الدفع وإدارة بياناتها</p>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    {{-- Gateways Grid --}}
    <div class="grid gap-4" x-data="{ editId: null }">

        @foreach($gateways as $gateway)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-5">
                <div class="flex items-center gap-4">
                    {{-- Icon --}}
                    <div class="w-14 h-14 bg-gray-50 rounded-xl flex items-center justify-center flex-shrink-0 text-2xl">
                        {{ $gateway->icon ?? '💳' }}
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-bold text-accent">{{ $gateway->name_ar }}</h3>
                            <span class="text-xs text-gray-400 font-mono bg-gray-100 px-2 py-0.5 rounded">{{ $gateway->code }}</span>
                            @if($gateway->mode === 'test')
                            <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">وضع تجريبي</span>
                            @else
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">وضع حقيقي</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500 mt-0.5">
                            {{ $gateway->name_en }}
                            @if($gateway->min_amount || $gateway->max_amount)
                            &nbsp;·&nbsp;
                            @if($gateway->min_amount) الحد الأدنى: {{ number_format($gateway->min_amount) }} ر.س @endif
                            @if($gateway->max_amount) &nbsp;·&nbsp; الحد الأقصى: {{ number_format($gateway->max_amount) }} ر.س @endif
                            @endif
                        </p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-3 flex-shrink-0">
                        {{-- Toggle --}}
                        <form action="{{ route('admin.payment-gateways.toggle', $gateway) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none
                                       {{ $gateway->is_enabled ? 'bg-brand' : 'bg-gray-200' }}"
                                title="{{ $gateway->is_enabled ? 'تعطيل' : 'تفعيل' }}">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform shadow
                                             {{ $gateway->is_enabled ? 'translate-x-6' : 'translate-x-1' }}"></span>
                            </button>
                        </form>

                        {{-- Edit --}}
                        <button @click="editId = editId === {{ $gateway->id }} ? null : {{ $gateway->id }}"
                            class="text-sm text-brand hover:text-brand-dark font-medium px-3 py-1.5 bg-brand-light rounded-lg transition-colors">
                            إعداد
                        </button>
                    </div>
                </div>
            </div>

            {{-- Edit Panel --}}
            <div x-show="editId === {{ $gateway->id }}" x-collapse class="border-t border-gray-100">
                <form action="{{ route('admin.payment-gateways.update', $gateway) }}" method="POST" class="p-5 space-y-4">
                    @csrf @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">وضع التشغيل</label>
                            <select name="mode" class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:border-brand">
                                <option value="test" {{ $gateway->mode === 'test' ? 'selected' : '' }}>تجريبي (Test)</option>
                                <option value="live" {{ $gateway->mode === 'live' ? 'selected' : '' }}>حقيقي (Live)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">الترتيب</label>
                            <input type="number" name="sort_order" value="{{ $gateway->sort_order }}" min="0"
                                class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:border-brand">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">API Key <span class="text-gray-400 font-normal">(اتركه فارغاً للإبقاء)</span></label>
                            <input type="password" name="api_key" placeholder="••••••••••••••••"
                                class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:border-brand">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Secret Key</label>
                            <input type="password" name="secret_key" placeholder="••••••••••••••••"
                                class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:border-brand">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Merchant ID</label>
                            <input type="password" name="merchant_id" placeholder="••••••••••••••••"
                                class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:border-brand">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Entity ID</label>
                            <input type="password" name="entity_id" placeholder="••••••••••••••••"
                                class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:border-brand">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">الحد الأدنى للمبلغ (ر.س)</label>
                            <input type="number" name="min_amount" value="{{ $gateway->min_amount }}" step="0.01" min="0"
                                class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:border-brand">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">الحد الأقصى للمبلغ (ر.س)</label>
                            <input type="number" name="max_amount" value="{{ $gateway->max_amount }}" step="0.01" min="0"
                                class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:border-brand">
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit"
                            class="bg-brand text-white text-sm font-semibold px-5 py-2 rounded-xl hover:bg-brand-dark transition-colors">
                            حفظ التغييرات
                        </button>
                        <button type="button" @click="editId = null"
                            class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2 rounded-xl border border-gray-200 hover:bg-gray-50 transition-colors">
                            إلغاء
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endforeach

    </div>
</div>
@endsection
