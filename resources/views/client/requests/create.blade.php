@extends('layouts.dashboard')

@section('title', 'طلب صيانة جديد')

@push('styles')
<style>
    .service-card { transition: all 0.2s ease; cursor: pointer; }
    .service-card:hover { transform: translateY(-3px); }
    .service-card.selected {
        border-color: #F5A623 !important;
        background-color: #FFF3DC !important;
    }
    .step-dot {
        width: 2rem; height: 2rem;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.8rem; font-weight: 700;
        transition: all 0.3s;
    }
    .step-dot.done { background-color: #F5A623; color: white; }
    .step-dot.active { background-color: #F5A623; color: white; box-shadow: 0 0 0 4px #FFF3DC; }
    .step-dot.pending { background-color: #E5E7EB; color: #6B7280; }
    .step-connector {
        flex: 1; height: 2px; background: #E5E7EB;
        transition: background 0.3s;
    }
    .step-connector.done { background: #F5A623; }
    .chip {
        display: inline-block;
        padding: 0.375rem 0.875rem;
        border-radius: 9999px;
        font-size: 0.8rem; font-weight: 600;
        border: 1.5px solid #E5E7EB;
        background: white; color: #374151;
        cursor: pointer; transition: all 0.2s;
    }
    .chip:hover { border-color: #F5A623; color: #D4881A; background: #FFF3DC; }
    .dropzone {
        border: 2.5px dashed #D1D5DB;
        border-radius: 1.25rem;
        transition: all 0.2s;
        cursor: pointer;
    }
    .dropzone:hover, .dropzone.drag-over {
        border-color: #F5A623;
        background: #FFF3DC;
    }
</style>
@endpush

@section('content')

<div
    x-data="{
        step: 1,
        totalSteps: 5,
        serviceType: '',
        serviceLabel: '',
        description: '',
        files: [],
        previews: [],
        street: '{{ $user->address ?? '' }}',
        district: '{{ $user->district ?? '' }}',
        city: '{{ $user->city ?? 'الرياض' }}',
        notes: '',
        isDragging: false,

        selectService(type, label) {
            this.serviceType = type;
            this.serviceLabel = label;
        },

        appendChip(text) {
            if (this.description.length > 0 && !this.description.endsWith(' ')) {
                this.description += ' ';
            }
            this.description += text;
            this.$nextTick(() => {
                const ta = this.$el.querySelector('#descriptionTextarea');
                if (ta) { ta.focus(); ta.setSelectionRange(ta.value.length, ta.value.length); }
            });
        },

        handleFileSelect(event) {
            const selectedFiles = Array.from(event.target.files);
            selectedFiles.forEach(file => {
                if (this.files.length >= 8) return;
                this.files.push(file);
                const reader = new FileReader();
                reader.onload = (e) => this.previews.push({ url: e.target.result, name: file.name, type: file.type });
                reader.readAsDataURL(file);
            });
        },

        handleDrop(event) {
            this.isDragging = false;
            const droppedFiles = Array.from(event.dataTransfer.files);
            droppedFiles.forEach(file => {
                if (!file.type.startsWith('image/') && !file.type.startsWith('video/')) return;
                if (this.files.length >= 8) return;
                this.files.push(file);
                const reader = new FileReader();
                reader.onload = (e) => this.previews.push({ url: e.target.result, name: file.name, type: file.type });
                reader.readAsDataURL(file);
            });
        },

        removeFile(index) {
            this.files.splice(index, 1);
            this.previews.splice(index, 1);
        },

        nextStep() { if (this.canProceed()) this.step++; },
        prevStep() { if (this.step > 1) this.step--; },

        canProceed() {
            if (this.step === 1) return this.serviceType !== '';
            if (this.step === 2) return this.description.trim().length >= 5;
            return true;
        },

        get progressPercent() {
            return ((this.step - 1) / (this.totalSteps - 1)) * 100;
        },

        get serviceBadgeClass() {
            const map = {
                plumbing: 'bg-blue-100 text-blue-700',
                electrical: 'bg-yellow-100 text-yellow-700',
                hvac: 'bg-cyan-100 text-cyan-700',
                general: 'bg-orange-100 text-orange-700',
            };
            return map[this.serviceType] || 'bg-gray-100 text-gray-700';
        }
    }"
    class="max-w-3xl mx-auto"
>

    {{-- ===== PROGRESS HEADER ===== --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-black text-accent">طلب صيانة جديد</h2>
            <span class="text-sm font-semibold text-gray-500">
                الخطوة <span class="text-brand font-black" x-text="step"></span> من {{ 5 }}
            </span>
        </div>

        {{-- Step Dots --}}
        <div class="flex items-center gap-1 mb-4">
            @foreach([
                ['num' => 1, 'label' => 'نوع الخدمة'],
                ['num' => 2, 'label' => 'الوصف'],
                ['num' => 3, 'label' => 'الصور'],
                ['num' => 4, 'label' => 'العنوان'],
                ['num' => 5, 'label' => 'المراجعة'],
            ] as $i => $s)
            <div class="flex flex-col items-center flex-shrink-0">
                <div class="step-dot"
                     :class="{
                         'done': step > {{ $s['num'] }},
                         'active': step === {{ $s['num'] }},
                         'pending': step < {{ $s['num'] }}
                     }">
                    <template x-if="step > {{ $s['num'] }}">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </template>
                    <template x-if="step <= {{ $s['num'] }}">
                        <span>{{ $s['num'] }}</span>
                    </template>
                </div>
                <span class="text-xs text-gray-500 mt-1 whitespace-nowrap hidden sm:block"
                      :class="step === {{ $s['num'] }} ? 'text-brand font-bold' : ''">
                    {{ $s['label'] }}
                </span>
            </div>
            @if($i < 4)
            <div class="step-connector flex-1"
                 :class="step > {{ $s['num'] }} ? 'done' : ''"></div>
            @endif
            @endforeach
        </div>

        {{-- Progress bar --}}
        <div class="w-full bg-gray-100 rounded-full h-1.5">
            <div class="h-1.5 rounded-full bg-gradient-to-l from-brand to-brand-dark transition-all duration-500"
                 :style="'width: ' + progressPercent + '%'"></div>
        </div>
    </div>

    {{-- ===== FORM ===== --}}
    <form
        action="{{ route('client.requests.store') }}"
        method="POST"
        enctype="multipart/form-data"
        id="requestForm"
    >
        @csrf

        {{-- Hidden inputs for Alpine state --}}
        <input type="hidden" name="type" :value="serviceType" />
        <input type="hidden" name="description" :value="description" />
        <input type="hidden" name="street" :value="street" />
        <input type="hidden" name="district" :value="district" />
        <input type="hidden" name="city" :value="city" />
        <input type="hidden" name="notes" :value="notes" />

        {{-- ============================
             STEP 1: نوع الخدمة
        ============================= --}}
        <div x-show="step === 1" x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
                <h3 class="text-xl font-black text-accent mb-2">نوع الخدمة</h3>
                <p class="text-sm text-gray-500 mb-6">اختر نوع الصيانة التي تحتاجها</p>

                <div class="grid grid-cols-2 gap-4">

                    {{-- سباكة --}}
                    <div class="service-card border-2 border-gray-200 rounded-2xl p-5 text-center relative"
                         :class="serviceType === 'plumbing' ? 'selected' : ''"
                         @click="selectService('plumbing', 'سباكة')">
                        <div x-show="serviceType === 'plumbing'"
                             class="absolute top-3 left-3 w-6 h-6 bg-brand rounded-full flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="text-5xl mb-3">🔧</div>
                        <h4 class="font-black text-accent text-base mb-1">سباكة</h4>
                        <p class="text-xs text-gray-500 leading-relaxed">تسريبات • أعطال المياه • صرف صحي</p>
                    </div>

                    {{-- كهرباء --}}
                    <div class="service-card border-2 border-gray-200 rounded-2xl p-5 text-center relative"
                         :class="serviceType === 'electrical' ? 'selected' : ''"
                         @click="selectService('electrical', 'كهرباء')">
                        <div x-show="serviceType === 'electrical'"
                             class="absolute top-3 left-3 w-6 h-6 bg-brand rounded-full flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="text-5xl mb-3">⚡</div>
                        <h4 class="font-black text-accent text-base mb-1">كهرباء</h4>
                        <p class="text-xs text-gray-500 leading-relaxed">أسلاك • لوحات كهربائية • إضاءة</p>
                    </div>

                    {{-- تكييف --}}
                    <div class="service-card border-2 border-gray-200 rounded-2xl p-5 text-center relative"
                         :class="serviceType === 'hvac' ? 'selected' : ''"
                         @click="selectService('hvac', 'تكييف')">
                        <div x-show="serviceType === 'hvac'"
                             class="absolute top-3 left-3 w-6 h-6 bg-brand rounded-full flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="text-5xl mb-3">❄️</div>
                        <h4 class="font-black text-accent text-base mb-1">تكييف</h4>
                        <p class="text-xs text-gray-500 leading-relaxed">صيانة • تعبئة فريون • تنظيف</p>
                    </div>

                    {{-- عام --}}
                    <div class="service-card border-2 border-gray-200 rounded-2xl p-5 text-center relative"
                         :class="serviceType === 'general' ? 'selected' : ''"
                         @click="selectService('general', 'عام')">
                        <div x-show="serviceType === 'general'"
                             class="absolute top-3 left-3 w-6 h-6 bg-brand rounded-full flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="text-5xl mb-3">🏠</div>
                        <h4 class="font-black text-accent text-base mb-1">عام</h4>
                        <p class="text-xs text-gray-500 leading-relaxed">نجارة • دهانات • تنظيف عام</p>
                    </div>
                </div>

                <p x-show="!canProceed() && step === 1"
                   x-cloak
                   class="text-red-500 text-sm mt-4 text-center font-medium">
                   يرجى اختيار نوع الخدمة للمتابعة
                </p>
            </div>
        </div>

        {{-- ============================
             STEP 2: وصف المشكلة
        ============================= --}}
        <div x-show="step === 2" x-cloak x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
                <h3 class="text-xl font-black text-accent mb-2">وصف المشكلة</h3>
                <p class="text-sm text-gray-500 mb-5">كلما كان الوصف أدق كلما تمكن الفني من المساعدة أفضل</p>

                <textarea
                    id="descriptionTextarea"
                    x-model="description"
                    rows="6"
                    placeholder="اكتب وصف المشكلة بالتفصيل... مثلاً: يوجد تسريب مياه تحت حوض المطبخ منذ يومين، الماء يتجمع ببطء..."
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand resize-none leading-relaxed"
                ></textarea>

                <div class="flex justify-between items-center mt-2 mb-5">
                    <span class="text-xs text-gray-400" :class="description.length < 5 ? 'text-red-400' : 'text-gray-400'">
                        <span x-text="description.length"></span> حرف (الحد الأدنى 5)
                    </span>
                </div>

                {{-- Quick Chips --}}
                <div>
                    <p class="text-sm font-semibold text-gray-700 mb-3">اقتراحات سريعة:</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="chip" @click="appendChip('تسريب مياه')">💧 تسريب مياه</span>
                        <span class="chip" @click="appendChip('انقطاع تيار كهربائي')">⚡ انقطاع تيار</span>
                        <span class="chip" @click="appendChip('عطل في المكيف')">❄️ عطل مكيف</span>
                        <span class="chip" @click="appendChip('مشكلة في الإضاءة')">💡 مشكلة إضاءة</span>
                        <span class="chip" @click="appendChip('انسداد في الصرف الصحي')">🚿 انسداد صرف</span>
                        <span class="chip" @click="appendChip('عطل في الدش أو الحمام')">🛁 عطل حمام</span>
                        <span class="chip" @click="appendChip('حنفية تقطر')">🔧 حنفية تقطر</span>
                        <span class="chip" @click="appendChip('رائحة غاز أو دخان')">⚠️ رائحة غاز</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================
             STEP 3: الصور والوسائط
        ============================= --}}
        <div x-show="step === 3" x-cloak x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xl font-black text-accent">الصور والوسائط</h3>
                    <span class="text-xs text-gray-400 bg-gray-100 px-2.5 py-1 rounded-full font-medium">اختياري</span>
                </div>
                <p class="text-sm text-gray-500 mb-6">أرفق صوراً أو فيديو للمشكلة لمساعدة الفني على الاستعداد (حتى 8 ملفات)</p>

                {{-- Dropzone --}}
                <div
                    class="dropzone"
                    :class="isDragging ? 'drag-over' : ''"
                    @dragover.prevent="isDragging = true"
                    @dragleave.prevent="isDragging = false"
                    @drop.prevent="handleDrop($event)"
                    @click="$refs.fileInput.click()"
                >
                    <input
                        type="file"
                        name="media[]"
                        multiple
                        accept="image/*,video/*"
                        x-ref="fileInput"
                        class="hidden"
                        @change="handleFileSelect($event)"
                    />

                    <div class="py-12 flex flex-col items-center" x-show="previews.length === 0">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="font-bold text-gray-700 text-base mb-1">اسحب الصور هنا أو اضغط للرفع</p>
                        <p class="text-xs text-gray-400">JPG, PNG, GIF, MP4 — حتى 10 ميجابايت لكل ملف</p>
                    </div>

                    {{-- Previews --}}
                    <div x-show="previews.length > 0" class="p-4">
                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                            <template x-for="(preview, index) in previews" :key="index">
                                <div class="relative group rounded-xl overflow-hidden bg-gray-100 aspect-square">
                                    <template x-if="!preview.type.startsWith('video/')">
                                        <img :src="preview.url" :alt="preview.name" class="w-full h-full object-cover" />
                                    </template>
                                    <template x-if="preview.type.startsWith('video/')">
                                        <div class="w-full h-full flex items-center justify-center bg-gray-800">
                                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                        </div>
                                    </template>
                                    <button
                                        type="button"
                                        @click.stop="removeFile(index)"
                                        class="absolute top-1 left-1 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-sm"
                                    >
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </template>
                            {{-- Add more --}}
                            <div
                                x-show="previews.length < 8"
                                @click.stop="$refs.fileInput.click()"
                                class="border-2 border-dashed border-gray-200 rounded-xl aspect-square flex items-center justify-center cursor-pointer hover:border-brand hover:bg-brand-light transition-colors"
                            >
                                <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-3 text-center" x-text="previews.length + ' ملف/ملفات محددة'"></p>
                    </div>
                </div>

                <button
                    type="button"
                    @click="step = 4"
                    class="mt-4 w-full text-center text-sm text-gray-500 hover:text-gray-700 font-medium py-2 underline underline-offset-2"
                >
                    تخطي هذه الخطوة
                </button>
            </div>
        </div>

        {{-- ============================
             STEP 4: تأكيد العنوان
        ============================= --}}
        <div x-show="step === 4" x-cloak x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
                <h3 class="text-xl font-black text-accent mb-2">تأكيد العنوان</h3>
                <p class="text-sm text-gray-500 mb-6">تأكد من صحة عنوانك حتى يصل الفني إليك بسرعة</p>

                <div class="space-y-4 mb-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">الشارع</label>
                        <input
                            type="text"
                            x-model="street"
                            placeholder="اسم الشارع ورقم المبنى"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand"
                        />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">الحي</label>
                            <input
                                type="text"
                                x-model="district"
                                placeholder="اسم الحي"
                                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">المدينة</label>
                            <input
                                type="text"
                                x-model="city"
                                placeholder="المدينة"
                                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand"
                            />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">ملاحظات للفني <span class="text-gray-400 font-normal">(اختياري)</span></label>
                        <textarea
                            x-model="notes"
                            rows="3"
                            placeholder="مثلاً: الشقة في الدور الثالث، اضغط الجرس مرتين..."
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand resize-none"
                        ></textarea>
                    </div>
                </div>

                {{-- Map Placeholder --}}
                <div class="w-full h-48 bg-gray-100 rounded-2xl flex flex-col items-center justify-center text-gray-400 border border-gray-200 overflow-hidden relative">
                    {{-- Grid lines to simulate map --}}
                    <div class="absolute inset-0 opacity-20">
                        <div class="grid grid-cols-8 h-full">
                            @for($i = 0; $i < 8; $i++)
                            <div class="border-r border-gray-400 h-full"></div>
                            @endfor
                        </div>
                        <div class="absolute inset-0 grid grid-rows-6">
                            @for($i = 0; $i < 6; $i++)
                            <div class="border-b border-gray-400 w-full"></div>
                            @endfor
                        </div>
                    </div>
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="text-4xl mb-2">📍</div>
                        <p class="font-bold text-gray-600 text-sm">موقعك على الخريطة</p>
                        <p class="text-xs text-gray-400 mt-1" x-text="city || 'الرياض'"></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================
             STEP 5: المراجعة والإرسال
        ============================= --}}
        <div x-show="step === 5" x-cloak x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
                <h3 class="text-xl font-black text-accent mb-2">مراجعة الطلب</h3>
                <p class="text-sm text-gray-500 mb-6">راجع تفاصيل طلبك قبل الإرسال</p>

                <div class="space-y-4">

                    {{-- Service type --}}
                    <div class="flex items-center justify-between p-4 bg-brand-light rounded-xl">
                        <span class="text-sm font-semibold text-gray-700">نوع الخدمة</span>
                        <span class="font-black text-brand text-sm" x-text="serviceLabel || '—'"></span>
                    </div>

                    {{-- Description --}}
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <p class="text-sm font-semibold text-gray-700 mb-2">وصف المشكلة</p>
                        <p class="text-sm text-gray-600 leading-relaxed" x-text="description || 'لم يتم إدخال وصف'"></p>
                    </div>

                    {{-- Media --}}
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                        <span class="text-sm font-semibold text-gray-700">المرفقات</span>
                        <span class="text-sm text-gray-600 font-medium" x-text="files.length > 0 ? files.length + ' ملف/ملفات' : 'لا توجد مرفقات'"></span>
                    </div>

                    {{-- Address --}}
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <p class="text-sm font-semibold text-gray-700 mb-2">العنوان</p>
                        <p class="text-sm text-gray-600">
                            <span x-text="street || '—'"></span>,
                            حي <span x-text="district || '—'"></span>,
                            <span x-text="city || '—'"></span>
                        </p>
                        <template x-if="notes">
                            <p class="text-xs text-gray-400 mt-1">ملاحظة: <span x-text="notes"></span></p>
                        </template>
                    </div>

                    {{-- Preview thumbnails in summary --}}
                    <template x-if="previews.length > 0">
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-sm font-semibold text-gray-700 mb-3">معاينة الصور</p>
                            <div class="flex gap-2 flex-wrap">
                                <template x-for="(p, i) in previews.slice(0, 5)" :key="i">
                                    <div class="w-14 h-14 rounded-lg overflow-hidden bg-gray-200 flex-shrink-0">
                                        <img :src="p.url" class="w-full h-full object-cover" />
                                    </div>
                                </template>
                                <template x-if="previews.length > 5">
                                    <div class="w-14 h-14 rounded-lg bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600">
                                        +<span x-text="previews.length - 5"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Terms note --}}
                <div class="mt-5 p-3 bg-blue-50 rounded-xl flex items-start gap-2.5 border border-blue-100">
                    <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-xs text-blue-700 leading-relaxed">
                        بإرسال الطلب، أنت توافق على <a href="#" class="font-bold underline">شروط الخدمة</a> وتؤكد أن المعلومات المدخلة صحيحة.
                    </p>
                </div>
            </div>
        </div>

        {{-- ===== NAVIGATION BUTTONS ===== --}}
        <div class="flex items-center justify-between gap-4">

            {{-- Back --}}
            <button
                type="button"
                @click="prevStep()"
                x-show="step > 1"
                class="flex items-center gap-2 px-6 py-3 border-2 border-gray-200 text-gray-600 hover:border-gray-300 font-semibold rounded-xl transition-all text-sm"
            >
                <svg class="w-4 h-4 rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                رجوع
            </button>
            <div x-show="step === 1"></div>

            {{-- Next / Submit --}}
            <div>
                {{-- Next Step --}}
                <button
                    type="button"
                    @click="nextStep()"
                    x-show="step < 5"
                    :disabled="!canProceed()"
                    :class="canProceed()
                        ? 'bg-brand hover:bg-brand-dark text-white shadow-sm cursor-pointer'
                        : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                    class="flex items-center gap-2 px-8 py-3 font-bold rounded-xl transition-all text-sm"
                >
                    التالي
                    <svg class="w-4 h-4 rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                {{-- Submit --}}
                <button
                    type="submit"
                    x-show="step === 5"
                    class="flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white font-black px-8 py-3 rounded-xl transition-all text-sm shadow-sm"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    إرسال الطلب
                </button>
            </div>
        </div>

    </form>

</div>

@endsection
