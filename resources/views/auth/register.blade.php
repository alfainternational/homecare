@extends('layouts.app')
@section('title', 'إنشاء حساب — WarmConcierge')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-16 px-4">
  <div class="w-full max-w-md">

    <div class="text-center mb-10">
      <a href="{{ route('home') }}" class="inline-flex items-center gap-2">
        <div class="w-12 h-12 bg-[#F5A623] rounded-2xl flex items-center justify-center text-white font-black text-lg">W</div>
        <span class="text-2xl font-black text-gray-800">WarmConcierge</span>
      </a>
      <h1 class="text-2xl font-bold text-gray-800 mt-6">إنشاء حساب جديد</h1>
      <p class="text-gray-500 mt-1">انضم إلى مئات الأسر التي تثق بنا</p>
    </div>

    <div class="bg-white rounded-3xl shadow-xl p-8 border border-gray-100">

      @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-2xl p-4 mb-6">
          @foreach($errors->all() as $error)
            <p class="text-red-600 text-sm">{{ $error }}</p>
          @endforeach
        </div>
      @endif

      @if(isset($refCode) && $refCode)
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 mb-6 flex items-center gap-3">
          <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
          <p class="text-amber-700 text-sm font-medium">ستحصل على <strong>25 ريال</strong> مكافأة ترحيبية عند التسجيل عبر رابط الإحالة!</p>
        </div>
      @endif

      <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf
        @if(isset($refCode) && $refCode)
          <input type="hidden" name="ref_code" value="{{ $refCode }}">
        @endif

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">الاسم الكامل</label>
          <input type="text" name="name" value="{{ old('name') }}" required
            class="w-full px-4 py-3.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#F5A623] focus:border-[#F5A623] outline-none transition bg-gray-50 focus:bg-white text-right"
            placeholder="أحمد محمد العلي">
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">البريد الإلكتروني</label>
          <input type="email" name="email" value="{{ old('email') }}" required
            class="w-full px-4 py-3.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#F5A623] focus:border-[#F5A623] outline-none transition bg-gray-50 focus:bg-white"
            placeholder="example@email.com" dir="ltr">
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">رقم الجوال</label>
          <input type="tel" name="phone" value="{{ old('phone') }}" required
            class="w-full px-4 py-3.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#F5A623] focus:border-[#F5A623] outline-none transition bg-gray-50 focus:bg-white"
            placeholder="05xxxxxxxx" dir="ltr">
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">كلمة المرور</label>
          <input type="password" name="password" required minlength="8"
            class="w-full px-4 py-3.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#F5A623] focus:border-[#F5A623] outline-none transition bg-gray-50 focus:bg-white"
            placeholder="8 أحرف على الأقل" dir="ltr">
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">تأكيد كلمة المرور</label>
          <input type="password" name="password_confirmation" required
            class="w-full px-4 py-3.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#F5A623] focus:border-[#F5A623] outline-none transition bg-gray-50 focus:bg-white"
            placeholder="أعد كتابة كلمة المرور" dir="ltr">
        </div>

        <div class="flex items-start gap-3">
          <input type="checkbox" required class="w-4 h-4 mt-1 text-[#F5A623] border-gray-300 rounded">
          <span class="text-sm text-gray-500">أوافق على <a href="#" class="text-[#D4881A] underline">شروط الخدمة</a> و<a href="#" class="text-[#D4881A] underline">سياسة الخصوصية</a></span>
        </div>

        <button type="submit" class="w-full bg-[#F5A623] hover:bg-[#D4881A] text-white font-bold py-4 rounded-2xl text-lg transition-all shadow-lg shadow-orange-100 mt-2">
          إنشاء حساب
        </button>
      </form>

      <p class="text-center text-gray-500 text-sm mt-6">
        لديك حساب بالفعل؟
        <a href="{{ route('login') }}" class="text-[#D4881A] font-bold hover:underline">سجّل دخولك</a>
      </p>
    </div>
  </div>
</div>
@endsection
