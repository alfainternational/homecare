@extends('layouts.tech')

@section('title', 'تفاصيل المهمة #' . $serviceRequest->request_number)
@section('page-title', 'تفاصيل المهمة')

@section('content')

@php
    $req    = $serviceRequest;
    $client = $req->client;
    $status = $req->status;
    $initialReport = $req->initialReport;
    $finalReport   = $req->finalReport;
    $media         = $req->media;

    $steps = [
        'assigned'          => 1,
        'on_way'            => 2,
        'arrived'           => 3,
        'in_progress'       => 4,
        'awaiting_approval' => 4,
        'completed'         => 5,
    ];
    $currentStep = $steps[$status] ?? 0;

    $severityMap = [
        'low'    => ['label' => 'منخفضة', 'class' => 'bg-green-100 text-green-700'],
        'medium' => ['label' => 'متوسطة', 'class' => 'bg-yellow-100 text-yellow-700'],
        'high'   => ['label' => 'عالية',  'class' => 'bg-red-100 text-red-700'],
    ];
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ===== LEFT: TIMELINE + FORMS ===== --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Request Header --}}
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xs font-mono text-gray-400 bg-gray-100 px-2.5 py-1 rounded-lg">{{ $req->request_number }}</span>
                        @php
                            $statusColors = [
                                'assigned'          => 'bg-blue-100 text-blue-700',
                                'on_way'            => 'bg-purple-100 text-purple-700',
                                'arrived'           => 'bg-orange-100 text-orange-700',
                                'in_progress'       => 'bg-[#FFF3DC] text-[#D4881A]',
                                'awaiting_approval' => 'bg-amber-100 text-amber-700',
                                'completed'         => 'bg-green-100 text-green-700',
                                'cancelled'         => 'bg-red-100 text-red-600',
                            ];
                        @endphp
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $statusColors[$status] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ $req->status_label }}
                        </span>
                    </div>
                    <h2 class="text-lg font-black text-[#2C2C2A]">{{ $client->name ?? 'عميل' }}</h2>
                    <p class="text-sm text-gray-600 mt-0.5">{{ $req->service_type_label }}</p>
                    @if($req->description)
                        <p class="text-sm text-gray-500 mt-1">{{ $req->description }}</p>
                    @endif
                </div>
                @if($req->scheduled_at)
                <div class="text-left bg-[#FFF3DC] rounded-xl p-3 flex-shrink-0">
                    <p class="text-xs text-gray-500 font-medium">الموعد المحدد</p>
                    <p class="text-base font-black text-[#D4881A] mt-0.5">{{ $req->scheduled_at->format('h:i A') }}</p>
                    <p class="text-xs text-gray-500">{{ $req->scheduled_at->format('d/m/Y') }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- ===== TIMELINE STEPPER ===== --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="font-bold text-[#2C2C2A]">مراحل تنفيذ المهمة</h3>
            </div>

            <div class="px-6 py-6">
                <div class="relative">
                    {{-- Vertical line --}}
                    <div class="absolute right-5 top-5 bottom-5 w-0.5 bg-gray-100"></div>

                    <div class="space-y-8">

                        {{-- STEP 1: استلام المهمة --}}
                        <div class="relative flex gap-5">
                            <div class="relative z-10 w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0
                                        {{ $currentStep >= 1 ? 'bg-green-500' : 'bg-gray-200' }} shadow">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div class="flex-1 pt-1.5">
                                <p class="font-bold text-sm {{ $currentStep >= 1 ? 'text-green-700' : 'text-gray-400' }}">استلام المهمة</p>
                                <p class="text-xs text-gray-500 mt-0.5">تم تعيينك لهذه المهمة بنجاح</p>
                            </div>
                        </div>

                        {{-- STEP 2: في الطريق --}}
                        <div class="relative flex gap-5">
                            <div class="relative z-10 w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0
                                        {{ $currentStep >= 2 ? 'bg-purple-500' : 'bg-gray-200' }} shadow">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1 pt-1.5">
                                <p class="font-bold text-sm {{ $currentStep >= 2 ? 'text-purple-700' : 'text-gray-400' }}">في الطريق</p>
                                @if($status === 'assigned')
                                    <p class="text-xs text-gray-500 mt-1 mb-3">أخبر العميل أنك في الطريق إليه</p>
                                    <form action="{{ route('tech.tasks.status', $req) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="on_way">
                                        <button type="submit"
                                                class="bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl transition-colors shadow-sm">
                                            🚗 بدأت التوجه للموقع
                                        </button>
                                    </form>
                                @elseif($currentStep >= 2)
                                    <p class="text-xs text-gray-500 mt-0.5">أبلغت العميل أنك في الطريق</p>
                                @else
                                    <p class="text-xs text-gray-400 mt-0.5">سيُفعَّل بعد استلام المهمة</p>
                                @endif
                            </div>
                        </div>

                        {{-- STEP 3: تم الوصول --}}
                        <div class="relative flex gap-5">
                            <div class="relative z-10 w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0
                                        {{ $currentStep >= 3 ? 'bg-orange-500' : 'bg-gray-200' }} shadow">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1 pt-1.5">
                                <p class="font-bold text-sm {{ $currentStep >= 3 ? 'text-orange-700' : 'text-gray-400' }}">تم الوصول</p>
                                @if($status === 'on_way')
                                    <p class="text-xs text-gray-500 mt-1 mb-3">وصلت للموقع؟ أكّد وصولك</p>
                                    <form action="{{ route('tech.tasks.status', $req) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="arrived">
                                        <button type="submit"
                                                class="bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold px-5 py-2.5 rounded-xl transition-colors shadow-sm">
                                            📍 وصلت للموقع
                                        </button>
                                    </form>
                                @elseif($currentStep >= 3)
                                    <p class="text-xs text-gray-500 mt-0.5">تم تأكيد الوصول للموقع</p>
                                @else
                                    <p class="text-xs text-gray-400 mt-0.5">سيُفعَّل بعد الانطلاق</p>
                                @endif
                            </div>
                        </div>

                        {{-- STEP 4: التقرير الأولي --}}
                        <div class="relative flex gap-5">
                            <div class="relative z-10 w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0
                                        {{ $currentStep >= 4 ? 'bg-[#F5A623]' : 'bg-gray-200' }} shadow">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div class="flex-1 pt-1.5">
                                <p class="font-bold text-sm {{ $currentStep >= 4 ? 'text-[#D4881A]' : 'text-gray-400' }}">التقرير الأولي</p>

                                @if($status === 'arrived' && !$initialReport)
                                    <p class="text-xs text-gray-500 mt-1 mb-4">افحص العطل وأرسل التقرير الأولي لبدء العمل</p>
                                    <form action="{{ route('tech.tasks.initial-report', $req) }}" method="POST" class="space-y-4 bg-[#FFF3DC]/40 rounded-xl p-4 border border-[#F5A623]/20">
                                        @csrf
                                        <div>
                                            <label class="block text-xs font-bold text-[#2C2C2A] mb-1.5">وصف العطل <span class="text-red-500">*</span></label>
                                            <textarea name="problem_description" rows="3" required
                                                      placeholder="اشرح المشكلة بالتفصيل..."
                                                      class="w-full text-sm border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-[#F5A623] focus:ring-1 focus:ring-[#F5A623] resize-none bg-white">{{ old('problem_description') }}</textarea>
                                        </div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-bold text-[#2C2C2A] mb-1.5">درجة الخطورة <span class="text-red-500">*</span></label>
                                                <select name="severity" required
                                                        class="w-full text-sm border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-[#F5A623] bg-white">
                                                    <option value="">اختر...</option>
                                                    <option value="low"    {{ old('severity') === 'low'    ? 'selected' : '' }}>منخفضة</option>
                                                    <option value="medium" {{ old('severity') === 'medium' ? 'selected' : '' }}>متوسطة</option>
                                                    <option value="high"   {{ old('severity') === 'high'   ? 'selected' : '' }}>عالية</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-[#2C2C2A] mb-1.5">الوقت التقديري</label>
                                                <div class="relative">
                                                    <input type="number" name="estimated_duration" min="5" step="5"
                                                           placeholder="60"
                                                           value="{{ old('estimated_duration') }}"
                                                           class="w-full text-sm border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-[#F5A623] bg-white">
                                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">دقيقة</span>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit"
                                                class="w-full bg-[#F5A623] hover:bg-[#D4881A] text-white font-bold py-3 rounded-xl transition-colors text-sm shadow-sm">
                                            إرسال التقرير الأولي وبدء العمل
                                        </button>
                                    </form>

                                @elseif($initialReport)
                                    <div class="mt-2 bg-white rounded-xl border border-gray-200 p-4 space-y-2">
                                        <div>
                                            <p class="text-xs text-gray-500">وصف المشكلة</p>
                                            <p class="text-sm text-[#2C2C2A] font-medium mt-0.5">{{ $initialReport->problem_description }}</p>
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <div>
                                                <p class="text-xs text-gray-500">الخطورة</p>
                                                @php $sv = $severityMap[$initialReport->severity] ?? ['label'=>$initialReport->severity,'class'=>'bg-gray-100 text-gray-600']; @endphp
                                                <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $sv['class'] }}">{{ $sv['label'] }}</span>
                                            </div>
                                            @if($initialReport->estimated_duration)
                                            <div>
                                                <p class="text-xs text-gray-500">الوقت التقديري</p>
                                                <p class="text-sm font-bold text-[#2C2C2A]">{{ $initialReport->estimated_duration }} دقيقة</p>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="pt-1">
                                            @if($initialReport->is_approved)
                                                <span class="inline-flex items-center gap-1.5 text-xs text-green-700 bg-green-50 px-2.5 py-1 rounded-full font-semibold">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                    موافق عليه من العميل
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 text-xs text-amber-700 bg-amber-50 px-2.5 py-1 rounded-full font-semibold">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    في انتظار موافقة العميل
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <p class="text-xs text-gray-400 mt-0.5">سيُفعَّل بعد الوصول للموقع</p>
                                @endif
                            </div>
                        </div>

                        {{-- STEP 5: جاري التنفيذ --}}
                        <div class="relative flex gap-5">
                            <div class="relative z-10 w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0
                                        {{ in_array($status, ['in_progress','awaiting_approval','completed']) ? 'bg-blue-500' : 'bg-gray-200' }} shadow">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1 pt-1.5">
                                <p class="font-bold text-sm {{ in_array($status, ['in_progress','awaiting_approval','completed']) ? 'text-blue-700' : 'text-gray-400' }}">جاري التنفيذ</p>
                                @if(in_array($status, ['in_progress', 'awaiting_approval']))
                                    <div class="mt-2 flex items-center gap-2">
                                        <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                                        <p class="text-xs text-blue-600 font-medium">
                                            {{ $status === 'awaiting_approval' ? 'في انتظار موافقة العميل على التقرير الأولي' : 'المهمة قيد التنفيذ' }}
                                        </p>
                                    </div>
                                @elseif($status === 'completed')
                                    <p class="text-xs text-gray-500 mt-0.5">تم إنجاز المهمة</p>
                                @else
                                    <p class="text-xs text-gray-400 mt-0.5">سيُفعَّل بعد إرسال التقرير الأولي</p>
                                @endif
                            </div>
                        </div>

                        {{-- STEP 6: التقرير النهائي --}}
                        <div class="relative flex gap-5">
                            <div class="relative z-10 w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0
                                        {{ $status === 'completed' ? 'bg-green-500' : 'bg-gray-200' }} shadow">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1 pt-1.5">
                                <p class="font-bold text-sm {{ $status === 'completed' ? 'text-green-700' : 'text-gray-400' }}">التقرير النهائي</p>

                                @if($status === 'in_progress' && !$finalReport)
                                    <p class="text-xs text-gray-500 mt-1 mb-4">أنهيت العمل؟ أرسل التقرير النهائي لإغلاق المهمة</p>
                                    <form action="{{ route('tech.tasks.final-report', $req) }}" method="POST" class="space-y-4 bg-green-50/40 rounded-xl p-4 border border-green-200">
                                        @csrf
                                        <div>
                                            <label class="block text-xs font-bold text-[#2C2C2A] mb-1.5">ما تم إنجازه <span class="text-red-500">*</span></label>
                                            <textarea name="work_done" rows="3" required
                                                      placeholder="صف ما تم إنجازه بالتفصيل..."
                                                      class="w-full text-sm border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 resize-none bg-white">{{ old('work_done') }}</textarea>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-[#2C2C2A] mb-1.5">توصيات <span class="text-gray-400 font-normal">(اختياري)</span></label>
                                            <textarea name="recommendations" rows="2"
                                                      placeholder="أي توصيات أو ملاحظات للعميل..."
                                                      class="w-full text-sm border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 resize-none bg-white">{{ old('recommendations') }}</textarea>
                                        </div>
                                        <button type="submit"
                                                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-xl transition-colors text-sm shadow-sm">
                                            ✅ إغلاق المهمة وإرسال التقرير
                                        </button>
                                    </form>

                                @elseif($finalReport)
                                    <div class="mt-2 bg-white rounded-xl border border-gray-200 p-4 space-y-2">
                                        <div>
                                            <p class="text-xs text-gray-500">ما تم إنجازه</p>
                                            <p class="text-sm text-[#2C2C2A] font-medium mt-0.5">{{ $finalReport->work_done }}</p>
                                        </div>
                                        @if($finalReport->recommendations)
                                        <div>
                                            <p class="text-xs text-gray-500">التوصيات</p>
                                            <p class="text-sm text-[#2C2C2A] mt-0.5">{{ $finalReport->recommendations }}</p>
                                        </div>
                                        @endif
                                        <span class="inline-flex items-center gap-1.5 text-xs text-green-700 bg-green-50 px-2.5 py-1 rounded-full font-semibold">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            المهمة مكتملة
                                        </span>
                                    </div>
                                @else
                                    <p class="text-xs text-gray-400 mt-0.5">سيُفعَّل بعد بدء التنفيذ</p>
                                @endif
                            </div>
                        </div>

                    </div>{{-- end steps --}}
                </div>
            </div>
        </div>

    </div>

    {{-- ===== RIGHT: CLIENT SIDEBAR ===== --}}
    <div class="lg:col-span-1 space-y-4">

        {{-- Client Card --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <h3 class="font-bold text-[#2C2C2A] text-sm mb-4">معلومات العميل</h3>

            <div class="flex items-center gap-3 mb-5">
                <div class="w-12 h-12 bg-[#FFF3DC] rounded-full flex items-center justify-center font-bold text-[#F5A623] text-lg flex-shrink-0">
                    {{ mb_substr($client->name ?? 'ع', 0, 1) }}
                </div>
                <div>
                    <p class="font-bold text-[#2C2C2A] text-sm">{{ $client->name ?? 'العميل' }}</p>
                    <p class="text-xs text-gray-500">{{ $client->email ?? '' }}</p>
                </div>
            </div>

            {{-- Call button --}}
            @if($client?->phone)
            <a href="tel:{{ $client->phone }}"
               class="flex items-center justify-center gap-2 w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3.5 rounded-xl transition-colors shadow-sm text-sm mb-3">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                {{ $client->phone }}
            </a>
            @endif

            {{-- Address --}}
            @if($req->description)
            <div class="bg-gray-50 rounded-xl p-3 mb-3">
                <p class="text-xs text-gray-500 mb-1 font-medium">العنوان / الوصف</p>
                <p class="text-sm text-[#2C2C2A]">{{ $req->description }}</p>
            </div>
            @endif

            {{-- Maps button --}}
            <a href="https://maps.google.com/?q={{ urlencode($req->description ?? $client->name ?? '') }}"
               target="_blank" rel="noopener noreferrer"
               class="flex items-center justify-center gap-2 w-full border-2 border-gray-200 hover:border-[#F5A623] hover:bg-[#FFF3DC] text-[#2C2C2A] hover:text-[#D4881A] font-semibold py-2.5 rounded-xl transition-colors text-xs">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
                فتح في خرائط جوجل
            </a>
        </div>

        {{-- Media / Photos --}}
        @if($media && $media->count() > 0)
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <h3 class="font-bold text-[#2C2C2A] text-sm mb-3">صور الطلب</h3>
            <div class="grid grid-cols-3 gap-2">
                @foreach($media->take(6) as $item)
                <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank"
                   class="aspect-square rounded-xl overflow-hidden bg-gray-100 block hover:opacity-80 transition-opacity">
                    <img src="{{ asset('storage/' . $item->file_path) }}"
                         alt="صورة الطلب"
                         class="w-full h-full object-cover">
                </a>
                @endforeach
            </div>
            @if($media->count() > 6)
                <p class="text-xs text-gray-400 mt-2 text-center">+{{ $media->count() - 6 }} صور أخرى</p>
            @endif
        </div>
        @endif

        {{-- Request Notes --}}
        @if($req->client_notes)
        <div class="bg-amber-50 rounded-2xl p-5 border border-amber-200">
            <h3 class="font-bold text-amber-800 text-sm mb-2">ملاحظات العميل</h3>
            <p class="text-sm text-amber-700">{{ $req->client_notes }}</p>
        </div>
        @endif

    </div>

</div>
@endsection
