@extends('layouts.app')
@section('title', 'المتجر — WarmConcierge')

@push('styles')
<style>
  .product-card:hover { transform: translateY(-3px); box-shadow: 0 12px 30px -5px rgba(0,0,0,.12); }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-6 py-12">

  {{-- Header --}}
  <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-8">
    <div>
      <h1 class="text-3xl font-black text-gray-800">المتجر</h1>
      <p class="text-gray-500 mt-1">قطع غيار معتمدة لجميع احتياجات منزلك</p>
    </div>
    <div class="flex items-center gap-4">
      <form method="GET" action="{{ route('store.index') }}" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}"
          placeholder="ابحث عن قطعة غيار..."
          class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#F5A623] outline-none bg-gray-50 w-64">
        <button type="submit" class="bg-[#F5A623] text-white px-4 py-2.5 rounded-xl">🔍</button>
      </form>
      <a href="{{ route('store.cart') }}" class="relative bg-white border border-gray-200 rounded-xl p-2.5 hover:border-[#F5A623] transition-all">
        🛒
        @php $cartCount = count(session('cart', [])); @endphp
        @if($cartCount > 0)
          <span class="absolute -top-2 -left-2 bg-[#F5A623] text-white text-xs w-5 h-5 rounded-full flex items-center justify-center font-bold">{{ $cartCount }}</span>
        @endif
      </a>
    </div>
  </div>

  <div class="flex gap-8" x-data="{ mobileFilter: false }">

    {{-- Sidebar Filter --}}
    <aside class="hidden lg:block w-64 flex-shrink-0">
      <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm sticky top-24">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-bold text-gray-800">تصفية النتائج</h3>
          <a href="{{ route('store.index') }}" class="text-xs text-[#D4881A] hover:underline">مسح</a>
        </div>

        <form method="GET" action="{{ route('store.index') }}" id="filterForm">
          @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
          @endif

          {{-- Categories --}}
          <div class="mb-6">
            <h4 class="text-sm font-bold text-gray-600 mb-3 border-b pb-2">الفئات</h4>
            <div class="space-y-2">
              @foreach($categories as $cat)
                <label class="flex items-center justify-between cursor-pointer hover:bg-gray-50 rounded-lg p-1.5">
                  <div class="flex items-center gap-2">
                    <input type="checkbox" name="category[]" value="{{ $cat->id }}"
                      {{ in_array((string)$cat->id, array_map('strval', (array)request('category', []))) ? 'checked' : '' }}
                      class="text-[#F5A623] border-gray-300 rounded"
                      onchange="document.getElementById('filterForm').submit()">
                    <span class="text-sm text-gray-700">{{ $cat->icon }} {{ $cat->name_ar }}</span>
                  </div>
                </label>
              @endforeach
            </div>
          </div>

          {{-- In Stock --}}
          <div class="mb-6">
            <h4 class="text-sm font-bold text-gray-600 mb-3 border-b pb-2">التوفر</h4>
            <label class="flex items-center gap-3 cursor-pointer">
              <div x-data="{ on: {{ request('in_stock') ? 'true' : 'false' }} }" class="relative">
                <input type="hidden" name="in_stock" :value="on ? '1' : ''">
                <button type="button" @click="on=!on; $nextTick(()=>document.getElementById('filterForm').submit())"
                  :class="on ? 'bg-[#F5A623]' : 'bg-gray-200'"
                  class="w-11 h-6 rounded-full transition-colors relative">
                  <span :class="on ? 'translate-x-5' : 'translate-x-1'" class="absolute top-1 w-4 h-4 bg-white rounded-full transition-transform shadow"></span>
                </button>
              </div>
              <span class="text-sm text-gray-700">متوفر في المخزن فقط</span>
            </label>
          </div>
        </form>
      </div>
    </aside>

    {{-- Products Grid --}}
    <div class="flex-1 min-w-0">

      {{-- Results count --}}
      <div class="flex items-center justify-between mb-6">
        <p class="text-gray-500 text-sm">{{ $products->total() }} منتج</p>
      </div>

      @if($products->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          @foreach($products as $product)
            <x-product-card :product="$product" />
          @endforeach
        </div>

        <div class="mt-10">{{ $products->withQueryString()->links() }}</div>
      @else
        <div class="text-center py-20">
          <div class="text-6xl mb-4">🔍</div>
          <h3 class="text-xl font-bold text-gray-700 mb-2">لم يتم العثور على منتجات</h3>
          <p class="text-gray-400 mb-6">جرب تغيير مصطلح البحث أو تصفية مختلفة</p>
          <a href="{{ route('store.index') }}" class="bg-[#F5A623] text-white font-bold px-6 py-3 rounded-xl hover:bg-[#D4881A] transition-all">عرض جميع المنتجات</a>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
