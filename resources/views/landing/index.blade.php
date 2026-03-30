@extends('layouts.app')

@section('title', 'WarmConcierge — صيانة منزلك بضغطة واحدة')

@push('styles')
<style>
  .hero-pattern { background-color: #fff8f0; background-image: radial-gradient(#f5a62322 1px, transparent 1px); background-size: 24px 24px; }
  .floating-card { animation: float 3s ease-in-out infinite; }
  @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
  .step-line::after { content:''; position:absolute; top:50%; left:-50%; width:100%; height:2px; background:#f5a623; opacity:.4; }
  .plan-card { transition: transform .2s, box-shadow .2s; }
  .plan-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px -10px rgba(0,0,0,.15); }
  [x-cloak] { display:none !important; }
</style>
@endpush

@section('content')

{{-- ===== HERO ===== --}}
<section class="hero-pattern min-h-screen flex items-center py-20">
  <div class="max-w-7xl mx-auto px-6 w-full">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

      {{-- Text --}}
      <div class="order-2 lg:order-1 text-right">
        <span class="inline-block bg-orange-100 text-orange-700 text-sm font-semibold px-4 py-1.5 rounded-full mb-6">
          الاختيار الأول في المملكة
        </span>
        <h1 class="text-5xl lg:text-6xl font-black text-gray-900 leading-tight mb-6">
          صيانة منزلك<br>
          <span class="text-[#F5A623]">بضغطة واحدة</span>
        </h1>
        <p class="text-xl text-gray-500 mb-8 leading-relaxed">
          استمتع براحة البال مع خدمات الصيانة المنزلية الاحترافية. من السباكة إلى الكهرباء، فريقنا المتخصص جاهز لخدمتك في أي وقت.
        </p>
        <div class="flex gap-4 mb-8 flex-wrap">
          <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-[#F5A623] hover:bg-[#D4881A] text-white font-bold px-8 py-4 rounded-2xl text-lg transition-all shadow-lg shadow-orange-200">
            اشترك الآن
            <svg class="w-5 h-5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
          </a>
          <a href="{{ route('pricing') }}" class="inline-flex items-center gap-2 border-2 border-[#F5A623] text-[#D4881A] font-bold px-8 py-4 rounded-2xl text-lg hover:bg-orange-50 transition-all">
            احجز زيارة تجريبية
          </a>
        </div>
        <p class="text-sm text-gray-400 flex items-center gap-2">
          <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
          أكثر من <strong>500 منزل</strong> يثقون بنا في عرعر
        </p>
      </div>

      {{-- Visual --}}
      <div class="order-1 lg:order-2 flex justify-center relative">
        <div class="relative w-80 h-80 lg:w-96 lg:h-96">
          {{-- Orange circle --}}
          <div class="absolute inset-0 bg-[#F5A623] rounded-full flex flex-col items-center justify-center text-white">
            <div class="text-2xl font-black">WarmConcierge</div>
            <div class="text-sm opacity-80 mt-1">SERVICE</div>
            <div class="mt-3 text-xs bg-white/20 px-4 py-1 rounded-full">SAFE FOR HOME</div>
          </div>
          {{-- Floating status card --}}
          <div class="floating-card absolute -bottom-6 -right-6 bg-white rounded-2xl shadow-2xl p-4 min-w-52">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600 text-lg">✓</div>
              <div>
                <div class="font-bold text-gray-800 text-sm">طلب صيانة</div>
                <div class="text-xs text-green-600 font-medium">جاري التنفيذ</div>
              </div>
            </div>
          </div>
          {{-- Rating card --}}
          <div class="floating-card absolute -top-4 -left-4 bg-white rounded-2xl shadow-xl p-3" style="animation-delay:.5s">
            <div class="flex items-center gap-1 text-yellow-400">⭐⭐⭐⭐⭐</div>
            <div class="text-xs font-bold text-gray-700 mt-1">4.9 رضا العملاء</div>
          </div>
          {{-- Trust badge --}}
          <div class="absolute top-4 -right-8 bg-white border border-orange-100 rounded-xl shadow-md px-3 py-2">
            <div class="flex items-center gap-2">
              <span class="text-green-500">🛡️</span>
              <div class="text-xs">
                <div class="font-bold text-gray-700">خدمة موثوقة</div>
                <div class="text-gray-400">أكثر من 500 صديق</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ===== TRUST BAR ===== --}}
