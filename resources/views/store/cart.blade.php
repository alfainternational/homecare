@extends('layouts.app')
@section('title', 'سلة المشتريات — WarmConcierge')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-12">
  <h1 class="text-3xl font-black text-gray-800 mb-8">🛒 سلة المشتريات</h1>

  @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-2xl p-4 mb-6 flex items-center gap-3">
      <span class="text-green-500 text-xl">✓</span>
      <p class="text-green-700 font-medium">{{ session('success') }}</p>
    </div>
  @endif

  @php
    $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
    $tax = $total * 0.15;
    $grand = $total + $tax;
  @endphp

  @if(count($cart))
    <div class="flex flex-col lg:flex-row gap-8">

      {{-- Cart Items --}}
      <div class="flex-1">
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
          <table class="w-full">
            <thead class="bg-gray-50">
              <tr>
                <th class="py-4 px-6 text-right text-sm font-bold text-gray-600">المنتج</th>
                <th class="py-4 px-4 text-center text-sm font-bold text-gray-600">السعر</th>
                <th class="py-4 px-4 text-center text-sm font-bold text-gray-600">الكمية</th>
                <th class="py-4 px-4 text-center text-sm font-bold text-gray-600">الإجمالي</th>
                <th class="py-4 px-4 text-center text-sm font-bold text-gray-600"></th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              @foreach($cart as $id => $item)
                <tr class="hover:bg-gray-50 transition-colors">
                  <td class="py-5 px-6">
                    <div class="flex items-center gap-4">
                      <div class="w-14 h-14 bg-gray-100 rounded-xl flex items-center justify-center text-2xl flex-shrink-0">
                        @if(!empty($item['image']))
                          <img src="{{ Storage::url($item['image']) }}" class="w-full h-full object-cover rounded-xl">
                        @else
                          📦
                        @endif
                      </div>
                      <div>
                        <p class="font-bold text-gray-800">{{ $item['name'] }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="py-5 px-4 text-center font-semibold text-gray-700">{{ number_format($item['price']) }} ر.س</td>
                  <td class="py-5 px-4 text-center">
                    <span class="bg-gray-100 px-4 py-1.5 rounded-xl font-bold text-gray-700">{{ $item['quantity'] }}</span>
                  </td>
                  <td class="py-5 px-4 text-center font-bold text-[#D4881A]">{{ number_format($item['price'] * $item['quantity']) }} ر.س</td>
                  <td class="py-5 px-4 text-center">
                    <form method="POST" action="{{ route('store.remove-cart', $id) }}">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="text-red-400 hover:text-red-600 hover:bg-red-50 p-2 rounded-xl transition-all" title="حذف">✕</button>
                    </form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="flex justify-between mt-4">
          <a href="{{ route('store.index') }}" class="flex items-center gap-2 text-[#D4881A] font-medium hover:underline text-sm">
            ← مواصلة التسوق
          </a>
        </div>
      </div>

      {{-- Summary --}}
      <div class="w-full lg:w-80 flex-shrink-0">
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 sticky top-24">
          <h3 class="text-xl font-black text-gray-800 mb-6">ملخص الطلب</h3>
          <div class="space-y-4 mb-6">
            <div class="flex justify-between text-sm">
              <span class="text-gray-500">المجموع الفرعي</span>
              <span class="font-semibold">{{ number_format($total, 2) }} ر.س</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-gray-500">ضريبة القيمة المضافة (15%)</span>
              <span class="font-semibold">{{ number_format($tax, 2) }} ر.س</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-gray-500">رسوم التوصيل</span>
              <span class="font-semibold text-green-600">مجاناً</span>
            </div>
            <div class="border-t border-gray-100 pt-4 flex justify-between">
              <span class="font-black text-gray-800">الإجمالي</span>
              <span class="font-black text-2xl text-[#D4881A]">{{ number_format($grand, 2) }} ر.س</span>
            </div>
          </div>
          <a href="{{ route('store.checkout') }}" class="block text-center bg-[#F5A623] hover:bg-[#D4881A] text-white font-black py-4 rounded-2xl text-lg transition-all shadow-lg shadow-orange-100">
            إتمام الشراء ←
          </a>
          <p class="text-center text-xs text-gray-400 mt-3 flex items-center justify-center gap-1">
            <span>🔒</span> تسوق آمن 100%
          </p>
        </div>
      </div>

    </div>
  @else
    <div class="text-center py-24">
      <div class="text-7xl mb-6">🛒</div>
      <h3 class="text-2xl font-bold text-gray-700 mb-3">سلتك فارغة</h3>
      <p class="text-gray-400 mb-8">لم تضف أي منتجات بعد. استعرض المتجر وأضف ما تحتاجه.</p>
      <a href="{{ route('store.index') }}" class="bg-[#F5A623] hover:bg-[#D4881A] text-white font-bold px-10 py-4 rounded-2xl text-lg transition-all shadow-lg shadow-orange-100">
        تسوق الآن
      </a>
    </div>
  @endif
</div>
@endsection
