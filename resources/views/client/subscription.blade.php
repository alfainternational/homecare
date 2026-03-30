@extends('layouts.dashboard')
@section('title', 'اشتراكي')

@section('content')
<div class="space-y-8">

  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-black text-gray-800">اشتراكي</h1>
    <a href="{{ route('pricing') }}" class="bg-[#F5A623] hover:bg-[#D4881A] text-white font-bold px-5 py-2.5 rounded-xl text-sm transition-all">ترقية الباقة</a>
  </div>

  @php
    $sub = $subscription;
    $plan = $sub?->plan;
    $visitsUsed = $sub?->visits_used ?? 0;
    $visitsTotal = $sub?->visits_total ?? 0;
    $visitsRemaining = $sub ? $sub->visitsRemaining() : 0;
    $percent = $visitsTotal > 0 ? round(($visitsUsed / $visitsTotal) * 100) : 0;
  @endphp

  @if($sub && $plan)
    {{-- Current Plan Card --}}
    <div class="bg-white rounded-3xl border-2 border-[#F5A623] p-8 shadow-lg shadow-orange-50">
      <div class="flex flex-col md:flex-row gap-8 items-center md:items-start">

        {{-- Circular progress --}}
        <div class="relative w-36 h-36 flex-shrink-0">
          <svg class="w-36 h-36 -rotate-90" viewBox="0 0 120 120">
            <circle cx="60" cy="60" r="52" fill="none" stroke="#fef3c7" stroke-width="10"/>
            <circle cx="60" cy="60" r="52" fill="none" stroke="#F5A623" stroke-width="10"
              stroke-dasharray="{{ round(2 * 3.14159 * 52) }}"
              stroke-dashoffset="{{ round(2 * 3.14159 * 52 * (1 - $percent/100)) }}"
              stroke-linecap="round"/>
          </svg>
          <div class="absolute inset-0 flex flex-col items-center justify-center">
            <span class="text-3xl font-black text-[#D4881A]">{{ $visitsRemaining }}</span>
            <span class="text-xs text-gray-500">زيارة متبقية</span>
          </div>
        </div>

        {{-- Info --}}
        <div class="flex-1 text-center md:text-right">
          <div class="flex items-center gap-3 justify-center md:justify-start mb-2">
            <h2 class="text-3xl font-black text-gray-800">{{ $plan->name_ar }}</h2>
            <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">نشط</span>
          </div>
          <p class="text-gray-500 text-sm mb-4">{{ $visitsUsed }} من {{ $visitsTotal }} زيارة مستخدمة هذا العام</p>

          {{-- Progress bar --}}
          <div class="bg-gray-100 rounded-full h-3 mb-4 max-w-sm mx-auto md:mx-0">
            <div class="bg-[#F5A623] h-3 rounded-full transition-all" style="width: {{ $percent }}%"></div>
          </div>

          <div class="flex flex-wrap gap-6 text-sm justify-center md:justify-start mb-6">
            <div><span class="text-gray-400">تاريخ البدء:</span> <strong>{{ $sub->starts_at?->format('d/m/Y') }}</strong></div>
            <div><span class="text-gray-400">تاريخ الانتهاء:</span> <strong class="{{ $sub->ends_at?->diffInDays() < 30 ? 'text-red-600' : '' }}">{{ $sub->ends_at?->format('d/m/Y') }}</strong></div>
            <div><span class="text-gray-400">السعر:</span> <strong>{{ number_format($plan->price) }} ر.س/شهر</strong></div>
          </div>

          <div class="flex gap-3 flex-wrap justify-center md:justify-start">
            <a href="{{ route('pricing') }}" class="bg-[#F5A623] hover:bg-[#D4881A] text-white font-bold px-6 py-3 rounded-xl text-sm transition-all">جدّد الآن</a>
            <a href="{{ route('pricing') }}" class="border border-gray-200 text-gray-600 font-medium px-6 py-3 rounded-xl text-sm hover:border-[#F5A623] hover:text-[#D4881A] transition-all">ترقية الباقة</a>
          </div>
        </div>
      </div>

      {{-- Features --}}
      @if($plan->features)
        @php $features = is_string($plan->features) ? json_decode($plan->features, true) : $plan->features; @endphp
        <div class="mt-6 pt-6 border-t border-orange-100">
          <p class="text-sm font-bold text-gray-700 mb-3">مزايا باقتك:</p>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
            @foreach($features as $f)
              <div class="flex items-center gap-2 text-sm text-gray-600">
                <span class="text-green-500 font-bold">✓</span> {{ $f }}
              </div>
            @endforeach
          </div>
        </div>
      @endif
    </div>
  @else
    {{-- No subscription --}}
    <div class="bg-white rounded-3xl border-2 border-dashed border-gray-200 p-12 text-center">
      <div class="text-6xl mb-4">📋</div>
      <h3 class="text-xl font-bold text-gray-700 mb-2">لا يوجد اشتراك نشط</h3>
      <p class="text-gray-400 mb-6">اشترك الآن واستمتع بخدمات الصيانة المنزلية الاحترافية</p>
      <a href="{{ route('pricing') }}" class="bg-[#F5A623] hover:bg-[#D4881A] text-white font-bold px-8 py-3 rounded-xl transition-all">اختر باقتك</a>
    </div>
  @endif

  {{-- Plans comparison table --}}
  <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
    <h2 class="text-xl font-black text-gray-800 mb-6">مقارنة الباقات</h2>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b border-gray-100">
            <th class="text-right py-3 px-4 font-bold text-gray-600 w-1/3">الميزة</th>
            <th class="text-center py-3 px-4 font-bold text-gray-600">الأساسية<br><span class="text-[#D4881A] font-black">199 ر.س</span></th>
            <th class="text-center py-3 px-4 font-bold text-[#D4881A] bg-orange-50 rounded-xl">المتقدمة<br><span class="font-black">349 ر.س</span></th>
            <th class="text-center py-3 px-4 font-bold text-gray-600">الشاملة<br><span class="text-[#D4881A] font-black">599 ر.س</span></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          @foreach([
            ['زيارات شهرية','2','4','غير محدود'],
            ['دعم 24/7','✗','✓','✓'],
            ['صيانة مكيف','✗','✓','✓'],
            ['أولوية الحجز الطارئ','✗','✓','✓'],
            ['خصم 20% قطع الغيار','✗','✓','✓'],
            ['مدير حساب خاص','✗','✗','✓'],
            ['تقرير صحة منزلي','✗','✗','✓'],
          ] as $row)
            <tr>
              <td class="py-3 px-4 font-medium text-gray-700">{{ $row[0] }}</td>
              <td class="py-3 px-4 text-center {{ $row[1]==='✗' ? 'text-gray-300' : ($row[1]==='✓' ? 'text-green-500 font-bold' : 'text-gray-700 font-semibold') }}">{{ $row[1] }}</td>
              <td class="py-3 px-4 text-center bg-orange-50 {{ $row[2]==='✗' ? 'text-gray-300' : ($row[2]==='✓' ? 'text-green-500 font-bold' : 'text-[#D4881A] font-black') }}">{{ $row[2] }}</td>
              <td class="py-3 px-4 text-center {{ $row[3]==='✗' ? 'text-gray-300' : ($row[3]==='✓' ? 'text-green-500 font-bold' : 'text-gray-700 font-semibold') }}">{{ $row[3] }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
