@extends('layouts.admin')

@section('title', 'إعدادات السوق الحر')
@section('page-title', 'إعدادات السوق الحر')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h2 class="text-xl font-bold text-accent">إعدادات السوق الحر</h2>
            <p class="text-sm text-gray-500 mt-1">التحكم في نموذج الفنيين والعمولات والأسعار</p>
        </div>
        <a href="{{ route('admin.marketplace.subscriptions') }}"
            class="text-sm font-semibold text-brand border border-brand px-4 py-2 rounded-xl hover:bg-brand hover:text-white transition-colors">
            اشتراكات الفنيين
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <p class="text-xs text-gray-500 font-medium mb-1">إجمالي المنشورات</p>
            <p class="text-3xl font-black text-accent">{{ $stats['total_posts'] }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <p class="text-xs text-gray-500 font-medium mb-1">منشورات مفتوحة</p>
            <p class="text-3xl font-black text-brand">{{ $stats['open_posts'] }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <p class="text-xs text-gray-500 font-medium mb-1">فنيون نشطون</p>
            <p class="text-3xl font-black text-accent">{{ $stats['active_technicians'] }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <p class="text-xs text-gray-500 font-medium mb-1">اشتراكات فعّالة</p>
            <p class="text-3xl font-black text-green-600">{{ $stats['active_tech_subs'] }}</p>
        </div>
    </div>

    {{-- Settings Form --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
        <div class="px-6 py-5 border-b border-gray-100">
            <h3 class="font-bold text-accent">إعدادات النظام</h3>
        </div>
        <form action="{{ route('admin.marketplace.settings') }}" method="POST" class="p-6 space-y-6">
            @csrf

            {{-- Technician Model --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">نموذج الفنيين</label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @foreach([
                        ['value' => 'subscription', 'label' => 'اشتراك شهري/سنوي', 'desc' => 'الفني يدفع اشتراكاً للوصول للطلبات', 'icon' => '📋'],
                        ['value' => 'commission', 'label' => 'عمولة على الصفقة', 'desc' => 'خصم نسبة من كل صفقة مكتملة', 'icon' => '💰'],
                        ['value' => 'both', 'label' => 'الخيارين معاً', 'desc' => 'الفني يختار نموذجه المناسب', 'icon' => '🔄'],
                    ] as $model)
                    <label class="cursor-pointer">
                        <input type="radio" name="technician_model" value="{{ $model['value'] }}"
                            class="peer sr-only"
                            {{ ($settings['technician_model'] ?? 'both') === $model['value'] ? 'checked' : '' }}>
                        <div class="border-2 border-gray-200 peer-checked:border-brand peer-checked:bg-brand-light rounded-xl p-4 transition-all">
                            <p class="text-xl mb-2">{{ $model['icon'] }}</p>
                            <p class="font-semibold text-sm text-accent">{{ $model['label'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $model['desc'] }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Pricing --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">نسبة العمولة (%)</label>
                    <input type="number" name="commission_rate" value="{{ $settings['commission_rate'] ?? 10 }}"
                        min="0" max="50" step="0.5"
                        class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-brand">
                    <p class="text-xs text-gray-400 mt-1">نسبة مئوية من كل صفقة</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">سعر الاشتراك الشهري (ر.س)</label>
                    <input type="number" name="monthly_sub_price" value="{{ $settings['monthly_sub_price'] ?? 99 }}"
                        min="0" step="1"
                        class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-brand">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">سعر الاشتراك السنوي (ر.س)</label>
                    <input type="number" name="annual_sub_price" value="{{ $settings['annual_sub_price'] ?? 899 }}"
                        min="0" step="1"
                        class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-brand">
                </div>
            </div>

            {{-- Job Post Settings --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">مدة صلاحية المنشور (أيام)</label>
                    <input type="number" name="post_expiry_days" value="{{ $settings['post_expiry_days'] ?? 14 }}"
                        min="1" max="90"
                        class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-brand">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">الحد الأقصى للعروض على المنشور</label>
                    <input type="number" name="max_bids_per_post" value="{{ $settings['max_bids_per_post'] ?? 10 }}"
                        min="1" max="50"
                        class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-brand">
                </div>
            </div>

            {{-- Toggles --}}
            <div class="space-y-3">
                <label class="flex items-center justify-between p-4 bg-gray-50 rounded-xl cursor-pointer">
                    <div>
                        <p class="text-sm font-semibold text-accent">تفعيل السوق الحر</p>
                        <p class="text-xs text-gray-500 mt-0.5">السماح للعملاء بنشر طلبات وللفنيين بتقديم عروض</p>
                    </div>
                    <input type="checkbox" name="marketplace_enabled" value="1"
                        class="w-5 h-5 text-brand rounded border-gray-300 focus:ring-brand"
                        {{ ($settings['marketplace_enabled'] ?? true) ? 'checked' : '' }}>
                </label>
                <label class="flex items-center justify-between p-4 bg-gray-50 rounded-xl cursor-pointer">
                    <div>
                        <p class="text-sm font-semibold text-accent">مراجعة المنشورات قبل النشر</p>
                        <p class="text-xs text-gray-500 mt-0.5">يراجع المشرف كل منشور قبل ظهوره للفنيين</p>
                    </div>
                    <input type="checkbox" name="require_post_review" value="1"
                        class="w-5 h-5 text-brand rounded border-gray-300 focus:ring-brand"
                        {{ ($settings['require_post_review'] ?? false) ? 'checked' : '' }}>
                </label>
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="bg-brand text-white text-sm font-semibold px-8 py-2.5 rounded-xl hover:bg-brand-dark transition-colors">
                    حفظ الإعدادات
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
