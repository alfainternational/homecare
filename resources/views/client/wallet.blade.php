@extends('layouts.dashboard')
@section('title', 'محفظتي')

@section('content')
<div class="space-y-8">
  <h1 class="text-2xl font-black text-gray-800">محفظتي</h1>

  {{-- Balance Card --}}
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-gradient-to-br from-[#F5A623] to-[#D4881A] rounded-3xl p-8 text-white col-span-1 md:col-span-2">
      <div class="flex items-center gap-3 mb-4">
        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center text-2xl">👛</div>
        <div>
          <p class="text-orange-100 text-sm">رصيد المحفظة</p>
          <p class="text-4xl font-black">{{ number_format($wallet->balance ?? 0, 2) }} <span class="text-lg">ر.س</span></p>
        </div>
      </div>
      <p class="text-orange-100 text-sm">يمكنك استخدام رصيد محفظتك للدفع في المتجر أو تجديد اشتراكك.</p>
    </div>

    {{-- Referral --}}
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
      <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2">🎁 أحل صديقاً</h3>
      <p class="text-sm text-gray-500 mb-4">شارك رابط الإحالة واحصل على <strong class="text-[#D4881A]">50 ريال</strong> لكل صديق يسجل ويشترك.</p>
      <div class="flex gap-2" x-data="{ copied: false }">
        <input type="text" readonly value="{{ $referralCode }}" class="flex-1 px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-600 font-mono" dir="ltr" id="referralLink">
        <button @click="navigator.clipboard.writeText($el.previousElementSibling.value); copied=true; setTimeout(()=>copied=false, 2000)"
          :class="copied ? 'bg-green-500' : 'bg-[#F5A623] hover:bg-[#D4881A]'"
          class="text-white px-4 py-2.5 rounded-xl text-sm font-bold transition-all">
          <span x-text="copied ? 'تم النسخ ✓' : 'نسخ'"></span>
        </button>
      </div>

      {{-- Referral stats --}}
      <div class="flex gap-4 mt-4 pt-4 border-t border-gray-100">
        <div class="text-center flex-1">
          <div class="text-xl font-black text-[#D4881A]">{{ $referrals->count() }}</div>
          <div class="text-xs text-gray-400">صديق مُحال</div>
        </div>
        <div class="text-center flex-1">
          <div class="text-xl font-black text-green-600">{{ number_format($referrals->where('status', 'rewarded')->sum('reward_amount')) }}</div>
          <div class="text-xs text-gray-400">ر.س مكتسبة</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Transaction History --}}
  <div class="bg-white rounded-3xl border border-gray-100 shadow-sm">
    <div class="p-6 border-b border-gray-100">
      <h2 class="text-lg font-black text-gray-800">سجل المعاملات</h2>
    </div>

    @if(method_exists($transactions, 'count') && $transactions->count() > 0)
      <div class="divide-y divide-gray-50">
        @foreach($transactions as $tx)
          <div class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition-colors">
            <div class="flex items-center gap-4">
              <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg {{ $tx->type === 'credit' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-500' }}">
                {{ $tx->type === 'credit' ? '↓' : '↑' }}
              </div>
              <div>
                <p class="font-semibold text-gray-800 text-sm">{{ $tx->description ?: ($tx->type === 'credit' ? 'إيداع' : 'سحب') }}</p>
                <p class="text-xs text-gray-400">{{ $tx->created_at->diffForHumans() }}</p>
              </div>
            </div>
            <span class="font-black text-lg {{ $tx->type === 'credit' ? 'text-green-600' : 'text-red-500' }}">
              {{ $tx->type === 'credit' ? '+' : '-' }}{{ number_format($tx->amount, 2) }} ر.س
            </span>
          </div>
        @endforeach
      </div>
      @if(method_exists($transactions, 'links'))
        <div class="px-6 py-4 border-t border-gray-50">{{ $transactions->links() }}</div>
      @endif
    @else
      <div class="text-center py-16">
        <div class="text-5xl mb-4 opacity-30">💸</div>
        <p class="text-gray-400 font-medium">لا توجد معاملات بعد</p>
        <p class="text-gray-300 text-sm mt-1">ستظهر معاملاتك هنا عند إجراء أي عملية</p>
      </div>
    @endif
  </div>

  {{-- Referrals list --}}
  @if($referrals->count())
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm">
      <div class="p-6 border-b border-gray-100">
        <h2 class="text-lg font-black text-gray-800">أصدقائي المُحالون</h2>
      </div>
      <div class="divide-y divide-gray-50">
        @foreach($referrals as $ref)
          <div class="flex items-center justify-between px-6 py-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center text-[#D4881A] font-bold">{{ mb_substr($ref->referred->name ?? '?', 0, 1) }}</div>
              <div>
                <p class="font-semibold text-gray-800 text-sm">{{ $ref->referred->name ?? 'مستخدم' }}</p>
                <p class="text-xs text-gray-400">{{ $ref->created_at->format('d/m/Y') }}</p>
              </div>
            </div>
            <div class="text-left">
              @if($ref->status === 'rewarded')
                <span class="bg-green-100 text-green-600 text-xs font-bold px-3 py-1 rounded-full">+{{ $ref->reward_amount }} ر.س</span>
              @else
                <span class="bg-yellow-100 text-yellow-600 text-xs font-bold px-3 py-1 rounded-full">قيد المعالجة</span>
              @endif
            </div>
          </div>
        @endforeach
      </div>
    </div>
  @endif
</div>
@endsection
