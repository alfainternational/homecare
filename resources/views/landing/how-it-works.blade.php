@extends('layouts.app')
@section('title', 'كيف يعمل — WarmConcierge')

@section('content')
<div class="max-w-5xl mx-auto px-6 py-20">
  <h1 class="text-4xl font-black text-gray-900 text-center mb-4">كيف يعمل <span class="text-[#F5A623]">وورم كونسيرج؟</span></h1>
  <p class="text-gray-500 text-center text-lg mb-16">أربع خطوات بسيطة للحصول على خدمة صيانة منزلية احترافية</p>
  <div class="space-y-8">
    @foreach([['1','👤','إنشاء حساب','سجّل بياناتك ومعلومات منزلك خلال دقيقة واحدة فقط.'],['2','🔧','اطلب الخدمة','اختر نوع الصيانة وصف المشكلة بالكتابة أو الصوت وأرفق صوراً.'],['3','🚗','وصول الفني','الفني المعتمد يصلك في الموعد المحدد بجميع الأدوات اللازمة.'],['4','⭐','قيّم الخدمة','تأكد من جودة العمل وقيّم الفني لمساعدة العملاء الآخرين.']] as [$n,$icon,$title,$desc])
      <div class="flex items-start gap-6 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="relative flex-shrink-0">
          <div class="w-16 h-16 bg-orange-50 rounded-2xl flex items-center justify-center text-3xl">{{ $icon }}</div>
          <div class="absolute -top-2 -right-2 w-7 h-7 bg-[#F5A623] text-white rounded-full flex items-center justify-center text-sm font-black">{{ $n }}</div>
        </div>
        <div>
          <h3 class="text-xl font-black text-gray-800 mb-1">{{ $title }}</h3>
          <p class="text-gray-500 leading-relaxed">{{ $desc }}</p>
        </div>
      </div>
    @endforeach
  </div>
  <div class="text-center mt-12">
    <a href="{{ route('register') }}" class="bg-[#F5A623] hover:bg-[#D4881A] text-white font-black px-10 py-4 rounded-2xl text-lg transition-all shadow-lg shadow-orange-100">ابدأ الآن مجاناً</a>
  </div>
</div>
@endsection
