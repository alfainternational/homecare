@extends('layouts.tech')

@section('title', 'عروضي')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h2 class="text-xl font-bold text-accent">عروضي المقدمة</h2>
            <p class="text-sm text-gray-500 mt-1">جميع العروض التي قدمتها في السوق الحر</p>
        </div>
        <a href="{{ route('tech.marketplace.browse') }}"
            class="text-sm font-semibold text-brand border border-brand px-4 py-2 rounded-xl hover:bg-brand hover:text-white transition-colors">
            تصفح الطلبات
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    {{-- Filter Tabs --}}
    <div class="flex gap-2 overflow-x-auto">
        @foreach([
            ['all', 'الكل'],
            ['pending', 'بانتظار القرار'],
            ['accepted', 'مقبولة'],
            ['rejected', 'مرفوضة'],
        ] as [$val, $label])
        <a href="{{ request()->fullUrlWithQuery(['status' => $val]) }}"
            class="flex-shrink-0 text-sm font-medium px-4 py-2 rounded-xl transition-colors
                {{ (request('status', 'all') === $val) ? 'bg-brand text-white' : 'bg-white border border-gray-200 text-gray-600 hover:border-brand hover:text-brand' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    {{-- Bids --}}
    <div class="space-y-4">
        @forelse($bids as $bid)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-5">
                <div class="flex items-start justify-between gap-3 flex-wrap">
                    <div class="flex items-start gap-3">
                        <div class="w-11 h-11 bg-brand-light rounded-xl flex items-center justify-center text-xl flex-shrink-0">
                            {{ $bid->jobPost->serviceCategory?->icon ?? '🔧' }}
                        </div>
                        <div>
                            <h3 class="font-bold text-accent">{{ $bid->jobPost->title }}</h3>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $bid->jobPost->post_number }} · {{ $bid->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @php
                    $statusStyle = match($bid->status) {
                        'accepted' => 'bg-green-100 text-green-700',
                        'rejected' => 'bg-red-100 text-red-600',
                        'withdrawn' => 'bg-gray-100 text-gray-500',
                        default => 'bg-blue-100 text-blue-700',
                    };
                    @endphp
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $statusStyle }}">{{ $bid->status_label }}</span>
                </div>

                <div class="flex items-center gap-5 mt-4">
                    <div>
                        <p class="text-xs text-gray-500">عرضك</p>
                        <p class="text-xl font-black text-accent">{{ number_format($bid->price) }} <span class="text-sm font-medium text-gray-500">ر.س</span></p>
                    </div>
                    @if($bid->estimated_duration)
                    <div>
                        <p class="text-xs text-gray-500">المدة</p>
                        <p class="text-sm font-semibold text-accent">{{ $bid->estimated_duration }}</p>
                    </div>
                    @endif
                </div>

                <p class="text-sm text-gray-600 mt-3 line-clamp-2">{{ $bid->message }}</p>
            </div>

            <div class="px-5 py-3 border-t border-gray-50 flex items-center justify-between gap-3">
                <a href="{{ route('tech.marketplace.show', $bid->jobPost) }}"
                    class="text-sm font-semibold text-brand hover:text-brand-dark transition-colors">
                    عرض التفاصيل →
                </a>
                @if($bid->status === 'pending')
                <form action="{{ route('tech.marketplace.bid.withdraw', $bid) }}" method="POST"
                    onsubmit="return confirm('هل تريد سحب عرضك؟')">
                    @csrf
                    <button type="submit" class="text-xs text-red-500 hover:text-red-700 transition-colors">سحب العرض</button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
            <p class="text-5xl mb-4">📋</p>
            <h3 class="font-bold text-accent mb-2">لم تقدم أي عروض حتى الآن</h3>
            <p class="text-sm text-gray-500 mb-6">تصفح الطلبات المتاحة وقدم عروضك</p>
            <a href="{{ route('tech.marketplace.browse') }}"
                class="inline-block bg-brand text-white text-sm font-semibold px-6 py-2.5 rounded-xl hover:bg-brand-dark transition-colors">
                تصفح الطلبات
            </a>
        </div>
        @endforelse
    </div>

    @if($bids->hasPages())
    <div>{{ $bids->links() }}</div>
    @endif

</div>
@endsection
