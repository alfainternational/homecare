@extends('layouts.admin')
@section('title', 'إدارة الاشتراكات')

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-black text-gray-800">إدارة الاشتراكات</h1>
  </div>

  @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-2xl p-4 flex items-center gap-3">
      <span class="text-green-500">✓</span>
      <p class="text-green-700 font-medium">{{ session('success') }}</p>
    </div>
  @endif

  {{-- Stats --}}
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-green-50 border border-green-200 rounded-2xl p-5">
      <div class="text-3xl font-black text-green-700">{{ $stats['active'] }}</div>
      <div class="text-sm text-green-600 font-medium mt-1">نشط</div>
    </div>
    <div class="bg-red-50 border border-red-200 rounded-2xl p-5">
      <div class="text-3xl font-black text-red-600">{{ $stats['expired'] }}</div>
      <div class="text-sm text-red-500 font-medium mt-1">منتهي</div>
    </div>
    <div class="bg-gray-50 border border-gray-200 rounded-2xl p-5">
      <div class="text-3xl font-black text-gray-600">{{ $stats['suspended'] }}</div>
      <div class="text-sm text-gray-500 font-medium mt-1">معلق</div>
    </div>
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
      <div class="text-3xl font-black text-amber-600">{{ $stats['expiring_soon'] }}</div>
      <div class="text-sm text-amber-500 font-medium mt-1">تنتهي خلال 30 يوم</div>
    </div>
  </div>

  {{-- Expiring soon warning --}}
  @if($stats['expiring_soon'] > 0)
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-center gap-3">
      <span class="text-amber-500 text-xl">⚠️</span>
      <p class="text-amber-700 font-medium">{{ $stats['expiring_soon'] }} اشتراك تنتهي خلال 30 يوماً — يُنصح بالتواصل مع العملاء</p>
    </div>
  @endif

  {{-- Table --}}
  <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
          <tr>
            <th class="py-4 px-5 text-right font-bold text-gray-600">المشترك</th>
            <th class="py-4 px-5 text-right font-bold text-gray-600">الباقة</th>
            <th class="py-4 px-5 text-right font-bold text-gray-600">البدء</th>
            <th class="py-4 px-5 text-right font-bold text-gray-600">الانتهاء</th>
            <th class="py-4 px-5 text-center font-bold text-gray-600">الزيارات</th>
            <th class="py-4 px-5 text-center font-bold text-gray-600">الحالة</th>
            <th class="py-4 px-5 text-center font-bold text-gray-600">إجراء</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          @forelse($subscriptions as $sub)
            <tr class="hover:bg-gray-50 transition-colors {{ optional($sub->ends_at)->diffInDays(now()) <= 30 && $sub->status === 'active' ? 'bg-amber-50/30' : '' }}">
              <td class="py-4 px-5">
                <div class="font-bold text-gray-800">{{ $sub->user?->name }}</div>
                <div class="text-xs text-gray-400">{{ $sub->user?->email }}</div>
              </td>
              <td class="py-4 px-5">
                <div class="font-semibold text-gray-700">{{ $sub->plan?->name_ar }}</div>
                <div class="text-xs text-gray-400">{{ number_format($sub->plan?->price ?? 0) }} ر.س/شهر</div>
              </td>
              <td class="py-4 px-5 text-gray-600">{{ $sub->starts_at?->format('d/m/Y') }}</td>
              <td class="py-4 px-5">
                <span class="{{ optional($sub->ends_at)->diffInDays(now()) <= 30 ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                  {{ $sub->ends_at?->format('d/m/Y') }}
                </span>
              </td>
              <td class="py-4 px-5 text-center">
                <div class="font-bold text-gray-800">{{ $sub->visits_total - $sub->visits_used }} / {{ $sub->visits_total }}</div>
                <div class="text-xs text-gray-400">متبقية / إجمالي</div>
              </td>
              <td class="py-4 px-5 text-center">
                @php
                  $statusMap = ['active'=>['نشط','bg-green-100 text-green-700'],'expired'=>['منتهي','bg-red-100 text-red-600'],'suspended'=>['معلق','bg-gray-100 text-gray-600']];
                  [$label,$cls] = $statusMap[$sub->status] ?? ['غير معروف','bg-gray-100 text-gray-600'];
                @endphp
                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $cls }}">{{ $label }}</span>
              </td>
              <td class="py-4 px-5 text-center" x-data="{ open: false }">
                <div class="relative">
                  <button @click="open=!open" class="text-gray-400 hover:text-gray-600 px-3 py-1.5 rounded-xl hover:bg-gray-100 font-bold">⋮</button>
                  <div x-show="open" @click.outside="open=false" x-cloak
                    class="absolute left-0 top-8 bg-white border border-gray-100 rounded-2xl shadow-xl z-10 w-48 py-2">
                    <form method="POST" action="{{ route('admin.subscriptions.grant-visit', $sub) }}">
                      @csrf
                      <button type="submit" class="w-full text-right px-4 py-2.5 text-sm text-gray-700 hover:bg-orange-50 hover:text-[#D4881A]">
                        🎁 منح زيارة مجانية
                      </button>
                    </form>
                  </div>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="py-16 text-center">
                <div class="text-4xl mb-3">📋</div>
                <p class="text-gray-400">لا توجد اشتراكات بعد</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if(method_exists($subscriptions, 'links'))
      <div class="px-6 py-4 border-t border-gray-50">{{ $subscriptions->links() }}</div>
    @endif
  </div>
</div>
@endsection
