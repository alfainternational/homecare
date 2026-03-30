@extends('layouts.dashboard')

@section('title', 'طلباتي')

@section('content')

@php
    $typeMap = [
        'plumbing'   => ['label' => 'سباكة',  'icon' => '🔧', 'bg' => 'bg-blue-50'],
        'electrical' => ['label' => 'كهرباء', 'icon' => '⚡', 'bg' => 'bg-yellow-50'],
        'hvac'       => ['label' => 'تكييف',  'icon' => '❄️', 'bg' => 'bg-cyan-50'],
        'general'    => ['label' => 'عام',    'icon' => '🏠', 'bg' => 'bg-orange-50'],
    ];
@endphp

<div x-data="{
    activeTab: '{{ request('filter', 'all') }}',
    search: '{{ request('search', '') }}',
    setTab(tab) {
        this.activeTab = tab;
        const url = new URL(window.location.href);
        url.searchParams.set('filter', tab);
        url.searchParams.delete('page');
        window.location.href = url.toString();
    }
}">

    {{-- ===== PAGE HEADER ===== --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-black text-accent">طلباتي</h2>
            <p class="text-sm text-gray-500 mt-0.5">إدارة ومتابعة جميع طلبات الصيانة الخاصة بك</p>
        </div>
        <a href="{{ route('client.requests.create') }}"
           class="flex items-center gap-2 bg-brand hover:bg-brand-dark text-white font-bold px-5 py-2.5 rounded-xl transition-colors shadow-sm text-sm self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            طلب جديد
        </a>
    </div>

    {{-- ===== FILTER TABS + SEARCH ===== --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-5 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4">

            {{-- Filter Tabs --}}
            <div class="flex items-center gap-1 bg-gray-50 rounded-xl p-1 w-full sm:w-auto overflow-x-auto">
                <button @click="setTab('all')"
                        :class="activeTab === 'all' ? 'bg-white text-accent shadow-sm font-bold' : 'text-gray-500 hover:text-gray-700'"
                        class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium transition-all whitespace-nowrap">
                    الكل
                    <span class="text-xs bg-gray-200 text-gray-600 rounded-full px-1.5 py-0.5 font-semibold"
                          :class="activeTab === 'all' ? 'bg-brand/20 text-brand-dark' : ''">
                        {{ $requests->total() }}
                    </span>
                </button>
                <button @click="setTab('active')"
                        :class="activeTab === 'active' ? 'bg-white text-accent shadow-sm font-bold' : 'text-gray-500 hover:text-gray-700'"
                        class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium transition-all whitespace-nowrap">
                    نشطة
                    <span class="w-2 h-2 bg-blue-400 rounded-full"></span>
                </button>
                <button @click="setTab('completed')"
                        :class="activeTab === 'completed' ? 'bg-white text-accent shadow-sm font-bold' : 'text-gray-500 hover:text-gray-700'"
                        class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium transition-all whitespace-nowrap">
                    مكتملة
                    <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                </button>
                <button @click="setTab('cancelled')"
                        :class="activeTab === 'cancelled' ? 'bg-white text-accent shadow-sm font-bold' : 'text-gray-500 hover:text-gray-700'"
                        class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium transition-all whitespace-nowrap">
                    ملغاة
                    <span class="w-2 h-2 bg-red-400 rounded-full"></span>
                </button>
            </div>

            {{-- Search Bar --}}
            <form method="GET" action="{{ route('client.requests.index') }}" class="flex-shrink-0">
                <input type="hidden" name="filter" :value="activeTab" />
                <div class="relative">
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input
                        type="search"
                        name="search"
                        x-model="search"
                        placeholder="ابحث برقم الطلب..."
                        value="{{ request('search') }}"
                        class="w-full sm:w-64 bg-gray-50 border border-gray-200 rounded-xl pr-9 pl-4 py-2.5 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand"
                    />
                </div>
            </form>
        </div>
    </div>

    {{-- ===== REQUESTS LIST ===== --}}
    @if($requests->count() > 0)

    {{-- Desktop Table --}}
    <div class="hidden md:block bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-5">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="text-right text-xs font-bold text-gray-500 px-5 py-3.5 tracking-wide uppercase">رقم الطلب</th>
                    <th class="text-right text-xs font-bold text-gray-500 px-4 py-3.5 tracking-wide uppercase">نوع الخدمة</th>
                    <th class="text-right text-xs font-bold text-gray-500 px-4 py-3.5 tracking-wide uppercase">التاريخ</th>
                    <th class="text-right text-xs font-bold text-gray-500 px-4 py-3.5 tracking-wide uppercase">الفني</th>
                    <th class="text-right text-xs font-bold text-gray-500 px-4 py-3.5 tracking-wide uppercase">الحالة</th>
                    <th class="text-right text-xs font-bold text-gray-500 px-4 py-3.5 tracking-wide uppercase">الإجراء</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($requests as $request)
                @php $type = $typeMap[$request->service_type] ?? ['label' => $request->service_type, 'icon' => '🔨', 'bg' => 'bg-gray-50']; @endphp
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-5 py-4">
                        <span class="font-mono font-bold text-accent text-sm">
                            #{{ $request->request_number ?? str_pad($request->id, 5, '0', STR_PAD_LEFT) }}
                        </span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 {{ $type['bg'] }} rounded-xl flex items-center justify-center text-lg">
                                {{ $type['icon'] }}
                            </div>
                            <span class="text-sm font-semibold text-gray-800">{{ $type['label'] }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <div>
                            <p class="text-sm text-gray-700">{{ $request->created_at->format('d/m/Y') }}</p>
                            <p class="text-xs text-gray-400">{{ $request->created_at->format('H:i') }}</p>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        @if($request->technician)
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 bg-brand rounded-full flex items-center justify-center text-white text-xs font-bold">
                                {{ mb_substr($request->technician->name, 0, 1) }}
                            </div>
                            <span class="text-sm text-gray-700">{{ $request->technician->name }}</span>
                        </div>
                        @else
                        <span class="text-sm text-gray-400 italic">لم يُعيَّن بعد</span>
                        @endif
                    </td>
                    <td class="px-4 py-4">
                        <x-status-badge :status="$request->status" />
                    </td>
                    <td class="px-4 py-4">
                        <a href="{{ route('client.requests.show', $request) }}"
                           class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand hover:text-brand-dark border border-brand/30 hover:border-brand px-3 py-1.5 rounded-lg transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            التفاصيل
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mobile Cards --}}
    <div class="md:hidden space-y-3 mb-5">
        @foreach($requests as $request)
        @php $type = $typeMap[$request->service_type] ?? ['label' => $request->service_type, 'icon' => '🔨', 'bg' => 'bg-gray-50']; @endphp
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 {{ $type['bg'] }} rounded-xl flex items-center justify-center text-xl">
                        {{ $type['icon'] }}
                    </div>
                    <div>
                        <p class="font-bold text-accent text-sm">{{ $type['label'] }}</p>
                        <p class="text-xs text-gray-400 font-mono mt-0.5">
                            #{{ $request->request_number ?? str_pad($request->id, 5, '0', STR_PAD_LEFT) }}
                        </p>
                    </div>
                </div>
                <x-status-badge :status="$request->status" />
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3 text-xs text-gray-500">
                    <span>{{ $request->created_at->format('d/m/Y') }}</span>
                    @if($request->technician)
                    <span class="flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        {{ $request->technician->name }}
                    </span>
                    @endif
                </div>
                <a href="{{ route('client.requests.show', $request) }}"
                   class="text-xs font-semibold text-brand hover:text-brand-dark">
                    التفاصيل ←
                </a>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="flex justify-center">
        {{ $requests->withQueryString()->links() }}
    </div>

    @else
    {{-- ===== EMPTY STATE ===== --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm py-16 flex flex-col items-center justify-center text-center">
        <div class="w-20 h-20 bg-brand-light rounded-3xl flex items-center justify-center mb-5 text-4xl">
            📋
        </div>
        <h3 class="text-xl font-black text-accent mb-2">لا توجد طلبات بعد</h3>
        <p class="text-gray-500 text-sm max-w-sm mb-6 leading-relaxed">
            @if(request('filter') && request('filter') !== 'all')
                لا توجد طلبات في هذه الفئة. جرّب تصفية مختلفة.
            @else
                ابدأ باستخدام خدمات وورم كونسيرج واطلب أول صيانة منزلية الآن.
            @endif
        </p>
        @if(!request('filter') || request('filter') === 'all')
        <a href="{{ route('client.requests.create') }}"
           class="flex items-center gap-2 bg-brand hover:bg-brand-dark text-white font-bold px-8 py-3 rounded-xl transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            اطلب الآن
        </a>
        @else
        <button @click="setTab('all')" class="text-brand font-semibold text-sm hover:underline">
            عرض جميع الطلبات
        </button>
        @endif
    </div>
    @endif

</div>

@endsection
