@extends('layouts.tech')

@section('title', 'لوحة الفني')
@section('page-title', 'لوحة الفني')

@section('content')

@php
    $techProfile = $profile ?? $user->technicianProfile ?? null;
    $currentStatus = $techProfile?->status ?? 'available';
    $todayCount = $stats['today_count'] ?? count($todayTasks);
    $monthCount = $stats['month_count'] ?? $monthTasks ?? 0;
    $ratingAvg = $techProfile?->rating_average ?? '4.9';
@endphp

<div class="space-y-6">

    {{-- ===== TOP STATS ROW ===== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- مهام اليوم --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium mb-1">مهام اليوم</p>
                    <p class="text-3xl font-black text-[#2C2C2A]">{{ $todayCount }}</p>
                </div>
                <div class="w-11 h-11 bg-[#FFF3DC] rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-[#F5A623]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-3">مهام مجدولة اليوم</p>
        </div>

        {{-- التقييم الشهري --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium mb-1">التقييم الشهري</p>
                    <p class="text-3xl font-black text-amber-500">{{ $ratingAvg }}</p>
                </div>
                <div class="w-11 h-11 bg-amber-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-amber-400" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                </div>
            </div>
            <div class="flex items-center gap-0.5 mt-3">
                @for($i = 1; $i <= 5; $i++)
                    <svg class="w-3.5 h-3.5 {{ $i <= round((float)$ratingAvg) ? 'text-amber-400' : 'text-gray-200' }}" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                @endfor
            </div>
        </div>

        {{-- مهام هذا الشهر --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium mb-1">مهام هذا الشهر</p>
                    <p class="text-3xl font-black text-[#2C2C2A]">{{ $monthCount }}</p>
                </div>
                <div class="w-11 h-11 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-3">{{ now()->isoFormat('MMMM YYYY') }}</p>
        </div>

        {{-- الحالة الحالية --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium mb-2">الحالة الحالية</p>
                    @if($currentStatus === 'available')
                        <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 text-xs font-bold px-3 py-1.5 rounded-full">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>متاح
                        </span>
                    @elseif($currentStatus === 'busy')
                        <span class="inline-flex items-center gap-1.5 bg-yellow-100 text-yellow-700 text-xs font-bold px-3 py-1.5 rounded-full">
                            <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>مشغول
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-500 text-xs font-bold px-3 py-1.5 rounded-full">
                            <span class="w-2 h-2 bg-gray-400 rounded-full"></span>في إجازة
                        </span>
                    @endif
                </div>
                <div class="w-11 h-11 bg-green-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-3">عدّل حالتك أدناه</p>
        </div>
    </div>

    {{-- ===== STATUS TOGGLE ===== --}}
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <h2 class="text-base font-bold text-[#2C2C2A] mb-1">تحديث حالتك</h2>
        <p class="text-sm text-gray-500 mb-5">اختر حالتك ليتمكن النظام من تعيين المهام المناسبة لك</p>

        <form action="{{ route('tech.status') }}" method="POST" x-data="{ selected: '{{ $currentStatus }}' }">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

                {{-- متاح --}}
                <button type="submit" name="status" value="available"
                        @click="selected = 'available'"
                        class="flex flex-col sm:flex-row items-center gap-3 p-4 rounded-2xl border-2 transition-all duration-200 w-full text-right
                               {{ $currentStatus === 'available' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-green-300 hover:bg-green-50/40' }}">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-2xl flex-shrink-0">✅</div>
                    <div>
                        <p class="font-bold text-green-700 text-sm">متاح</p>
                        <p class="text-xs text-gray-500 mt-0.5">جاهز لاستقبال المهام</p>
                    </div>
                    @if($currentStatus === 'available')
                        <svg class="w-4 h-4 text-green-600 mr-auto" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    @endif
                </button>

                {{-- مشغول --}}
                <button type="submit" name="status" value="busy"
                        class="flex flex-col sm:flex-row items-center gap-3 p-4 rounded-2xl border-2 transition-all duration-200 w-full text-right
                               {{ $currentStatus === 'busy' ? 'border-yellow-500 bg-yellow-50' : 'border-gray-200 hover:border-yellow-300 hover:bg-yellow-50/40' }}">
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center text-2xl flex-shrink-0">⏳</div>
                    <div>
                        <p class="font-bold text-yellow-700 text-sm">مشغول</p>
                        <p class="text-xs text-gray-500 mt-0.5">في منتصف مهمة حالية</p>
                    </div>
                    @if($currentStatus === 'busy')
                        <svg class="w-4 h-4 text-yellow-600 mr-auto" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    @endif
                </button>

                {{-- في إجازة --}}
                <button type="submit" name="status" value="off"
                        class="flex flex-col sm:flex-row items-center gap-3 p-4 rounded-2xl border-2 transition-all duration-200 w-full text-right
                               {{ $currentStatus === 'off' ? 'border-gray-400 bg-gray-50' : 'border-gray-200 hover:border-gray-400 hover:bg-gray-50/60' }}">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center text-2xl flex-shrink-0">🚫</div>
                    <div>
                        <p class="font-bold text-gray-600 text-sm">في إجازة</p>
                        <p class="text-xs text-gray-500 mt-0.5">غير متاح مؤقتاً</p>
                    </div>
                    @if($currentStatus === 'off')
                        <svg class="w-4 h-4 text-gray-500 mr-auto" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    @endif
                </button>
            </div>
        </form>
    </div>

    {{-- ===== TODAY'S TASKS ===== --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h2 class="text-base font-bold text-[#2C2C2A]">مهام اليوم</h2>
                <p class="text-xs text-gray-500 mt-0.5">{{ now()->format('l، d/m/Y') }}</p>
            </div>
            @if(count($todayTasks) > 0)
                <span class="bg-[#FFF3DC] text-[#D4881A] text-xs font-bold px-3 py-1.5 rounded-full">
                    {{ count($todayTasks) }} {{ count($todayTasks) === 1 ? 'مهمة' : 'مهام' }}
                </span>
            @endif
        </div>

        @if(count($todayTasks) === 0)
            {{-- Empty state --}}
            <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                <div class="w-20 h-20 bg-[#FFF3DC] rounded-full flex items-center justify-center text-4xl mb-4">☕</div>
                <h3 class="text-base font-bold text-[#2C2C2A] mb-2">لا مهام اليوم — استرح! ☕</h3>
                <p class="text-sm text-gray-500 max-w-xs">ليس لديك أي مهام مجدولة لهذا اليوم. استمتع بوقتك!</p>
            </div>
        @else
            <div class="divide-y divide-gray-50">
                @foreach($todayTasks as $task)
                <div class="px-6 py-5 hover:bg-gray-50/50 transition-colors">
                    <div class="flex items-start gap-4">

                        {{-- Icon --}}
                        <div class="w-11 h-11 bg-[#FFF3DC] rounded-xl flex items-center justify-center flex-shrink-0 text-lg">
                            @switch($task->service_type)
                                @case('plumbing') 🔧 @break
                                @case('electrical') ⚡ @break
                                @case('hvac') ❄️ @break
                                @default 🔨
                            @endswitch
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <div>
                                    <p class="font-bold text-[#2C2C2A] text-sm leading-tight">{{ $task->client->name ?? 'عميل' }}</p>
                                    <p class="text-sm text-gray-600 mt-0.5">{{ $task->service_type_label }}</p>
                                </div>
                                @php
                                    $statusMap = [
                                        'assigned'          => ['bg-blue-100 text-blue-700',       'تم التعيين'],
                                        'on_way'            => ['bg-purple-100 text-purple-700',    'في الطريق'],
                                        'arrived'           => ['bg-orange-100 text-orange-700',    'وصل الفني'],
                                        'in_progress'       => ['bg-[#FFF3DC] text-[#D4881A]',     'جاري التنفيذ'],
                                        'awaiting_approval' => ['bg-amber-100 text-amber-700',      'ينتظر موافقتك'],
                                        'completed'         => ['bg-green-100 text-green-700',      'مكتمل'],
                                        'cancelled'         => ['bg-red-100 text-red-600',           'ملغي'],
                                    ];
                                    [$badgeClass, $badgeLabel] = $statusMap[$task->status] ?? ['bg-gray-100 text-gray-600', $task->status_label];
                                @endphp
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $badgeClass }} flex-shrink-0">
                                    {{ $badgeLabel }}
                                </span>
                            </div>

                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2.5">
                                @if($task->description)
                                <div class="flex items-center gap-1.5 text-xs text-gray-500">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span>{{ Str::limit($task->description, 55) }}</span>
                                </div>
                                @endif
                                @if($task->scheduled_at)
                                <div class="flex items-center gap-1.5 text-xs text-gray-500">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>{{ $task->scheduled_at->format('h:i A') }}</span>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Button --}}
                        <a href="{{ route('tech.tasks.show', $task) }}"
                           class="flex-shrink-0 bg-[#F5A623] hover:bg-[#D4881A] text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-colors whitespace-nowrap shadow-sm">
                            عرض التفاصيل
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
