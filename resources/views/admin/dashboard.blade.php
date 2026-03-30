@extends('layouts.admin')

@section('title', 'لوحة الإدارة')
@section('page-title', 'لوحة الإدارة')

@section('content')

<div class="space-y-6">

    {{-- ===== TOP STATS (4 cards) ===== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- اشتراكات نشطة --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium mb-1">اشتراك نشط</p>
                    <p class="text-3xl font-black text-accent">{{ $stats['active_subscriptions'] }}</p>
                </div>
                <div class="w-11 h-11 bg-brand-light rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-3">اشتراكات مفعّلة</p>
        </div>

        {{-- طلبات اليوم --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium mb-1">طلبات اليوم</p>
                    <p class="text-3xl font-black text-accent">{{ $stats['today_requests'] }}</p>
                </div>
                <div class="w-11 h-11 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-3">{{ now()->format('d/m/Y') }}</p>
        </div>

        {{-- فنيون متاحون --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium mb-1">فنيون متاحون</p>
                    <p class="text-3xl font-black text-accent">{{ $stats['available_technicians'] }}</p>
                </div>
                <div class="w-11 h-11 bg-green-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-3">جاهزون للعمل</p>
        </div>

        {{-- إيراد الشهر --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium mb-1">إيراد الشهر</p>
                    <p class="text-2xl font-black text-accent">{{ number_format($stats['monthly_revenue'], 0) }}</p>
                </div>
                <div class="w-11 h-11 bg-purple-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-3">ر.س — {{ now()->isoFormat('MMMM YYYY') }}</p>
        </div>
    </div>

    {{-- ===== MAIN CONTENT (2 cols) ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- LEFT 60%: الطلبات التي تحتاج تدخلاً --}}
        <div class="lg:col-span-3 space-y-4">

            {{-- Alert banner --}}
            @php $interventionCount = $criticalRequests->count(); @endphp
            @if($interventionCount > 0)
            <div class="flex items-center gap-3 bg-brand text-white px-5 py-3.5 rounded-2xl shadow-sm">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <p class="font-bold text-sm">{{ $interventionCount }} {{ $interventionCount === 1 ? 'طلب يحتاج' : 'طلبات تحتاج' }} تدخلاً فورياً</p>
            </div>
            @endif

            {{-- Table card --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h2 class="font-bold text-accent">الطلبات المعلقة</h2>
                        <a href="{{ route('admin.requests.index') }}"
                           class="text-xs text-brand hover:text-brand-dark font-semibold transition-colors">
                            عرض الكل ←
                        </a>
                    </div>
                </div>

                @if($pendingRequests->isEmpty())
                <div class="py-12 text-center">
                    <div class="w-12 h-12 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-3 text-xl">✅</div>
                    <p class="text-sm text-gray-500">لا توجد طلبات معلقة</p>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-right">
                                <th class="px-4 py-3 text-xs font-bold text-gray-500 whitespace-nowrap">#</th>
                                <th class="px-4 py-3 text-xs font-bold text-gray-500 whitespace-nowrap">العميل</th>
                                <th class="px-4 py-3 text-xs font-bold text-gray-500 whitespace-nowrap">الخدمة</th>
                                <th class="px-4 py-3 text-xs font-bold text-gray-500 whitespace-nowrap">الحالة</th>
                                <th class="px-4 py-3 text-xs font-bold text-gray-500 whitespace-nowrap">تعيين فني</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($pendingRequests as $request)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3 text-xs text-gray-400 font-mono whitespace-nowrap">{{ Str::after($request->request_number, 'WC-') }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-accent text-sm">{{ $request->client->name ?? '—' }}</p>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">{{ $request->service_type_label }}</td>
                                <td class="px-4 py-3">
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700">
                                        {{ $request->status_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <form action="{{ route('admin.requests.assign', $request) }}" method="POST"
                                          class="flex items-center gap-2">
                                        @csrf
                                        <select name="technician_id"
                                                class="text-xs border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:border-brand bg-white max-w-[130px]">
                                            <option value="">اختر فنياً...</option>
                                            @foreach($technicians as $tech)
                                                @if($tech->technicianProfile?->status === 'available')
                                                <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <button type="submit"
                                                class="bg-brand hover:bg-brand-dark text-white text-xs font-bold px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap">
                                            عيّن
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>

        {{-- RIGHT 40%: أداء الفنيين --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden h-full">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h2 class="font-bold text-accent">أداء الفنيين</h2>
                    <p class="text-xs text-gray-500 mt-0.5">{{ now()->isoFormat('MMMM YYYY') }}</p>
                </div>

                @if($technicians->isEmpty())
                <div class="py-12 text-center px-6">
                    <p class="text-sm text-gray-500">لا يوجد فنيون مسجلون</p>
                </div>
                @else
                <div class="divide-y divide-gray-50">
                    @foreach($technicians->take(6) as $index => $tech)
                    @php
                        $techProfile = $tech->technicianProfile;
                        $isTopPerformer = $index === 0;
                        $statusMap = [
                            'available' => ['متاح',    'bg-green-100 text-green-700'],
                            'busy'      => ['مشغول',    'bg-yellow-100 text-yellow-700'],
                            'off'       => ['إجازة',    'bg-gray-100 text-gray-500'],
                        ];
                        [$statusLabel, $statusClass] = $statusMap[$techProfile?->status ?? 'off'] ?? ['غير معروف', 'bg-gray-100 text-gray-500'];
                    @endphp
                    <div class="flex items-center gap-3 px-5 py-4">
                        {{-- Rank --}}
                        <div class="w-7 h-7 flex items-center justify-center flex-shrink-0">
                            @if($index === 0)
                                <span class="text-lg">🥇</span>
                            @elseif($index === 1)
                                <span class="text-lg">🥈</span>
                            @elseif($index === 2)
                                <span class="text-lg">🥉</span>
                            @else
                                <span class="text-sm font-bold text-gray-400">#{{ $index + 1 }}</span>
                            @endif
                        </div>

                        {{-- Avatar --}}
                        <div class="w-9 h-9 bg-brand-light rounded-full flex items-center justify-center font-bold text-brand text-sm flex-shrink-0">
                            {{ mb_substr($tech->name, 0, 1) }}
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="font-semibold text-accent text-sm truncate">{{ $tech->name }}</p>
                                @if($isTopPerformer)
                                    <span class="text-xs bg-brand text-white px-1.5 py-0.5 rounded-full font-bold whitespace-nowrap">الأفضل هذا الشهر 🏆</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 mt-0.5">
                                {{-- Stars --}}
                                <div class="flex items-center gap-0.5">
                                    @for($s = 1; $s <= 5; $s++)
                                        <svg class="w-3 h-3 {{ $s <= round($techProfile?->rating_average ?? 0) ? 'text-amber-400' : 'text-gray-200' }}" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                    @endfor
                                    <span class="text-xs text-gray-500 mr-0.5">{{ $techProfile?->rating_average ?? '—' }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Status badge --}}
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $statusClass }} flex-shrink-0">
                            {{ $statusLabel }}
                        </span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ===== REVENUE CHART PLACEHOLDER ===== --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-accent">رسم بياني الإيرادات</h2>
                    <p class="text-xs text-gray-500 mt-0.5">آخر 6 أشهر</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs bg-gray-100 text-gray-500 px-3 py-1.5 rounded-full font-medium cursor-pointer hover:bg-brand-light hover:text-brand transition-colors">شهري</span>
                    <span class="text-xs bg-gray-100 text-gray-500 px-3 py-1.5 rounded-full font-medium cursor-pointer hover:bg-brand-light hover:text-brand transition-colors">أسبوعي</span>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="bg-gray-50 rounded-2xl h-52 flex flex-col items-center justify-center gap-3 border-2 border-dashed border-gray-200">
                <svg class="w-10 h-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <p class="text-gray-400 font-semibold text-sm">رسم بياني الإيرادات</p>
                <p class="text-xs text-gray-400">سيتم دمج مكتبة الرسوم البيانية هنا</p>
            </div>
        </div>
    </div>

</div>
@endsection
