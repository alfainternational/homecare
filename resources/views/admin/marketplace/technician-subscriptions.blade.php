@extends('layouts.admin')

@section('title', 'اشتراكات الفنيين')
@section('page-title', 'اشتراكات الفنيين')

@section('content')
<div class="space-y-6" x-data="{ showGrant: false, grantUserId: '', grantUserName: '' }">

    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h2 class="text-xl font-bold text-accent">اشتراكات الفنيين في السوق الحر</h2>
            <p class="text-sm text-gray-500 mt-1">إدارة ومنح الاشتراكات المجانية للفنيين</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.marketplace.index') }}"
                class="text-sm text-gray-600 border border-gray-200 px-4 py-2 rounded-xl hover:bg-gray-50 transition-colors">
                ← الإعدادات
            </a>
            <button @click="showGrant = true"
                class="bg-brand text-white text-sm font-semibold px-4 py-2 rounded-xl hover:bg-brand-dark transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                منح اشتراك مجاني
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    {{-- Subscriptions Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-right">
                        <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">الفني</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">الخطة</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">النموذج</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">الصلاحية</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($subscriptions as $sub)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-brand-light flex items-center justify-center text-sm font-bold text-brand flex-shrink-0">
                                    {{ mb_substr($sub->technician->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-accent">{{ $sub->technician->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $sub->technician->phone }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="text-xs font-medium px-2.5 py-1 rounded-full
                                {{ $sub->plan_type === 'annual' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $sub->plan_type === 'annual' ? 'سنوي' : 'شهري' }}
                                @if($sub->is_free) · مجاني @endif
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            @if($sub->is_commission_model)
                            <span class="text-xs text-orange-700 bg-orange-100 px-2 py-0.5 rounded-full">
                                عمولة {{ $sub->commission_rate }}%
                            </span>
                            @else
                            <span class="text-xs text-blue-700 bg-blue-100 px-2 py-0.5 rounded-full">اشتراك</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-gray-600">
                            <p class="text-xs">{{ $sub->starts_at?->format('Y/m/d') }}</p>
                            <p class="text-xs text-gray-400">حتى {{ $sub->ends_at?->format('Y/m/d') }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full
                                {{ $sub->isActive() ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $sub->isActive() ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                {{ $sub->isActive() ? 'نشط' : $sub->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-gray-400">لا توجد اشتراكات حتى الآن</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($subscriptions->hasPages())
        <div class="px-5 py-4 border-t border-gray-50">{{ $subscriptions->links() }}</div>
        @endif
    </div>

    {{-- Grant Free Sub Modal --}}
    <div x-show="showGrant" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
        @click.self="showGrant = false">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 space-y-5" @click.stop>
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-accent text-lg">منح اشتراك مجاني</h3>
                <button @click="showGrant = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('admin.marketplace.subscriptions.grant') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">الفني</label>
                    <select name="user_id" required
                        class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-brand">
                        <option value="">اختر فنياً...</option>
                        @foreach($technicians as $tech)
                        <option value="{{ $tech->id }}">{{ $tech->name }} — {{ $tech->phone }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">نوع الخطة</label>
                        <select name="plan_type" required
                            class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-brand">
                            <option value="monthly">شهري</option>
                            <option value="annual">سنوي</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">مدة الاشتراك (أشهر)</label>
                        <input type="number" name="months" value="1" min="1" max="24" required
                            class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-brand">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">ملاحظة (اختياري)</label>
                    <input type="text" name="note" placeholder="سبب منح الاشتراك المجاني..."
                        class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-brand">
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="submit"
                        class="flex-1 bg-brand text-white text-sm font-semibold py-2.5 rounded-xl hover:bg-brand-dark transition-colors">
                        منح الاشتراك
                    </button>
                    <button type="button" @click="showGrant = false"
                        class="flex-1 border border-gray-200 text-sm text-gray-600 py-2.5 rounded-xl hover:bg-gray-50 transition-colors">
                        إلغاء
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
