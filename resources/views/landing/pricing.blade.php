@extends('layouts.app')
@section('title', 'الأسعار والباقات — WarmConcierge')

@section('content')
<div class="py-16 bg-gray-50 min-h-screen">
  <div class="max-w-6xl mx-auto px-6">
    <h1 class="text-4xl font-black text-gray-900 text-center mb-4">خطط اشتراك <span class="text-[#F5A623]">مرنة</span></h1>
    <p class="text-gray-500 text-center text-lg mb-12">اختر الباقة التي تناسب منزلك</p>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      @forelse($plans as $plan)
        <div class="bg-white rounded-3xl p-8 border-2 {{ $plan->is_featured ? 'border-[#F5A623] shadow-xl' : 'border-gray-100 shadow-sm' }} relative">
          @if($plan->is_featured)<div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-[#F5A623] text-white text-xs font-black px-5 py-1.5 rounded-full">الأكثر طلباً</div>@endif
          <h3 class="text-2xl font-black text-gray-800 text-center mb-4">{{ $plan->name_ar }}</h3>
          <div class="text-center mb-6"><span class="text-5xl font-black text-[#D4881A]">{{ number_format($plan->price) }}</span><span class="text-gray-400 text-sm"> ر.س/شهر</span></div>
          @php $features = is_string($plan->features) ? json_decode($plan->features, true) : ($plan->features ?? []); @endphp
          <ul class="space-y-2 mb-8">@foreach($features as $f)<li class="flex items-center gap-2 text-sm text-gray-700"><span class="text-green-500 font-bold">✓</span>{{ $f }}</li>@endforeach</ul>
          <a href="{{ route('register') }}" class="block text-center py-3 rounded-2xl font-bold text-sm {{ $plan->is_featured ? 'bg-[#F5A623] text-white' : 'border-2 border-gray-200 text-gray-700' }} hover:bg-[#D4881A] hover:text-white transition-all">ابدأ الآن</a>
        </div>
      @empty
        <div class="col-span-3 text-center py-12 text-gray-400">لا توجد باقات متاحة حالياً</div>
      @endforelse
    </div>
  </div>
</div>
@endsection
