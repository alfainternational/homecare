@extends('layouts.dashboard')

@section('title', 'نشر طلب جديد')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('client.marketplace.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h2 class="text-xl font-bold text-accent">نشر طلب جديد</h2>
            <p class="text-sm text-gray-500 mt-0.5">صف ما تحتاجه وحدد ميزانيتك</p>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
        <ul class="text-sm text-red-700 space-y-1 list-disc list-inside">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('client.marketplace.store') }}" method="POST" enctype="multipart/form-data"
        class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
        @csrf

        {{-- Title --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">عنوان الطلب *</label>
            <input type="text" name="title" value="{{ old('title') }}" required
                placeholder="مثال: تركيب مكيف سبليت في غرفتين"
                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand @error('title') border-red-400 @enderror">
        </div>

        {{-- Category --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">فئة الخدمة *</label>
            <select name="service_category_id" required
                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand @error('service_category_id') border-red-400 @enderror">
                <option value="">اختر الفئة المناسبة</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('service_category_id') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->icon }} {{ $cat->name_ar }}
                </option>
                @endforeach
            </select>
        </div>

        {{-- Description --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">وصف الطلب *</label>
            <textarea name="description" rows="4" required
                placeholder="اشرح المشكلة أو العمل المطلوب بالتفصيل، كلما كانت التفاصيل أوضح كلما تلقيت عروضاً أدق..."
                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand resize-none @error('description') border-red-400 @enderror">{{ old('description') }}</textarea>
        </div>

        {{-- Budget --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">الميزانية المتوقعة (ر.س)</label>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">من</label>
                    <input type="number" name="budget_min" value="{{ old('budget_min') }}" min="0" step="1"
                        placeholder="0"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">إلى</label>
                    <input type="number" name="budget_max" value="{{ old('budget_max') }}" min="0" step="1"
                        placeholder="0"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand">
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-1.5">اتركها فارغة إذا أردت عروضاً مفتوحة</p>
        </div>

        {{-- Address --}}
        @if($addresses->isNotEmpty())
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">موقع تنفيذ الخدمة</label>
            <select name="address_id"
                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand">
                <option value="">غير محدد</option>
                @foreach($addresses as $address)
                <option value="{{ $address->id }}" {{ old('address_id') == $address->id || $address->is_primary ? 'selected' : '' }}>
                    {{ $address->type_icon }} {{ $address->full_address }}
                </option>
                @endforeach
            </select>
        </div>
        @endif

        {{-- Media --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">صور/فيديو (اختياري)</label>
            <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-brand transition-colors">
                <input type="file" name="media[]" multiple accept="image/*,video/*" class="hidden" id="mediaInput"
                    onchange="updateFileCount(this)">
                <label for="mediaInput" class="cursor-pointer">
                    <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm text-gray-500">اضغط لاختيار الصور أو اسحبها هنا</p>
                    <p class="text-xs text-gray-400 mt-1" id="fileCount">حتى 5 ملفات · صور وفيديو مقبول</p>
                </label>
            </div>
        </div>

        {{-- Expiry --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">تاريخ انتهاء الطلب</label>
            <input type="date" name="expires_at" value="{{ old('expires_at', now()->addDays(14)->format('Y-m-d')) }}"
                min="{{ now()->addDay()->format('Y-m-d') }}" max="{{ now()->addDays(90)->format('Y-m-d') }}"
                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand">
        </div>

        {{-- Submit --}}
        <div class="flex gap-3 pt-2">
            <button type="submit"
                class="flex-1 bg-brand text-white text-sm font-bold py-3 rounded-xl hover:bg-brand-dark transition-colors">
                نشر الطلب
            </button>
            <a href="{{ route('client.marketplace.index') }}"
                class="flex-1 text-center border border-gray-200 text-sm text-gray-600 font-medium py-3 rounded-xl hover:bg-gray-50 transition-colors">
                إلغاء
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function updateFileCount(input) {
    const count = input.files.length;
    document.getElementById('fileCount').textContent = count > 0 ? `${count} ملف مختار` : 'حتى 5 ملفات · صور وفيديو مقبول';
}
</script>
@endpush
@endsection
