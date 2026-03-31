@extends('layouts.tech')

@section('title', $jobPost->title)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Back --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('tech.marketplace.browse') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h2 class="font-bold text-accent text-xl">تفاصيل الطلب</h2>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    {{-- Post --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
        <div class="flex items-start gap-4">
            <div class="w-14 h-14 bg-brand-light rounded-xl flex items-center justify-center text-2xl flex-shrink-0">
                {{ $jobPost->serviceCategory?->icon ?? '🔧' }}
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-accent text-lg">{{ $jobPost->title }}</h3>
                <p class="text-xs text-gray-400 mt-1">
                    {{ $jobPost->post_number }}
                    @if($jobPost->serviceCategory) · {{ $jobPost->serviceCategory->name_ar }} @endif
                    · {{ $jobPost->created_at->format('Y/m/d') }}
                </p>
            </div>
        </div>

        <p class="text-gray-700 leading-relaxed">{{ $jobPost->description }}</p>

        <div class="grid grid-cols-2 gap-3">
            @if($jobPost->budget_min || $jobPost->budget_max)
            <div class="bg-gray-50 rounded-xl p-3">
                <p class="text-xs text-gray-500">الميزانية</p>
                <p class="font-bold text-accent mt-0.5">
                    @if($jobPost->budget_min && $jobPost->budget_max)
                        {{ number_format($jobPost->budget_min) }} — {{ number_format($jobPost->budget_max) }} ر.س
                    @elseif($jobPost->budget_max)
                        حتى {{ number_format($jobPost->budget_max) }} ر.س
                    @else
                        من {{ number_format($jobPost->budget_min) }} ر.س
                    @endif
                </p>
            </div>
            @endif
            <div class="bg-gray-50 rounded-xl p-3">
                <p class="text-xs text-gray-500">عدد العروض</p>
                <p class="font-bold text-accent mt-0.5">{{ $jobPost->bids->count() }} عرض</p>
            </div>
            @if($jobPost->address)
            <div class="bg-gray-50 rounded-xl p-3 col-span-2">
                <p class="text-xs text-gray-500">الموقع</p>
                <p class="font-medium text-accent mt-0.5 text-sm">{{ $jobPost->address->full_address }}</p>
            </div>
            @endif
        </div>

        {{-- Media --}}
        @if($jobPost->media_paths)
        <div class="flex flex-wrap gap-2">
            @foreach($jobPost->media_paths as $path)
            <a href="{{ Storage::url($path) }}" target="_blank"
                class="w-20 h-20 rounded-xl overflow-hidden border border-gray-100 block">
                <img src="{{ Storage::url($path) }}" alt="" class="w-full h-full object-cover">
            </a>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Existing bid or bid form --}}
    @if($existingBid)
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <p class="font-bold text-blue-800">عرضك المقدم</p>
                <p class="text-2xl font-black text-blue-700 mt-1">{{ number_format($existingBid->price) }} ر.س</p>
                @if($existingBid->estimated_duration)
                <p class="text-xs text-blue-600 mt-0.5">⏱ {{ $existingBid->estimated_duration }}</p>
                @endif
                <p class="text-sm text-blue-700 mt-2">{{ $existingBid->message }}</p>
            </div>
            <div class="text-left">
                <span class="text-xs font-semibold px-3 py-1.5 rounded-full
                    {{ $existingBid->status === 'accepted' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                    {{ $existingBid->status_label }}
                </span>
                @if($existingBid->status === 'pending')
                <form action="{{ route('tech.marketplace.bid.withdraw', $existingBid) }}" method="POST"
                    onsubmit="return confirm('هل تريد سحب عرضك؟')" class="mt-3">
                    @csrf
                    <button type="submit" class="text-xs text-red-600 hover:text-red-800 font-medium">سحب العرض</button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @elseif($jobPost->status === 'open' && !$jobPost->expires_at?->isPast())
    {{-- Bid Form --}}
    @if($canBid)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
        <h3 class="font-bold text-accent">تقديم عرضك</h3>

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-3">
            <ul class="text-sm text-red-700 space-y-1 list-disc list-inside">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('tech.marketplace.bid', $jobPost) }}" method="POST" enctype="multipart/form-data"
            class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">سعر العرض (ر.س) *</label>
                    <input type="number" name="price" value="{{ old('price') }}" required min="1" step="0.01"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-brand @error('price') border-red-400 @enderror">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">مدة التنفيذ المتوقعة</label>
                    <input type="text" name="estimated_duration" value="{{ old('estimated_duration') }}"
                        placeholder="مثال: يومان، 3 ساعات"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-brand">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">رسالتك للعميل *</label>
                <textarea name="message" rows="4" required
                    placeholder="اشرح خبرتك في هذا المجال، ولماذا أنت الأنسب لهذا الطلب..."
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-brand resize-none @error('message') border-red-400 @enderror">{{ old('message') }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">مرفقات (اختياري)</label>
                <input type="file" name="attachments[]" multiple accept="image/*,.pdf"
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-brand-light file:text-brand hover:file:bg-brand hover:file:text-white">
            </div>
            <button type="submit"
                class="w-full bg-brand text-white text-sm font-bold py-3 rounded-xl hover:bg-brand-dark transition-colors">
                تقديم العرض
            </button>
        </form>
    </div>
    @else
    <div class="bg-orange-50 border border-orange-200 rounded-2xl p-5 text-center">
        <p class="text-3xl mb-2">🔒</p>
        <p class="font-bold text-orange-800">اشتراك مطلوب</p>
        <p class="text-sm text-orange-700 mt-1">يجب أن يكون لديك اشتراك نشط في السوق الحر لتقديم عروض.</p>
    </div>
    @endif
    @endif

</div>
@endsection
