@extends('layouts.dashboard')
@section('title', 'الإعدادات')

@section('content')
<div class="space-y-8">
  <h1 class="text-2xl font-black text-gray-800">إعدادات الحساب</h1>

  @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-2xl p-4 flex items-center gap-3">
      <span class="text-green-500 text-xl">✓</span>
      <p class="text-green-700 font-medium">{{ session('success') }}</p>
    </div>
  @endif

  @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-2xl p-4">
      @foreach($errors->all() as $error)
        <p class="text-red-600 text-sm">{{ $error }}</p>
      @endforeach
    </div>
  @endif

  {{-- Profile Info --}}
  <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8">
    <h2 class="text-lg font-black text-gray-800 mb-6 flex items-center gap-2">👤 البيانات الشخصية</h2>
    <form method="POST" action="{{ route('client.profile.update') }}">
      @csrf
      @method('PUT')
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">الاسم الكامل</label>
          <input type="text" name="name" value="{{ old('name', $user->name) }}" required
            class="w-full px-4 py-3.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#F5A623] focus:border-[#F5A623] outline-none transition bg-gray-50 focus:bg-white text-right">
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">البريد الإلكتروني</label>
          <input type="email" name="email" value="{{ old('email', $user->email) }}" required
            class="w-full px-4 py-3.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#F5A623] focus:border-[#F5A623] outline-none transition bg-gray-50 focus:bg-white" dir="ltr">
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">رقم الجوال</label>
          <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" required
            class="w-full px-4 py-3.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#F5A623] focus:border-[#F5A623] outline-none transition bg-gray-50 focus:bg-white" dir="ltr">
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">نوع الحساب</label>
          <div class="px-4 py-3.5 border border-gray-100 rounded-xl bg-gray-50 text-gray-500">
            @php
              $roleMap = ['client'=>'عميل','technician'=>'فني','admin'=>'مدير','supervisor'=>'مشرف'];
            @endphp
            {{ $roleMap[$user->role] ?? $user->role }}
          </div>
        </div>
      </div>
      <button type="submit" class="mt-6 bg-[#F5A623] hover:bg-[#D4881A] text-white font-bold px-8 py-3 rounded-xl transition-all">
        حفظ التغييرات
      </button>
    </form>
  </div>

  {{-- Change Password --}}
  <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8">
    <h2 class="text-lg font-black text-gray-800 mb-6 flex items-center gap-2">🔒 تغيير كلمة المرور</h2>
    <form method="POST" action="{{ route('client.profile.password') }}">
      @csrf
      @method('PUT')
      <div class="space-y-5 max-w-md">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">كلمة المرور الحالية</label>
          <input type="password" name="current_password" required
            class="w-full px-4 py-3.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#F5A623] focus:border-[#F5A623] outline-none transition bg-gray-50 focus:bg-white" dir="ltr">
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">كلمة المرور الجديدة</label>
          <input type="password" name="password" required minlength="8"
            class="w-full px-4 py-3.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#F5A623] focus:border-[#F5A623] outline-none transition bg-gray-50 focus:bg-white" dir="ltr">
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">تأكيد كلمة المرور الجديدة</label>
          <input type="password" name="password_confirmation" required
            class="w-full px-4 py-3.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#F5A623] focus:border-[#F5A623] outline-none transition bg-gray-50 focus:bg-white" dir="ltr">
        </div>
      </div>
      <button type="submit" class="mt-6 bg-gray-800 hover:bg-gray-700 text-white font-bold px-8 py-3 rounded-xl transition-all">
        تغيير كلمة المرور
      </button>
    </form>
  </div>

  {{-- Addresses --}}
  <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8">
    <h2 class="text-lg font-black text-gray-800 mb-6 flex items-center gap-2">📍 عناويني</h2>
    @if($addresses && $addresses->count())
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($addresses as $addr)
          <div class="border border-gray-100 rounded-2xl p-5 {{ $addr->is_primary ? 'border-[#F5A623] bg-orange-50/30' : '' }}">
            <div class="flex items-center justify-between mb-2">
              <span class="font-bold text-gray-800">{{ $addr->label }}</span>
              @if($addr->is_primary)
                <span class="bg-[#F5A623] text-white text-xs font-bold px-2 py-0.5 rounded-lg">الأساسي</span>
              @endif
            </div>
            <p class="text-sm text-gray-600">{{ $addr->street }}</p>
            @if($addr->district)
              <p class="text-sm text-gray-500">{{ $addr->district }}، {{ $addr->city }}</p>
            @endif
            @if($addr->extra_notes)
              <p class="text-xs text-gray-400 mt-1">{{ $addr->extra_notes }}</p>
            @endif
          </div>
        @endforeach
      </div>
    @else
      <div class="text-center py-8">
        <div class="text-4xl mb-3 opacity-30">📍</div>
        <p class="text-gray-400">لم تضف أي عنوان بعد</p>
      </div>
    @endif
  </div>

  {{-- Danger Zone --}}
  <div class="bg-white rounded-3xl border border-red-100 p-8">
    <h2 class="text-lg font-black text-red-600 mb-2">⚠️ منطقة الخطر</h2>
    <p class="text-sm text-gray-500 mb-4">بمجرد حذف حسابك، لن يمكن استرجاعه. يرجى التفكير ملياً قبل اتخاذ هذا الإجراء.</p>
    <button type="button" class="border border-red-200 text-red-500 font-medium px-6 py-2.5 rounded-xl text-sm hover:bg-red-50 transition-all">
      حذف حسابي نهائياً
    </button>
  </div>
</div>
@endsection
