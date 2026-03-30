@extends('layouts.app')
@section('title', 'إتمام الشراء — WarmConcierge')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-12" x-data="{ payMethod: 'card' }">
  <h1 class="text-3xl font-black text-gray-800 mb-8">إتمام الشراء</h1>

  @if(count($cart) === 0)
    <div class="text-center py-20">
      <p class="text-gray-400 mb-4">لا توجد منتجات في السلة</p>
      <a href="{{ route('store.index') }}" class="text-[#D4881A] underline font-medium">العودة للمتجر</a>
    </div>
  @else
    @php
      $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
      $tax = $subtotal * 0.15;
      $grand = $subtotal + $tax;
    @endphp

    <form method="POST" action="{{ route('store.checkout.post') }}">
      @csrf

    <div class="flex flex-col lg:flex-row gap-8">

      {{-- Form --}}
      <div class="flex-1 space-y-6">

        {{-- Delivery Address --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
          <h3 class="font-black text-gray-800 text-lg mb-5 flex items-center gap-2">📍 عنوان التوصيل</h3>
          @error('name') <p class="text-red-500 text-sm mb-3">{{ $message }}</p> @enderror
          @error('phone') <p class="text-red-500 text-sm mb-3">{{ $message }}</p> @enderror
          @error('address') <p class="text-red-500 text-sm mb-3">{{ $message }}</p> @enderror
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">الاسم الكامل</label>
              <input type="text" name="name" value="{{ old('name', auth()->user()?->name) }}"
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#F5A623] outline-none bg-gray-50 focus:bg-white text-right">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">رقم الجوال</label>
              <input type="tel" name="phone" value="{{ old('phone', auth()->user()?->phone) }}"
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#F5A623] outline-none bg-gray-50 focus:bg-white" dir="ltr">
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 mb-2">العنوان التفصيلي</label>
              <input type="text" name="address" value="{{ old('address') }}" placeholder="الشارع، الحي، المدينة"
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#F5A623] outline-none bg-gray-50 focus:bg-white text-right">
            </div>
          </div>
        </div>

        {{-- Payment Methods --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
          <h3 class="font-black text-gray-800 text-lg mb-5">💳 طريقة الدفع</h3>
          @error('payment_method') <p class="text-red-500 text-sm mb-3">{{ $message }}</p> @enderror
          <div class="space-y-3">

            @foreach([
              ['card','بطاقة ائتمانية / مدى','💳','ادفع بأمان باستخدام بطاقتك البنكية'],
              ['bank','تحويل بنكي','🏦','تحويل مباشر لحسابنا البنكي'],
              ['tabby','تابي — 4 أقساط','📱','قسّم فاتورتك على 4 دفعات بدون فوائد'],
              ['tamara','تمارا — ادفع لاحقاً','🌙','اشترِ الآن وادفع بعد 30 يوماً'],
              ['wallet','محفظتي','👛','الرصيد المتاح: ' . (auth()->user()?->wallet?->balance ?? 0) . ' ر.س'],
            ] as $m)
              <label class="flex items-center gap-4 p-4 border-2 rounded-2xl cursor-pointer transition-all"
                :class="payMethod === '{{ $m[0] }}' ? 'border-[#F5A623] bg-orange-50' : 'border-gray-100 hover:border-gray-200'">
                <input type="radio" name="payment_method" value="{{ $m[0] }}" x-model="payMethod" class="text-[#F5A623]">
                <span class="text-2xl">{{ $m[2] }}</span>
                <div>
                  <p class="font-bold text-gray-800 text-sm">{{ $m[1] }}</p>
                  @if(in_array($m[0], ['tabby','tamara']))
                    <p class="text-xs text-gray-400">
                      @if($m[0] === 'tabby')
                        {{ number_format($grand / 4, 2) }} ر.س × 4 أقساط
                      @else
                        {{ number_format($grand, 2) }} ر.س بعد 30 يوم
                      @endif
                    </p>
                  @else
                    <p class="text-xs text-gray-400">{{ $m[3] }}</p>
                  @endif
                </div>
              </label>
            @endforeach
          </div>
        </div>
      </div>

      {{-- Order Summary --}}
      <div class="w-full lg:w-80 flex-shrink-0">
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 sticky top-24">
          <h3 class="text-xl font-black text-gray-800 mb-5">ملخص الطلب</h3>

          <div class="space-y-3 mb-5">
            @foreach($cart as $item)
              <div class="flex justify-between items-center text-sm">
                <span class="text-gray-500 truncate flex-1 ml-2">{{ $item['name'] }} ×{{ $item['quantity'] }}</span>
                <span class="font-semibold text-gray-700 flex-shrink-0">{{ number_format($item['price'] * $item['quantity']) }} ر.س</span>
              </div>
            @endforeach
          </div>

          <div class="border-t border-gray-100 pt-4 space-y-2 mb-5">
            <div class="flex justify-between text-sm">
              <span class="text-gray-500">المجموع</span>
              <span class="font-semibold">{{ number_format($subtotal, 2) }} ر.س</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-gray-500">الضريبة (15%)</span>
              <span class="font-semibold">{{ number_format($tax, 2) }} ر.س</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-gray-500">التوصيل</span>
              <span class="font-semibold text-green-600">مجاناً</span>
            </div>
            <div class="flex justify-between pt-2 border-t border-gray-100">
              <span class="font-black text-gray-800">الإجمالي</span>
              <span class="font-black text-xl text-[#D4881A]">{{ number_format($grand, 2) }} ر.س</span>
            </div>
          </div>

          <button type="submit" class="w-full bg-[#F5A623] hover:bg-[#D4881A] text-white font-black py-4 rounded-2xl text-lg transition-all shadow-lg shadow-orange-100">
            تأكيد الدفع 🔒
          </button>

          <p class="text-center text-xs text-gray-400 mt-3 flex items-center justify-center gap-1">
            <span>🛡️</span> جميع المعاملات مشفرة وآمنة
          </p>
        </div>
      </div>
    </div>

    </form>
  @endif
</div>
@endsection
