@extends('layouts.dashboard')

@section('title', 'السوق الحر')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h2 class="text-xl font-bold text-accent">السوق الحر</h2>
            <p class="text-sm text-gray-500 mt-1">منشوراتي وطلباتي في السوق الحر</p>
        </div>
        <a href="{{ route('client.marketplace.create') }}"
            class="bg-brand text-white text-sm font-semibold px-4 py-2 rounded-xl hover:bg-brand-dark transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            نشر طلب جديد
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm text-center">
            <p class="text-2xl font-black text-accent">{{ $posts->where('status', 'open')->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">طلبات مفتوحة</p>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm text-center">
            <p class="text-2xl font-black text-brand">{{ $posts->where('status', 'in_progress')->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">قيد التنفيذ</p>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm text-center">
            <p class="text-2xl font-black text-green-600">{{ $posts->where('status', 'completed')->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">مكتملة</p>
        </div>
    </div>

    {{-- Posts --}}
    <div class="space-y-4">
        @forelse($posts as $post)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="p-5">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-brand-light rounded-xl flex items-center justify-center text-lg flex-shrink-0">
                        {{ $post->serviceCategory?->icon ?? '🔧' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-3 flex-wrap">
                            <div>
                                <h3 class="font-bold text-accent">{{ $post->title }}</h3>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $post->post_number }}
                                    @if($post->serviceCategory) · {{ $post->serviceCategory->name_ar }} @endif
                                    · {{ $post->created_at->diffForHumans() }}
                                </p>
                            </div>
                            @php
                            $statusMap = [
                                'draft' => ['label' => 'مسودة', 'class' => 'bg-gray-100 text-gray-600'],
                                'open' => ['label' => 'مفتوح', 'class' => 'bg-green-100 text-green-700'],
                                'in_progress' => ['label' => 'قيد التنفيذ', 'class' => 'bg-blue-100 text-blue-700'],
                                'completed' => ['label' => 'مكتمل', 'class' => 'bg-purple-100 text-purple-700'],
                                'cancelled' => ['label' => 'ملغي', 'class' => 'bg-red-100 text-red-600'],
                                'expired' => ['label' => 'منتهي', 'class' => 'bg-orange-100 text-orange-600'],
                            ];
                            $s = $statusMap[$post->status] ?? ['label' => $post->status, 'class' => 'bg-gray-100 text-gray-600'];
                            @endphp
                            <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $s['class'] }}">{{ $s['label'] }}</span>
                        </div>

                        <p class="text-sm text-gray-600 mt-2 line-clamp-2">{{ $post->description }}</p>

                        <div class="flex items-center gap-4 mt-3 flex-wrap">
                            @if($post->budget_min || $post->budget_max)
                            <span class="text-xs text-gray-500">
                                💰 الميزانية:
                                @if($post->budget_min && $post->budget_max)
                                    {{ number_format($post->budget_min) }} — {{ number_format($post->budget_max) }} ر.س
                                @elseif($post->budget_min)
                                    من {{ number_format($post->budget_min) }} ر.س
                                @else
                                    حتى {{ number_format($post->budget_max) }} ر.س
                                @endif
                            </span>
                            @endif
                            <span class="text-xs text-gray-500">
                                💬 {{ $post->bids_count ?? $post->bids->count() }} عرض
                            </span>
                            @if($post->expires_at)
                            <span class="text-xs {{ $post->expires_at->isPast() ? 'text-red-500' : 'text-gray-400' }}">
                                ⏰ {{ $post->expires_at->format('Y/m/d') }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-5 py-3 border-t border-gray-50 flex items-center justify-between gap-3">
                <a href="{{ route('client.marketplace.show', $post) }}"
                    class="text-sm font-semibold text-brand hover:text-brand-dark transition-colors">
                    عرض العروض →
                </a>
                @if(in_array($post->status, ['draft', 'open']))
                <form action="{{ route('client.marketplace.destroy', $post) }}" method="POST"
                    onsubmit="return confirm('هل تريد حذف هذا المنشور؟')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs text-red-500 hover:text-red-700 transition-colors">حذف</button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <div class="text-5xl mb-4">🛒</div>
            <h3 class="font-bold text-accent mb-2">لا توجد منشورات حتى الآن</h3>
            <p class="text-sm text-gray-500 mb-6">انشر طلبك وتلقَّ عروضاً من أفضل الفنيين</p>
            <a href="{{ route('client.marketplace.create') }}"
                class="inline-block bg-brand text-white text-sm font-semibold px-6 py-2.5 rounded-xl hover:bg-brand-dark transition-colors">
                نشر أول طلب
            </a>
        </div>
        @endforelse
    </div>

    @if($posts instanceof \Illuminate\Pagination\LengthAwarePaginator && $posts->hasPages())
    <div>{{ $posts->links() }}</div>
    @endif

</div>
@endsection
