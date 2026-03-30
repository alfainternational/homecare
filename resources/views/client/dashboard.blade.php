@extends('layouts.dashboard')

@section('title', 'لوحة التحكم')

@push('styles')
<style>
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px -5px rgba(0,0,0,0.1); }
    .progress-bar-inner { transition: width 0.8s ease-in-out; }
</style>
@endpush

@section('content')

@php
    $sub              = $user->subscription;
    $visitsRemaining  = $sub ? $sub->visitsRemaining() : 0;
    $visitsTotal      = $sub->visits_total  ?? 0;
    $visitsUsed       = $sub->visits_used   ?? 0;
    $visitsPercent    = $visitsTotal > 0 ? round(($visitsUsed / $visitsTotal) * 100) : 0;
    $renewalDate      = $sub?->ends_at;
    $daysLeft         = $renewalDate ? max(0, (int)now()->diffInDays($renewalDate, false)) : 0;
    $planName         = $sub?->plan?->name_ar ?? 'لا يوجد اشتراك';
    $totalRequests    = $user->serviceRequests()->count();
    $walletBalance    = (float)($user->wallet?->balance ?? 0);
    $referralLink     = route('register', ['ref' => $user->id]);
@endphp

{{-- ===== ROW 1: STAT CARDS ===== --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    {{-- زيارات متبقية --}}
    <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-start justify-between mb-3">
            <div class="w-11 h-11 rounded-xl bg-orange-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full">متاحة</span>
        </div>
        <p class="text-3xl font-black text-accent mb-1">{{ $visitsRemaining }}</p>
        <p class="text-sm text-gray-500 font-medium">زيارات متبقية</p>
    </div>

    {{-- يوم حتى التجديد --}}
    <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-start justify-between mb-3">
            <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            @if($daysLeft <= 7)
                <span class="text-xs font-medium text-red-600 bg-red-50 px-2 py-0.5 rounded-full">قريباً</span>
            @else
                <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">نشط</span>
            @endif
        </div>
        <p class="text-3xl font-black text-accent mb-1">{{ $daysLeft }}</p>
        <p class="text-sm text-gray-500 font-medium">يوم حتى التجديد</p>
    </div>

    {{-- إجمالي الطلبات --}}
    <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-start justify-between mb-3">
            <div class="w-11 h-11 rounded-xl bg-purple-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <span class="text-xs font-medium text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full">الكل</span>
        </div>
        <p class="text-3xl font-black text-accent mb-1">{{ $totalRequests }}</p>
        <p class="text-sm text-gray-500 font-medium">إجمالي الطلبات</p>
    </div>

    {{-- متوسط التقييم --}}
    <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-start justify-between mb-3">
            <div class="w-11 h-11 rounded-xl bg-yellow-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                </svg>
            </div>
            <span class="text-xs font-medium text-yellow-600 bg-yellow-50 px-2 py-0.5 rounded-full">ممتاز</span>
        </div>
        @php
            $avgRating = $user->serviceRequests()->whereNotNull('rating')->avg('rating');
            $ratingCount = $user->serviceRequests()->whereNotNull('rating')->count();
        @endphp
        <p class="text-3xl font-black text-accent mb-1">{{ $avgRating ? number_format($avgRating, 1) : '—' }}</p>
        <p class="text-sm text-gray-500 font-medium">متوسط التقييم @if($ratingCount) <span class="text-xs">({{ $ratingCount }} طلب)</span> @endif</p>
    </div>
</div>

{{-- ===== ROW 2: SUBSCRIPTION + LATEST REQUEST ===== --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">

    {{-- Subscription Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-l from-brand to-brand-dark p-5 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-orange-100 mb-1">اشتراكك الحالي</p>
                    <h3 class="text-xl font-black">{{ $planName }}</h3>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="p-5">
            {{-- Progress Bar --}}
            <div class="mb-5">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-semibold text-gray-700">الزيارات المستخدمة</span>
                    <span class="text-sm font-bold text-brand">{{ $visitsUsed }} / {{ $visitsTotal }}</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-3">
                    <div class="progress-bar-inner bg-gradient-to-l from-brand to-brand-dark h-3 rounded-full"
                         style="width: {{ $visitsPercent }}%"></div>
                </div>
                <div class="flex justify-between mt-1.5">
                    <span class="text-xs text-gray-400">{{ $visitsPercent }}% مستخدم</span>
                    <span class="text-xs text-gray-400">{{ $visitsRemaining }} زيارة متبقية</span>
                </div>
            </div>

            {{-- Renewal Date --}}
            <div class="flex items-center gap-2 mb-5 bg-brand-light rounded-xl px-4 py-3">
                <svg class="w-4 h-4 text-brand flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-sm text-gray-700 font-medium">
                    تاريخ التجديد:
                    <strong class="text-accent">
                        {{ $renewalDate ? \Carbon\Carbon::parse($renewalDate)->format('d/m/Y') : 'غير محدد' }}
                    </strong>
                </span>
            </div>

            <a href="{{ route('client.subscription') }}"
               class="w-full flex items-center justify-center gap-2 bg-brand hover:bg-brand-dark text-white font-bold py-3 rounded-xl transition-colors shadow-sm text-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                جدّد الآن
            </a>
        </div>
    </div>

    {{-- Latest Request Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 pt-5 pb-4 border-b border-gray-50">
            <h3 class="font-bold text-accent text-base">آخر طلب</h3>
            <a href="{{ route('client.requests.index') }}" class="text-sm text-brand font-semibold hover:underline">عرض الكل</a>
        </div>

        @if($latestRequest)
        @php
            $statusMap = [
                'pending'           => ['label' => 'قيد الانتظار',    'class' => 'bg-yellow-100 text-yellow-800'],
                'assigned'          => ['label' => 'تم التعيين',       'class' => 'bg-blue-100 text-blue-800'],
                'on_way'            => ['label' => 'في الطريق',         'class' => 'bg-blue-100 text-blue-800'],
                'arrived'           => ['label' => 'وصل الفني',         'class' => 'bg-blue-100 text-blue-800'],
                'in_progress'       => ['label' => 'جاري التنفيذ',      'class' => 'bg-blue-100 text-blue-800'],
                'awaiting_approval' => ['label' => 'بانتظار الموافقة',  'class' => 'bg-purple-100 text-purple-800'],
                'completed'         => ['label' => 'مكتمل',             'class' => 'bg-green-100 text-green-800'],
                'cancelled'         => ['label' => 'ملغي',              'class' => 'bg-red-100 text-red-800'],
            ];
            $typeMap = [
                'plumbing'   => ['label' => 'سباكة',   'icon' => '🔧'],
                'electrical' => ['label' => 'كهرباء',  'icon' => '⚡'],
                'hvac'       => ['label' => 'تكييف',   'icon' => '❄️'],
                'general'    => ['label' => 'عام',     'icon' => '🏠'],
            ];
            $status = $statusMap[$latestRequest->status] ?? ['label' => $latestRequest->status, 'class' => 'bg-gray-100 text-gray-800'];
            $type   = $typeMap[$latestRequest->service_type] ?? ['label' => $latestRequest->service_type, 'icon'  => '🔨'];
        @endphp

        <div class="p-5">
            {{-- Request header --}}
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-brand-light rounded-xl flex items-center justify-center text-2xl">
                        {{ $type['icon'] }}
                    </div>
                    <div>
                        <p class="font-bold text-accent text-sm">{{ $type['label'] }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            #{{ $latestRequest->request_number ?? str_pad($latestRequest->id, 5, '0', STR_PAD_LEFT) }}
                        </p>
                    </div>
                </div>
                <span class="text-xs font-semibold px-3 py-1.5 rounded-full {{ $status['class'] }}">
                    {{ $status['label'] }}
                </span>
            </div>

            {{-- Details --}}
            <div class="space-y-2.5 mb-5">
                <div class="flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-gray-600">{{ $latestRequest->created_at->format('d/m/Y — H:i') }}</span>
                </div>
                @if($latestRequest->technician)
                <div class="flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="text-gray-600">الفني: <strong class="text-accent">{{ $latestRequest->technician->name }}</strong></span>
                </div>
                @endif
            </div>

            <a href="{{ route('client.requests.show', $latestRequest) }}"
               class="w-full flex items-center justify-center gap-2 border-2 border-brand text-brand hover:bg-brand hover:text-white font-bold py-3 rounded-xl transition-all text-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                عرض التفاصيل
            </a>
        </div>

        @else
        {{-- Empty state --}}
        <div class="flex flex-col items-center justify-center py-10 px-5 text-center">
            <div class="w-16 h-16 bg-brand-light rounded-2xl flex items-center justify-center mb-4 text-3xl">🏠</div>
            <h4 class="font-bold text-accent mb-2">لا توجد طلبات بعد</h4>
            <p class="text-sm text-gray-500 mb-5 leading-relaxed">ابدأ رحلتك مع وورم كونسيرج واطلب صيانتك الأولى الآن</p>
            <a href="{{ route('client.requests.create') }}"
               class="flex items-center gap-2 bg-brand hover:bg-brand-dark text-white font-bold px-6 py-2.5 rounded-xl transition-colors shadow-sm text-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                اطلب صيانتك الأولى
            </a>
        </div>
        @endif
    </div>
</div>

{{-- ===== ROW 3: NOTIFICATIONS + WALLET ===== --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    {{-- Notifications Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 pt-5 pb-4 border-b border-gray-50">
            <div class="flex items-center gap-2">
                <h3 class="font-bold text-accent text-base">الإشعارات الأخيرة</h3>
                @if($notifications->count() > 0)
                <span class="w-5 h-5 bg-brand text-white text-xs font-bold rounded-full flex items-center justify-center">
                    {{ $notifications->count() }}
                </span>
                @endif
            </div>
            <a href="#" class="text-sm text-brand font-semibold hover:underline">عرض الكل</a>
        </div>

        @if($notifications->count() > 0)
        <ul class="divide-y divide-gray-50">
            @foreach($notifications->take(6) as $notification)
            @php
                $dotColor = match($notification->type ?? 'info') {
                    'success'  => 'bg-green-400',
                    'warning'  => 'bg-yellow-400',
                    'error'    => 'bg-red-400',
                    'request'  => 'bg-brand',
                    default    => 'bg-blue-400',
                };
            @endphp
            <li class="flex items-start gap-3 px-5 py-3.5 hover:bg-gray-50 transition-colors">
                <div class="mt-1.5 flex-shrink-0">
                    <span class="w-2.5 h-2.5 rounded-full block {{ $dotColor }}"></span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-gray-800 leading-snug font-medium">
                        {{ $notification->data['message'] ?? $notification->message ?? 'إشعار جديد' }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">
                        {{ $notification->created_at->diffForHumans() }}
                    </p>
                </div>
                @if(!$notification->read_at)
                <div class="w-2 h-2 bg-brand rounded-full flex-shrink-0 mt-2"></div>
                @endif
            </li>
            @endforeach
        </ul>
        @else
        <div class="flex flex-col items-center justify-center py-10 text-center px-5">
            <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mb-3">
                <svg class="w-7 h-7 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </div>
            <p class="text-sm text-gray-500 font-medium">لا توجد إشعارات حالياً</p>
        </div>
        @endif
    </div>

    {{-- Wallet Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-l from-accent to-gray-700 p-5 text-white">
            <p class="text-sm font-medium text-gray-300 mb-1">رصيد محفظتي</p>
            <div class="flex items-end gap-2">
                <span class="text-4xl font-black">{{ number_format($walletBalance, 2) }}</span>
                <span class="text-lg text-gray-300 mb-1">ر.س</span>
            </div>
        </div>

        <div class="p-5 space-y-4">
            {{-- Referral --}}
            <div>
                <p class="text-sm font-bold text-accent mb-2 flex items-center gap-2">
                    <span class="text-lg">🎁</span>
                    أحل صديقاً واربح 50 ر.س
                </p>
                <p class="text-xs text-gray-500 mb-3 leading-relaxed">
                    شارك رابط الإحالة مع أصدقائك واحصل على 50 ر.س في محفظتك عند اشتراك كل صديق.
                </p>

                {{-- Referral link input + copy button --}}
                <div x-data="{ copied: false }" class="flex gap-2">
                    <div class="flex-1 relative">
                        <input
                            type="text"
                            id="referralInput"
                            value="{{ $referralLink }}"
                            readonly
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-brand/30 font-mono text-xs"
                        />
                    </div>
                    <button
                        @click="
                            navigator.clipboard.writeText(document.getElementById('referralInput').value);
                            copied = true;
                            setTimeout(() => copied = false, 2500)
                        "
                        class="flex-shrink-0 flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all"
                        :class="copied
                            ? 'bg-green-500 text-white'
                            : 'bg-brand hover:bg-brand-dark text-white'"
                    >
                        <svg x-show="!copied" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        <svg x-show="copied" x-cloak class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span x-text="copied ? 'تم النسخ!' : 'نسخ'"></span>
                    </button>
                </div>
            </div>

            {{-- Quick actions --}}
            <div class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-50">
                <a href="#"
                   class="flex items-center justify-center gap-2 bg-gray-50 hover:bg-gray-100 text-gray-700 text-sm font-semibold py-2.5 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    شحن الرصيد
                </a>
                <a href="#"
                   class="flex items-center justify-center gap-2 bg-gray-50 hover:bg-gray-100 text-gray-700 text-sm font-semibold py-2.5 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    السجل
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
