@extends('layouts.tech')

@section('title', 'السوق الحر')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h2 class="text-xl font-bold text-accent">السوق الحر</h2>
            <p class="text-sm text-gray-500 mt-1">تصفح طلبات العملاء وقدم عروضك</p>
        </div>
        <a href="{{ route('tech.marketplace.my-bids') }}"
            class="text-sm font-semibold text-brand border border-brand px-4 py-2 rounded-xl hover:bg-brand hover:text-white transition-colors">
            عروضي
        </a>
    </div>

    {{-- Subscription Notice --}}
    @if(!$hasActiveSubscription)
    <div class="bg-orange-50 border border-orange-200 rounded-2xl p-5 flex items-start gap-4">
        <div class="text-3xl flex-shrink-0">🔒</div>
        <div class="flex-1">
            <p class="font-bold text-orange-800">اشتراك مطلوب للتقديم على الطلبات</p>
            <p class="text-sm text-orange-700 mt-1">يمكنك تصفح الطلبات، لكن للتقديم تحتاج اشتراكاً نشطاً في السوق الحر.</p>
        </div>
        <a href="#subscribe"
            class="flex-shrink-0 bg-orange-500 text-white text-sm font-semibold px-4 py-2 rounded-xl hover:bg-orange-600 transition-colors">
            اشترك الآن
        </a>
    </div>
    @endif

    {{-- Filters --}}
    <form method="GET" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <div class="flex flex-wrap gap-3">
            <select name="category" onchange="this.form.submit()"
                class="text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:border-brand">
                <option value="">جميع الفئات</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->icon }} {{ $cat->name_ar }}
                </option>
                @endforeach
            </select>
            <select name="sort" onchange="this.form.submit()"
                class="text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:border-brand">
                <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>الأحدث أولاً</option>
                <option value="budget_high" {{ request('sort') === 'budget_high' ? 'selected' : '' }}>الأعلى ميزانية</option>
                <option value="budget_low" {{ request('sort') === 'budget_low' ? 'selected' : '' }}>الأقل ميزانية</option>
                <option value="fewest_bids" {{ request('sort') === 'fewest_bids' ? 'selected' : '' }}>أقل عروض</option>
            </select>
        </div>
    </form>

    {{-- Posts --}}
    <div class="space-y-4">
        @forelse($posts as $post)
        <a href="{{ route('tech.marketplace.show', $post) }}"
            class="block bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-brand/30 transition-all p-5">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-brand-light rounded-xl flex items-center justify-center text-xl flex-shrink-0">
                    {{ $post->serviceCategory?->icon ?? '🔧' }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-3 flex-wrap">
                        <div>
                            <h3 class="font-bold text-accent">{{ $post->title }}</h3>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $post->serviceCategory?->name_ar ?? '—' }} · {{ $post->created_at->diffForHumans() }}
                            </p>
                        </div>
                        @if($post->budget_max || $post->budget_min)
                        <div class="text-left">
                            <p class="text-lg font-black text-accent">
                                @if($post->budget_min && $post->budget_max)
                                    {{ number_format($post->budget_min) }}–{{ number_format($post->budget_max) }}
                                @elseif($post->budget_max)
                                    حتى {{ number_format($post->budget_max) }}
                                @else
                                    من {{ number_format($post->budget_min) }}
                                @endif
                            </p>
                            <p class="text-xs text-gray-400">ر.س</p>
                        </div>
                        @else
                        <span class="text-xs text-gray-400 italic">ميزانية مفتوحة</span>
                        @endif
                    </div>

                    <p class="text-sm text-gray-600 mt-2 line-clamp-2">{{ $post->description }}</p>

                    <div class="flex items-center gap-4 mt-3 text-xs text-gray-500 flex-wrap">
                        <span>💬 {{ $post->bids_count }} عرض</span>
                        @if($post->address)
                        <span>📍 {{ Str::limit($post->address->full_address, 40) }}</span>
                        @endif
                        @if($post->expires_at)
                        <span class="{{ $post->expires_at->isPast() ? 'text-red-500' : '' }}">
                            ⏰ {{ $post->expires_at->format('Y/m/d') }}
                        </span>
                        @endif
                        @if($post->user_has_bid ?? false)
                        <span class="text-brand font-semibold">✓ قدمت عرضاً</span>
                        @endif
                    </div>
                </div>
            </div>
        </a>
        @empty
        <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
            <p class="text-5xl mb-4">🔍</p>
            <p class="font-bold text-accent mb-1">لا توجد طلبات متاحة حالياً</p>
            <p class="text-sm text-gray-500">تحقق مرة أخرى لاحقاً</p>
        </div>
        @endforelse
    </div>

    @if($posts->hasPages())
    <div>{{ $posts->links() }}</div>
    @endif

</div>
@endsection
