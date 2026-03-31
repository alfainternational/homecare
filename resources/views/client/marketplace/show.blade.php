@extends('layouts.dashboard')

@section('title', $jobPost->title)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Back --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('client.marketplace.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h2 class="text-xl font-bold text-accent">{{ $jobPost->title }}</h2>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    {{-- Post Details --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <p class="text-xs text-gray-400">{{ $jobPost->post_number }} · {{ $jobPost->created_at->format('Y/m/d') }}</p>
                @if($jobPost->serviceCategory)
                <p class="text-sm text-brand mt-1">{{ $jobPost->serviceCategory->icon }} {{ $jobPost->serviceCategory->name_ar }}</p>
                @endif
            </div>
            @php
            $statusMap = [
                'open' => ['label' => 'مفتوح للعروض', 'class' => 'bg-green-100 text-green-700'],
                'in_progress' => ['label' => 'قيد التنفيذ', 'class' => 'bg-blue-100 text-blue-700'],
                'completed' => ['label' => 'مكتمل', 'class' => 'bg-purple-100 text-purple-700'],
                'cancelled' => ['label' => 'ملغي', 'class' => 'bg-red-100 text-red-600'],
                'expired' => ['label' => 'منتهي', 'class' => 'bg-orange-100 text-orange-600'],
            ];
            $s = $statusMap[$jobPost->status] ?? ['label' => $jobPost->status, 'class' => 'bg-gray-100 text-gray-600'];
            @endphp
            <span class="text-sm font-semibold px-3 py-1 rounded-full {{ $s['class'] }}">{{ $s['label'] }}</span>
        </div>

        <p class="text-gray-700 leading-relaxed">{{ $jobPost->description }}</p>

        <div class="flex flex-wrap gap-4 text-sm text-gray-600">
            @if($jobPost->budget_min || $jobPost->budget_max)
            <span>💰
                @if($jobPost->budget_min && $jobPost->budget_max)
                    {{ number_format($jobPost->budget_min) }} — {{ number_format($jobPost->budget_max) }} ر.س
                @elseif($jobPost->budget_min)
                    من {{ number_format($jobPost->budget_min) }} ر.س
                @else
                    حتى {{ number_format($jobPost->budget_max) }} ر.س
                @endif
            </span>
            @endif
            @if($jobPost->address)
            <span>📍 {{ $jobPost->address->full_address }}</span>
            @endif
            @if($jobPost->expires_at)
            <span class="{{ $jobPost->expires_at->isPast() ? 'text-red-500' : '' }}">
                ⏰ ينتهي {{ $jobPost->expires_at->format('Y/m/d') }}
            </span>
            @endif
        </div>

        {{-- Media --}}
        @if($jobPost->media_paths)
        <div class="flex flex-wrap gap-2 pt-2">
            @foreach($jobPost->media_paths as $path)
            <a href="{{ Storage::url($path) }}" target="_blank"
                class="w-20 h-20 rounded-xl overflow-hidden border border-gray-100 block flex-shrink-0">
                <img src="{{ Storage::url($path) }}" alt="" class="w-full h-full object-cover">
            </a>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Bids --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="font-bold text-accent">العروض المقدمة ({{ $jobPost->bids->count() }})</h3>
        </div>

        @if($jobPost->winning_bid_id)
        {{-- Winning Bid Highlighted --}}
        @php $winner = $jobPost->bids->firstWhere('id', $jobPost->winning_bid_id); @endphp
        @if($winner)
        <div class="bg-green-50 border-2 border-green-300 rounded-2xl p-5 relative">
            <span class="absolute top-3 left-3 text-xs font-bold bg-green-500 text-white px-2 py-0.5 rounded-full">✓ العرض المقبول</span>
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-full bg-green-200 flex items-center justify-center text-lg font-bold text-green-800 flex-shrink-0">
                    {{ mb_substr($winner->technician->name, 0, 1) }}
                </div>
                <div class="flex-1">
                    <p class="font-bold text-accent">{{ $winner->technician->name }}</p>
                    <p class="text-xl font-black text-green-700 mt-1">{{ number_format($winner->price) }} ر.س</p>
                    @if($winner->estimated_duration)
                    <p class="text-xs text-gray-500 mt-1">⏱ {{ $winner->estimated_duration }}</p>
                    @endif
                    <p class="text-sm text-gray-700 mt-2">{{ $winner->message }}</p>
                </div>
            </div>
        </div>
        @endif
        @endif

        @forelse($jobPost->bids->where('status', '!=', 'accepted') as $bid)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-start gap-4">
                <div class="w-11 h-11 rounded-full bg-brand-light flex items-center justify-center text-base font-bold text-brand flex-shrink-0">
                    {{ mb_substr($bid->technician->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-3 flex-wrap">
                        <div>
                            <p class="font-semibold text-accent">{{ $bid->technician->name }}</p>
                            <p class="text-xs text-gray-400">{{ $bid->created_at->diffForHumans() }}</p>
                        </div>
                        <p class="text-xl font-black text-accent">{{ number_format($bid->price) }} <span class="text-sm font-medium text-gray-500">ر.س</span></p>
                    </div>
                    @if($bid->estimated_duration)
                    <p class="text-xs text-gray-500 mt-1">⏱ {{ $bid->estimated_duration }}</p>
                    @endif
                    <p class="text-sm text-gray-700 mt-2">{{ $bid->message }}</p>

                    @if($jobPost->status === 'open' && !$jobPost->winning_bid_id && $jobPost->client_id === auth()->id())
                    <div class="mt-3">
                        <form action="{{ route('client.marketplace.select-bid', [$jobPost, $bid]) }}" method="POST"
                            onsubmit="return confirm('هل تريد قبول عرض {{ $bid->technician->name }} بسعر {{ number_format($bid->price) }} ر.س؟')">
                            @csrf
                            <button type="submit"
                                class="text-sm bg-brand text-white font-semibold px-4 py-2 rounded-xl hover:bg-brand-dark transition-colors">
                                قبول هذا العرض
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        @if(!$jobPost->winning_bid_id)
        <div class="bg-white rounded-2xl border border-gray-100 p-10 text-center">
            <p class="text-4xl mb-3">⏳</p>
            <p class="text-gray-500 text-sm">لم يتقدم أحد بعرض حتى الآن</p>
        </div>
        @endif
        @endforelse
    </div>

</div>
@endsection
