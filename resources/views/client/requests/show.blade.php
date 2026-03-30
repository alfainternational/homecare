@extends('layouts.dashboard')

@section('title', 'تفاصيل الطلب #' . ($serviceRequest->request_number ?? str_pad($serviceRequest->id, 5, '0', STR_PAD_LEFT)))

@push('styles')
<style>
    .timeline-step { position: relative; }
    .timeline-step:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 2rem;
        right: 0.9rem;
        width: 2px;
        height: calc(100% - 0.5rem);
        background: #E5E7EB;
        z-index: 0;
    }
    .timeline-step.step-done:not(:last-child)::after { background: #F5A623; }
    .timeline-step.step-active:not(:last-child)::after { background: linear-gradient(to bottom, #F5A623 50%, #E5E7EB 100%); }
    .timeline-dot {
        width: 2rem; height: 2rem; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.75rem; font-weight: 700;
        border: 2px solid #E5E7EB;
        background: white;
        position: relative; z-index: 1;
        flex-shrink: 0;
    }
    .timeline-dot.done { background: #F5A623; border-color: #F5A623; color: white; }
    .timeline-dot.active { background: white; border-color: #F5A623; color: #F5A623; box-shadow: 0 0 0 4px #FFF3DC; }
    .timeline-dot.pending { background: white; border-color: #E5E7EB; color: #9CA3AF; }
</style>
@endpush

@section('content')

@php
    $statusMap = [
        'pending'           => ['label' => 'قيد الانتظار',    'class' => 'bg-yellow-100 text-yellow-800 border border-yellow-200'],
        'assigned'          => ['label' => 'تم التعيين',       'class' => 'bg-blue-100 text-blue-800 border border-blue-200'],
        'on_way'            => ['label' => 'في الطريق',         'class' => 'bg-blue-100 text-blue-800 border border-blue-200'],
        'arrived'           => ['label' => 'وصل الفني',         'class' => 'bg-blue-100 text-blue-800 border border-blue-200'],
        'in_progress'       => ['label' => 'جاري التنفيذ',      'class' => 'bg-blue-100 text-blue-800 border border-blue-200'],
        'awaiting_approval' => ['label' => 'بانتظار الموافقة',  'class' => 'bg-purple-100 text-purple-800 border border-purple-200'],
        'completed'         => ['label' => 'مكتمل',             'class' => 'bg-green-100 text-green-800 border border-green-200'],
        'cancelled'         => ['label' => 'ملغي',              'class' => 'bg-red-100 text-red-800 border border-red-200'],
    ];
    $typeMap = [
        'plumbing'   => ['label' => 'سباكة',  'icon' => '🔧'],
        'electrical' => ['label' => 'كهرباء', 'icon' => '⚡'],
        'hvac'       => ['label' => 'تكييف',  'icon' => '❄️'],
        'general'    => ['label' => 'عام',    'icon' => '🏠'],
    ];
    $severityMap = [
        'low'      => ['label' => 'منخفضة',  'class' => 'bg-green-100 text-green-700'],
        'medium'   => ['label' => 'متوسطة',  'class' => 'bg-yellow-100 text-yellow-700'],
        'high'     => ['label' => 'عالية',    'class' => 'bg-red-100 text-red-700'],
        'critical' => ['label' => 'حرجة',     'class' => 'bg-red-200 text-red-900'],
    ];

    $status   = $statusMap[$serviceRequest->status] ?? ['label' => $serviceRequest->status, 'class' => 'bg-gray-100 text-gray-700 border border-gray-200'];
    $type     = $typeMap[$serviceRequest->type]     ?? ['label' => $serviceRequest->type, 'icon' => '🔨'];
    $reqNum   = $serviceRequest->request_number ?? str_pad($serviceRequest->id, 5, '0', STR_PAD_LEFT);

    // Determine active timeline step from status
    $currentStatus = $serviceRequest->status;
    $stepDoneMap = [
        'pending'           => 0,
        'assigned'          => 1,
        'on_way'            => 2,
        'arrived'           => 2,
        'in_progress'       => 4,
        'awaiting_approval' => 3,
        'completed'         => 6,
        'cancelled'         => -1,
    ];
    $activeBeyond = $stepDoneMap[$currentStatus] ?? 0;

    $initialReport = $serviceRequest->initialReport ?? null;
    $initialReportSeverity = $severityMap[$initialReport->severity ?? ''] ?? ['label' => 'متوسطة', 'class' => 'bg-yellow-100 text-yellow-700'];
@endphp

{{-- ===== PAGE HEADER BREADCRUMB ===== --}}
<div class="flex items-center gap-2 text-sm text-gray-500 mb-5">
    <a href="{{ route('client.requests.index') }}" class="hover:text-brand transition-colors font-medium">طلباتي</a>
    <svg class="w-4 h-4 rotate-180 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
    </svg>
    <span class="text-accent font-semibold">تفاصيل الطلب #{{ $reqNum }}</span>
</div>

{{-- ===== REQUEST HEADER CARD ===== --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-brand-light rounded-2xl flex items-center justify-center text-3xl">
                {{ $type['icon'] }}
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <h2 class="text-xl font-black text-accent">{{ $type['label'] }}</h2>
                    <span class="font-mono text-sm text-gray-400 font-semibold">#{{ $reqNum }}</span>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $status['class'] }}">
                        {{ $status['label'] }}
                    </span>
                    <span class="text-xs text-gray-400">
                        {{ $serviceRequest->created_at->format('d/m/Y — H:i') }}
                    </span>
                </div>
            </div>
        </div>

        @if($serviceRequest->status === 'pending')
        <form method="POST" action="{{ route('client.requests.destroy', $serviceRequest) }}"
              onsubmit="return confirm('هل أنت متأكد من إلغاء هذا الطلب؟')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="flex items-center gap-2 text-red-500 border border-red-200 hover:bg-red-50 px-4 py-2 rounded-xl text-sm font-semibold transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                إلغاء الطلب
            </button>
        </form>
        @endif
    </div>
</div>

{{-- ===== MAIN 2-COL LAYOUT ===== --}}
<div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

    {{-- ===== LEFT / MAIN: TIMELINE (60%) ===== --}}
    <div class="lg:col-span-3 space-y-4">

        {{-- Timeline Card --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-bold text-accent text-base mb-6">مراحل الطلب</h3>

            <div class="space-y-0">

                @php
                function timelineStep($stepIndex, $activeBeyond, $currentStatus, $label, $sublabel, $extra = '') {
                    if ($currentStatus === 'cancelled') {
                        $state = $stepIndex < $activeBeyond ? 'done' : 'pending';
                    } else {
                        $state = $stepIndex < $activeBeyond ? 'done' : ($stepIndex === $activeBeyond ? 'active' : 'pending');
                    }
                    return ['state' => $state, 'label' => $label, 'sublabel' => $sublabel, 'extra' => $extra];
                }

                $techName = $serviceRequest->technician->name ?? 'لم يُعيَّن بعد';

                $timelineSteps = [
                    timelineStep(0, $activeBeyond, $currentStatus, 'تم استلام الطلب', 'تم إرسال طلبك وهو قيد المراجعة'),
                    timelineStep(1, $activeBeyond, $currentStatus, 'تم تعيين الفني', $serviceRequest->technician ? 'الفني: ' . $techName : 'جاري البحث عن أفضل فني'),
                    timelineStep(2, $activeBeyond, $currentStatus, 'الفني في الطريق', 'سيصل الفني إلى موقعك قريباً'),
                    timelineStep(3, $activeBeyond, $currentStatus, 'التقرير الأولي', $initialReport ? 'تم رفع التقرير — بانتظار موافقتك' : 'في انتظار فحص الفني للمشكلة'),
                    timelineStep(4, $activeBeyond, $currentStatus, 'جاري التنفيذ', 'الفني يعمل على إصلاح المشكلة'),
                    timelineStep(5, $activeBeyond, $currentStatus, 'التقرير النهائي', 'تم إتمام العمل وتقديم التقرير النهائي'),
                    timelineStep(6, $activeBeyond, $currentStatus, 'تقييم الخدمة', 'شاركنا تقييمك لتحسين خدمتنا'),
                ];
                @endphp

                @foreach($timelineSteps as $i => $ts)
                <div class="timeline-step {{ $ts['state'] === 'done' ? 'step-done' : ($ts['state'] === 'active' ? 'step-active' : '') }} flex gap-4 pb-6">
                    {{-- Dot --}}
                    <div>
                        <div class="timeline-dot {{ $ts['state'] }}">
                            @if($ts['state'] === 'done')
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            @elseif($ts['state'] === 'active')
                            <div class="w-2.5 h-2.5 bg-brand rounded-full"></div>
                            @else
                            <span class="text-xs">{{ $i + 1 }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 pt-0.5 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="font-bold text-sm {{ $ts['state'] === 'pending' ? 'text-gray-400' : 'text-accent' }}">
                                    {{ $ts['label'] }}
                                </p>
                                <p class="text-xs {{ $ts['state'] === 'pending' ? 'text-gray-300' : 'text-gray-500' }} mt-0.5 leading-relaxed">
                                    {{ $ts['sublabel'] }}
                                </p>
                            </div>
                            @if($ts['state'] === 'active')
                            <span class="text-xs bg-brand/20 text-brand-dark font-semibold px-2 py-0.5 rounded-full flex-shrink-0">
                                الحالي
                            </span>
                            @endif
                        </div>

                        {{-- Special: Awaiting Approval Buttons --}}
                        @if($i === 3 && $currentStatus === 'awaiting_approval' && $initialReport)
                        <div class="mt-3 p-4 bg-purple-50 rounded-xl border border-purple-100">
                            <p class="text-sm font-bold text-purple-800 mb-1">بانتظار موافقتك على التقرير الأولي</p>
                            <p class="text-xs text-purple-600 mb-3">وصف الفني المشكلة وقدّم تقديراً بالتكلفة والوقت. راجع التفاصيل وأكد للبدء.</p>
                            <div class="flex gap-3">
                                <form method="POST" action="{{ route('client.requests.approveReport', $serviceRequest) }}">
                                    @csrf
                                    <button type="submit"
                                            class="flex items-center gap-1.5 bg-green-500 hover:bg-green-600 text-white font-bold px-4 py-2 rounded-lg text-sm transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        موافق، ابدأ العمل
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('client.requests.rejectReport', $serviceRequest) }}">
                                    @csrf
                                    <button type="submit"
                                            class="flex items-center gap-1.5 border border-red-200 text-red-600 hover:bg-red-50 font-semibold px-4 py-2 rounded-lg text-sm transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        رفض
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endif

                        {{-- Special: Rating step --}}
                        @if($i === 6 && $currentStatus === 'completed' && !$serviceRequest->rating)
                        <div class="mt-3">
                            <a href="{{ route('client.requests.rate', $serviceRequest) }}"
                               class="inline-flex items-center gap-2 bg-yellow-400 hover:bg-yellow-500 text-white font-bold px-4 py-2 rounded-lg text-sm transition-colors">
                                ⭐ قيّم الخدمة الآن
                            </a>
                        </div>
                        @endif
                        @if($i === 6 && $serviceRequest->rating)
                        <div class="mt-2 flex items-center gap-1">
                            @for($r = 1; $r <= 5; $r++)
                            <svg class="w-4 h-4 {{ $r <= $serviceRequest->rating ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                            @endfor
                            <span class="text-xs text-gray-500 mr-1">{{ $serviceRequest->rating }}/5</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach

                {{-- Cancelled --}}
                @if($currentStatus === 'cancelled')
                <div class="flex gap-4 pt-2">
                    <div class="w-8 h-8 rounded-full bg-red-100 border-2 border-red-300 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div class="pt-1">
                        <p class="font-bold text-red-600 text-sm">تم إلغاء الطلب</p>
                        <p class="text-xs text-red-400 mt-0.5">{{ $serviceRequest->updated_at->format('d/m/Y — H:i') }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Description Card --}}
        @if($serviceRequest->description)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-bold text-accent text-sm mb-3">وصف المشكلة</h3>
            <p class="text-sm text-gray-600 leading-relaxed">{{ $serviceRequest->description }}</p>
        </div>
        @endif

        {{-- Media --}}
        @if($serviceRequest->media && $serviceRequest->media->count() > 0)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-bold text-accent text-sm mb-4">الصور والمرفقات</h3>
            <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                @foreach($serviceRequest->media as $media)
                <a href="{{ $media->url ?? $media->original_url ?? '#' }}" target="_blank"
                   class="aspect-square rounded-xl overflow-hidden bg-gray-100 block hover:opacity-90 transition-opacity">
                    <img src="{{ $media->thumbnail_url ?? $media->url ?? $media->original_url }}"
                         alt="صورة"
                         class="w-full h-full object-cover" />
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- ===== RIGHT SIDEBAR (40%) ===== --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Technician Card --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-bold text-accent text-sm mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                الفني المكلف
            </h3>

            @if($serviceRequest->technician)
            <div class="flex items-center gap-3 mb-4">
                <div class="w-14 h-14 bg-gradient-to-br from-brand to-brand-dark rounded-2xl flex items-center justify-center text-white font-black text-xl">
                    {{ mb_substr($serviceRequest->technician->name, 0, 1) }}
                </div>
                <div>
                    <p class="font-black text-accent">{{ $serviceRequest->technician->name }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $serviceRequest->technician->specialization ?? $type['label'] }}</p>
                    <div class="flex items-center gap-1 mt-1">
                        @for($r = 1; $r <= 5; $r++)
                        <svg class="w-3 h-3 {{ $r <= ($serviceRequest->technician->rating ?? 5) ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        @endfor
                        <span class="text-xs text-gray-500 mr-0.5">{{ number_format($serviceRequest->technician->rating ?? 4.9, 1) }}</span>
                    </div>
                </div>
            </div>

            @if(in_array($currentStatus, ['assigned', 'on_way', 'arrived', 'in_progress']))
            <a href="tel:{{ $serviceRequest->technician->phone ?? '' }}"
               class="w-full flex items-center justify-center gap-2 bg-brand hover:bg-brand-dark text-white font-bold py-2.5 rounded-xl transition-colors text-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                تواصل مع الفني
            </a>
            @endif
            @else
            <div class="py-6 flex flex-col items-center text-center text-gray-400">
                <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mb-2">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium">لم يُعيَّن فني بعد</p>
                <p class="text-xs mt-0.5">جاري البحث عن أقرب فني متخصص</p>
            </div>
            @endif
        </div>

        {{-- Initial Report Card --}}
        @if($initialReport)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-accent text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    التقرير الأولي
                </h3>
                @if($currentStatus === 'awaiting_approval')
                <span class="text-xs bg-purple-100 text-purple-700 font-semibold px-2 py-0.5 rounded-full">يحتاج موافقة</span>
                @endif
            </div>

            <div class="space-y-3">
                <div>
                    <p class="text-xs font-semibold text-gray-500 mb-1">وصف المشكلة</p>
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $initialReport->description }}</p>
                </div>

                <div class="flex items-center gap-3">
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-gray-500 mb-1">درجة الخطورة</p>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $initialReportSeverity['class'] }}">
                            {{ $initialReportSeverity['label'] }}
                        </span>
                    </div>
                    @if($initialReport->estimated_duration)
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-gray-500 mb-1">الوقت المتوقع</p>
                        <p class="text-sm font-bold text-accent">{{ $initialReport->estimated_duration }}</p>
                    </div>
                    @endif
                </div>

                @if($initialReport->estimated_cost)
                <div class="bg-brand-light rounded-xl p-3">
                    <p class="text-xs font-semibold text-gray-600 mb-0.5">التكلفة التقديرية</p>
                    <p class="text-lg font-black text-brand">{{ number_format($initialReport->estimated_cost, 0) }} <span class="text-sm font-semibold text-brand-dark">ر.س</span></p>
                </div>
                @endif

                @if($currentStatus === 'awaiting_approval')
                <div class="flex gap-2 pt-2">
                    <form method="POST" action="{{ route('client.requests.approveReport', $serviceRequest) }}" class="flex-1">
                        @csrf
                        <button type="submit"
                                class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2.5 rounded-xl text-sm transition-colors">
                            ✓ موافق
                        </button>
                    </form>
                    <form method="POST" action="{{ route('client.requests.rejectReport', $serviceRequest) }}" class="flex-1">
                        @csrf
                        <button type="submit"
                                class="w-full border-2 border-red-200 text-red-600 hover:bg-red-50 font-bold py-2.5 rounded-xl text-sm transition-colors">
                            ✗ رفض
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Address Card --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-bold text-accent text-sm mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                عنوان الخدمة
            </h3>
            <p class="text-sm text-gray-600 leading-relaxed">
                {{ $serviceRequest->street ?? '' }}
                @if($serviceRequest->district), حي {{ $serviceRequest->district }}@endif
                @if($serviceRequest->city)، {{ $serviceRequest->city }}@endif
            </p>
            @if($serviceRequest->notes)
            <p class="text-xs text-gray-400 mt-2 bg-gray-50 rounded-lg px-3 py-2">
                💬 {{ $serviceRequest->notes }}
            </p>
            @endif
        </div>

    </div>
</div>

@endsection
