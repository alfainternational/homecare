@extends('layouts.app')
@section('title', 'تسجيل الدخول — WarmConcierge')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-16 px-4">
  <div class="w-full max-w-md">

    {{-- Logo --}}
    <div class="text-center mb-10">
      <a href="{{ route('home') }}" class="inline-flex items-center gap-2">
        <div class="w-12 h-12 bg-[#F5A623] rounded-2xl flex items-center justify-center text-white font-black text-lg">W</div>
        <span class="text-2xl font-black text-gray-800">WarmConcierge</span>
      </a>
      <h1 class="text-2xl font-bold text-gray-800 mt-6">مرحباً بعودتك</h1>
      <p class="text-gray-500 mt-1">سجّل دخولك للوصول إلى لوحة التحكم</p>
    </div>

    <div class="bg-white rounded-3xl shadow-xl p-8 border border-gray-100">

      {{-- Errors --}}
      @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-2xl p-4 mb-6">
          @foreach($errors->all() as $error)
            <p class="text-red-600 text-sm">{{ $error }}</p>
          @endforeach
        </div>
      @endif

      {{-- Success --}}
      @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-2xl p-4 mb-6">
          <p class="text-green-600 text-sm">{{ session('success') }}</p>
        </div>
      @endif

      <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">البريد الإلكتروني</label>
          <input type="email" name="email" value="{{ old('email') }}" required
            class="w-full px-4 py-3.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#F5A623] focus:border-[#F5A623] outline-none transition text-right bg-gray-50 focus:bg-white"
            placeholder="example@email.com" dir="ltr">
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">كلمة المرور</label>
          <input type="password" name="password" required
            class="w-full px-4 py-3.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#F5A623] focus:border-[#F5A623] outline-none transition bg-gray-50 focus:bg-white"
            placeholder="••••••••" dir="ltr">
        </div>

        <div class="flex items-center justify-between">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="remember" class="w-4 h-4 text-[#F5A623] border-gray-300 rounded">
            <span class="text-sm text-gray-600">تذكّرني</span>
          </label>
          <a href="#" class="text-sm text-[#D4881A] hover:underline font-medium">نسيت كلمة المرور؟</a>
        </div>

        <button type="submit" class="w-full bg-[#F5A623] hover:bg-[#D4881A] text-white font-bold py-4 rounded-2xl text-lg transition-all shadow-lg shadow-orange-100 mt-2">
          دخول
        </button>
      </form>

      <p class="text-center text-gray-500 text-sm mt-6">
        ليس لديك حساب؟
        <a href="{{ route('register') }}" class="text-[#D4881A] font-bold hover:underline">سجّل الآن</a>
      </p>
    </div>

    {{-- Demo accounts --}}
    <div class="mt-6 bg-orange-50 border border-orange-100 rounded-2xl p-4 text-sm">
      <p class="font-bold text-orange-800 mb-2">حسابات تجريبية:</p>
      <div class="space-y-1 text-orange-700 font-mono text-xs">
        <p>👤 عميل: client@warmconcierge.com / password</p>
        <p>👷 فني: tech@warmconcierge.com / password</p>
        <p>⚙️ آدمن: admin@warmconcierge.com / password</p>
      </div>
    </div>

  </div>
</div>
@endsection
