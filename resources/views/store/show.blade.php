@extends('layouts.app')
@section('title', $product->name_ar . ' — WarmConcierge')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-12">
  {{-- Breadcrumb --}}
  <nav class="text-sm text-gray-400 mb-8 flex items-center gap-2">
    <a href="{{ route('store.index') }}" class="hover:text-[#D4881A]">المتجر</a>
    <span>›</span>
    <span>{{ $product->category?->name_ar }}</span>
    <span>›</span>
    <span class="text-gray-700">{{ $product->name_ar }}</span>
  </nav>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">
    {{-- Image --}}
    <div class="bg-gray-50 rounded-3xl h-96 flex items-center justify-center border border-gray-100">
      @if($product->image)
        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name_ar }}" class="max-h-80 object-contain">
      @else
        <div class="text-8xl opacity-20">📦</div>
      @endif
    </div>

    {{-- Details --}}
    <div>
      <p class="text-sm text-gray-400 mb-1">{{ $product->brand }}</p>
      <h1 class="text-3xl font-black text-gray-800 mb-4">{{ $product->name_ar }}</h1>
      <div class="flex items-center gap-4 mb-6">
        <span class="text-4xl font-black text-[#D4881A]">{{ number_format($product->price) }} ر.س</span>
        <span class="text-sm {{ $product->stock > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }} px-3 py-1 rounded-full font-medium">
          {{ $product->stock > 0 ? 'متوفر' : 'نفذ المخزون' }}
        </span>
      </div>

      @if($product->description_ar)
        <p class="text-gray-600 leading-relaxed mb-8">{{ $product->description_ar }}</p>
      @endif

      @if($product->stock > 0)
        <form method="POST" action="{{ route('store.add-cart', $product) }}">
          @csrf
          <div class="flex gap-4">
            <button type="submit" class="flex-1 bg-[#F5A623] hover:bg-[#D4881A] text-white font-black py-4 rounded-2xl text-lg transition-all shadow-lg shadow-orange-100">
              أضف للسلة 🛒
            </button>
            <a href="{{ route('store.cart') }}" class="border-2 border-gray-200 text-gray-700 font-bold px-6 py-4 rounded-2xl hover:border-[#F5A623] transition-all">
              عرض السلة
            </a>
          </div>
        </form>
      @endif
    </div>
  </div>

  {{-- Related --}}
  @if($related->count())
    <div>
      <h2 class="text-2xl font-black text-gray-800 mb-6">منتجات ذات صلة</h2>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($related as $r)
          <a href="{{ route('store.show', $r) }}" class="bg-white rounded-2xl border border-gray-100 p-4 hover:border-[#F5A623] transition-all shadow-sm">
            <div class="h-24 bg-gray-50 rounded-xl flex items-center justify-center mb-3 text-3xl">📦</div>
            <p class="font-bold text-gray-800 text-sm line-clamp-2">{{ $r->name_ar }}</p>
            <p class="text-[#D4881A] font-black mt-1">{{ number_format($r->price) }} ر.س</p>
          </a>
        @endforeach
      </div>
    </div>
  @endif
</div>
@endsection
