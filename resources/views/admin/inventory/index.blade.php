@extends('layouts.admin')
@section('title', 'المخزون')

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-black text-gray-800">المخزون</h1>
    <button class="bg-[#F5A623] hover:bg-[#D4881A] text-white font-bold px-5 py-2.5 rounded-xl text-sm transition-all">
      + إضافة منتج
    </button>
  </div>

  {{-- Stats --}}
  <div class="grid grid-cols-3 gap-4">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
      <div class="text-3xl font-black text-gray-800">{{ $stats['total'] }}</div>
      <div class="text-sm text-gray-500 mt-1">إجمالي المنتجات</div>
    </div>
    <div class="bg-red-50 border border-red-200 rounded-2xl p-5">
      <div class="text-3xl font-black text-red-600">{{ $stats['out_of_stock'] }}</div>
      <div class="text-sm text-red-500 mt-1">نفذ المخزون</div>
    </div>
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
      <div class="text-3xl font-black text-amber-600">{{ $stats['low_stock'] }}</div>
      <div class="text-sm text-amber-500 mt-1">مخزون منخفض</div>
    </div>
  </div>

  {{-- Table --}}
  <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
          <tr>
            <th class="py-4 px-5 text-right font-bold text-gray-600">المنتج</th>
            <th class="py-4 px-5 text-right font-bold text-gray-600">الفئة</th>
            <th class="py-4 px-5 text-right font-bold text-gray-600">السعر</th>
            <th class="py-4 px-5 text-center font-bold text-gray-600">المخزون</th>
            <th class="py-4 px-5 text-center font-bold text-gray-600">الحالة</th>
            <th class="py-4 px-5 text-center font-bold text-gray-600">إجراء</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          @forelse($products as $product)
            <tr class="hover:bg-gray-50 transition-colors {{ $product->stock === 0 ? 'bg-red-50/30' : ($product->stock < 5 ? 'bg-amber-50/30' : '') }}">
              <td class="py-4 px-5">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center text-lg flex-shrink-0">📦</div>
                  <div>
                    <div class="font-bold text-gray-800">{{ $product->name_ar }}</div>
                    <div class="text-xs text-gray-400">{{ $product->sku }} · {{ $product->brand }}</div>
                  </div>
                </div>
              </td>
              <td class="py-4 px-5 text-gray-600">{{ $product->category?->name_ar }}</td>
              <td class="py-4 px-5 font-bold text-[#D4881A]">{{ number_format($product->price) }} ر.س</td>
              <td class="py-4 px-5 text-center">
                <span class="font-bold {{ $product->stock === 0 ? 'text-red-600' : ($product->stock < 5 ? 'text-amber-600' : 'text-gray-800') }}">
                  {{ $product->stock }}
                </span>
              </td>
              <td class="py-4 px-5 text-center">
                @if($product->stock === 0)
                  <span class="bg-red-100 text-red-600 text-xs font-bold px-3 py-1 rounded-full">نفذ</span>
                @elseif($product->stock < 5)
                  <span class="bg-amber-100 text-amber-600 text-xs font-bold px-3 py-1 rounded-full">منخفض</span>
                @else
                  <span class="bg-green-100 text-green-600 text-xs font-bold px-3 py-1 rounded-full">متوفر</span>
                @endif
              </td>
              <td class="py-4 px-5 text-center">
                <div class="flex items-center justify-center gap-2">
                  <button class="text-xs bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg font-medium transition-all">تعديل</button>
                  <button class="text-xs bg-red-50 text-red-500 hover:bg-red-100 px-3 py-1.5 rounded-lg font-medium transition-all">حذف</button>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="py-16 text-center">
                <div class="text-4xl mb-3">📦</div>
                <p class="text-gray-400">لا توجد منتجات بعد</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if(method_exists($products, 'links'))
      <div class="px-6 py-4 border-t border-gray-50">{{ $products->links() }}</div>
    @endif
  </div>
</div>
@endsection