<section class="bg-gray-50 py-12 border-y border-gray-100">
  <div class="max-w-7xl mx-auto px-6">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center mb-10">
      <div>
        <div class="text-4xl font-black text-[#D4881A]">+500</div>
        <div class="text-gray-500 text-sm mt-1">منزل مشترك</div>
      </div>
      <div>
        <div class="text-4xl font-black text-[#D4881A]">98%</div>
        <div class="text-gray-500 text-sm mt-1">رضا العملاء</div>
      </div>
      <div>
        <div class="text-4xl font-black text-[#D4881A]">24/7</div>
        <div class="text-gray-500 text-sm mt-1">دعم فني متاح</div>
      </div>
      <div>
        <div class="text-4xl font-black text-[#D4881A]">2 ساعة</div>
        <div class="text-gray-500 text-sm mt-1">متوسط وقت الاستجابة</div>
      </div>
    </div>
    <div class="flex flex-wrap items-center justify-between gap-6 pt-8 border-t border-gray-200">
      <div class="flex items-center gap-6 flex-wrap">
        <span class="text-gray-400 text-sm">وسائل الدفع:</span>
        @foreach(['تمارا','تابي','VISA','مدى','ماستركارد'] as $pay)
          <span class="bg-white border border-gray-200 px-3 py-1 rounded-lg text-sm font-semibold text-gray-600 shadow-sm">{{ $pay }}</span>
        @endforeach
      </div>
      <div class="flex items-center gap-4 flex-wrap">
        <span class="flex items-center gap-2 text-sm text-gray-600"><span class="text-green-500">🛡️</span> ضمان جودة العمل</span>
        <span class="flex items-center gap-2 text-sm text-gray-600"><span>⏱️</span> استجابة خلال ساعتين</span>
        <span class="flex items-center gap-2 text-sm text-gray-600"><span>🎖️</span> فنيون معتمدون</span>
      </div>
    </div>
  </div>
</section>

{{-- ===== HOW IT WORKS ===== --}}
<section class="py-24 bg-white" id="how-it-works">
  <div class="max-w-7xl mx-auto px-6">
    <h2 class="text-4xl font-black text-gray-900 text-center mb-4">كيف يعمل <span class="text-[#F5A623]">وورم كونسيرج؟</span></h2>
    <p class="text-gray-500 text-center text-lg mb-16">أربع خطوات بسيطة للحصول على خدمة احترافية</p>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
      @foreach([
        ['1','إنشاء حساب','سجل بياناتك ومعلومات منزلك في دقيقة واحدة','👤','bg-blue-50 text-blue-600'],
        ['2','اطلب الخدمة','اختر نوع الصيانة وصف المشكلة بالكتابة أو الصوت','🔧','bg-orange-50 text-orange-600'],
        ['3','وصول الفني','فنينا المعتمد يصلك في الوقت المحدد مع جميع الأدوات','🚗','bg-green-50 text-green-600'],
        ['4','قيّم الخدمة','تأكد من جودة العمل وقيّم الفني لمساعدة العملاء الآخرين','⭐','bg-purple-50 text-purple-600'],
      ] as $step)
        <div class="flex flex-col items-center text-center group">
          <div class="relative mb-6">
            <div class="w-20 h-20 {{ $step[4] }} rounded-2xl flex items-center justify-center text-3xl group-hover:scale-110 transition-transform shadow-sm">
              {{ $step[2] }}
            </div>
            <div class="absolute -top-2 -right-2 w-7 h-7 bg-[#F5A623] text-white rounded-full flex items-center justify-center text-sm font-black">{{ $step[0] }}</div>
          </div>
          <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $step[1] }}</h3>
          <p class="text-gray-500 text-sm leading-relaxed">{{ $step[3] }}</p>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ===== PRICING ===== --}}
