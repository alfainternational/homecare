@props(['product'])

<div class="product-card bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm" style="transition: transform .2s, box-shadow .2s;">

  {{-- Image --}}
  <div class="relative bg-gray-100 h-48 flex items-center justify-center">
    @if($product->image)
      <img src="{{ $product->image_url }}" alt="{{ $product->name_ar }}" class="w-full h-full object-cover">
    @else
      <div class="text-5xl opacity-30">📦</div>
    @endif

    @if($product->is_featured)
      <span class="absolute top-3 right-3 bg-[#F5A623] text-white text-xs font-bold px-2 py-1 rounded-lg">موصى به</span>
    @endif

    @if($product->stock === 0)
      <div class="absolute inset-0 bg-white/70 flex items-center justify-center">
        <span class="bg-red-100 text-red-600 font-bold text-sm px-4 py-2 rounded-xl">نفذ المخزون</span>
      </div>
    @elseif($product->stock <= 5)
      <span class="absolute top-3 left-3 bg-amber-100 text-amber-700 text-xs px-2 py-1 rounded-lg">{{ $product->stock }} متبقية فقط</span>
    @endif
  </div>

  <div class="p-5">
    <p class="text-xs text-gray-400 mb-1">{{ $product->brand }}</p>
    <h3 class="font-bold text-gray-800 mb-3 leading-snug line-clamp-2">{{ $product->name_ar }}</h3>
    <div class="flex items-center justify-between mb-4">
      <span class="text-2xl font-black text-[#D4881A]">{{ number_format($product->price) }} <span class="text-sm font-normal">ر.س</span></span>
      <span class="text-xs {{ $product->stock > 0 ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50' }} px-2 py-1 rounded-full font-medium">
        {{ $product->stock > 0 ? 'متوفر' : 'نفذ' }}
      </span>
    </div>
    <div class="flex gap-2">
      @if($product->stock > 0)
        <form method="POST" action="{{ route('store.add-cart', $product) }}" class="flex-1">
          @csrf
          <button type="submit" class="w-full bg-[#F5A623] hover:bg-[#D4881A] text-white font-bold py-2.5 rounded-xl text-sm transition-all">
            أضف للسلة 🛒
          </button>
        </form>
      @endif
      <a href="{{ route('store.show', $product) }}" class="flex-1 border border-gray-200 text-gray-600 font-medium py-2.5 rounded-xl text-sm text-center hover:border-[#F5A623] hover:text-[#D4881A] transition-all">
        التفاصيل
      </a>
    </div>
  </div>
</div>