<section class="py-24 bg-gray-50" id="pricing" x-data="{ type: 'residential' }">
  <div class="max-w-7xl mx-auto px-6">
    <h2 class="text-4xl font-black text-gray-900 text-center mb-4">خطط اشتراك <span class="text-[#F5A623]">مرنة</span></h2>
    <p class="text-gray-500 text-center text-lg mb-10">اختر الباقة التي تناسب احتياجاتك</p>

    {{-- Toggle --}}
    <div class="flex justify-center mb-12">
      <div class="bg-white border border-gray-200 rounded-2xl p-1.5 flex gap-1 shadow-sm">
        <button @click="type='residential'" :class="type==='residential' ? 'bg-[#F5A623] text-white shadow' : 'text-gray-500 hover:bg-gray-50'" class="px-6 py-2.5 rounded-xl font-semibold transition-all text-sm">سكني</button>
        <button @click="type='commercial'" :class="type==='commercial' ? 'bg-[#F5A623] text-white shadow' : 'text-gray-500 hover:bg-gray-50'" class="px-6 py-2.5 rounded-xl font-semibold transition-all text-sm">تجاري</button>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
      @php
        $defaultPlans = [
          ['name_ar'=>'الباقة الأساسية','price'=>199,'visits'=>2,'featured'=>false,'features'=>['زيارتان شهرياً','صيانة كهرباء وسباكة','ضمان 30 يوم على العمل'],'missing'=>['دعم 24/7','أولوية الحجز','مدير حساب']],
          ['name_ar'=>'الباقة المتقدمة','price'=>349,'visits'=>4,'featured'=>true,'features'=>['4 زيارات شهرياً','صيانة المكيفات فحص+تنظيف','أولوية في الطلبات الطارئة','خصم 20% على قطع الغيار','دعم 24/7'],'missing'=>['مدير حساب']],
          ['name_ar'=>'الباقة الشاملة','price'=>599,'visits'=>null,'featured'=>false,'features'=>['زيارات غير محدودة','كافة خدمات الصيانة والترميم','مدير حساب خاص لمنزلك','أولوية قصوى 24/7','تقرير صحة منزلك ربع سنوي'],'missing'=>[]],
        ];
        $displayPlans = $plans->count() ? $plans : collect($defaultPlans);
      @endphp

      @foreach($displayPlans as $i => $plan)
        @php
          $isFeatured = is_array($plan) ? $plan['featured'] : $plan->is_featured;
          $nameAr     = is_array($plan) ? $plan['name_ar'] : $plan->name_ar;
          $price      = is_array($plan) ? $plan['price'] : $plan->price;
          $features   = is_array($plan) ? $plan['features'] : (is_string($plan->features) ? json_decode($plan->features,true) : $plan->features);
          $missing    = is_array($plan) ? ($plan['missing'] ?? []) : [];
        @endphp
        <div class="plan-card relative bg-white rounded-3xl p-8 border-2 {{ $isFeatured ? 'border-[#F5A623] shadow-2xl shadow-orange-100 scale-105' : 'border-gray-100 shadow-lg' }}">
          @if($isFeatured)
            <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-[#F5A623] text-white text-xs font-black px-6 py-2 rounded-full shadow-lg">الأكثر طلباً ⭐</div>
          @endif
          <h3 class="text-2xl font-black text-gray-800 mb-2 text-center">{{ $nameAr }}</h3>
          <div class="text-center my-6">
            <span class="text-5xl font-black text-[#D4881A]">{{ number_format($price) }}</span>
            <span class="text-gray-400 text-sm mr-1">ر.س / شهر</span>
          </div>
          <ul class="space-y-3 mb-8">
            @foreach($features as $f)
              <li class="flex items-center gap-2 text-sm text-gray-700"><span class="text-green-500 font-bold">✓</span>{{ $f }}</li>
            @endforeach
            @foreach($missing as $m)
              <li class="flex items-center gap-2 text-sm text-gray-400 line-through"><span class="text-gray-300">✗</span>{{ $m }}</li>
            @endforeach
          </ul>
          <a href="{{ route('register') }}" class="block text-center py-3.5 rounded-2xl font-bold text-sm transition-all {{ $isFeatured ? 'bg-[#F5A623] hover:bg-[#D4881A] text-white shadow-lg shadow-orange-200' : 'border-2 border-gray-200 text-gray-700 hover:border-[#F5A623] hover:text-[#D4881A]' }}">
            ابدأ الآن
          </a>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ===== TESTIMONIALS ===== --}}
<section class="py-24 bg-white">
  <div class="max-w-7xl mx-auto px-6">
    <h2 class="text-4xl font-black text-gray-900 text-center mb-4">ماذا يقول <span class="text-[#F5A623]">عملاؤنا؟</span></h2>
    <p class="text-gray-500 text-center text-lg mb-16">آراء حقيقية من عملاء راضين</p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
      @foreach([
        ['أحمد العنيزي','حي النزهة','⭐⭐⭐⭐⭐','خدمة احترافية جداً. الفني وصل في الموعد المحدد وكان ملماً بكل الخدمات. قاروا على عناء البحث عن فنيين غير موثوقين.','👤'],
        ['سارة خالد','حي الروضة','⭐⭐⭐⭐⭐','باقة الاشتراك الشهري تستاهل كل ريال. الفني يصل في أفضل الأوقات لمنزلنا ولم أعد أفكر في المكيفات أو السباكة كل شيء.','👩'],
      ] as $t)
        <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100">
          <div class="flex items-center gap-4 mb-4">
            <div class="w-14 h-14 bg-[#F5A623] rounded-full flex items-center justify-center text-2xl text-white">{{ $t[4] }}</div>
            <div>
              <div class="font-bold text-gray-800">{{ $t[0] }}</div>
              <div class="text-gray-500 text-sm">{{ $t[1] }}</div>
              <div>{{ $t[2] }}</div>
            </div>
          </div>
          <p class="text-gray-600 leading-relaxed text-sm">"{{ $t[3] }}"</p>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ===== FINAL CTA ===== --}}
<section class="py-24 bg-[#D4881A]">
  <div class="max-w-4xl mx-auto px-6 text-center">
    <h2 class="text-4xl lg:text-5xl font-black text-white mb-6">هل أنت جاهز لتجربة صيانة منزلية متميزة؟</h2>
    <p class="text-orange-100 text-xl mb-10">انضم إلى مئات العائلات التي وثقت بنا. واجعل منزلك دائماً في أفضل حال.</p>
    <div class="flex gap-4 justify-center flex-wrap">
      <a href="{{ route('register') }}" class="bg-white text-[#D4881A] font-black px-10 py-4 rounded-2xl text-lg hover:bg-orange-50 transition-all shadow-xl">سجل الآن مجاناً</a>
      <a href="#" class="border-2 border-white text-white font-bold px-10 py-4 rounded-2xl text-lg hover:bg-white/10 transition-all">تواصل مع الدعم</a>
    </div>
    <p class="text-orange-200 text-sm mt-6">بدون التزام طويل — جدّد أو ألغِ متى شئت</p>
  </div>
</section>

@endsection
