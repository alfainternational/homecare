@extends('layouts.admin')

@section('title', 'إدارة الطلبات')
@section('page-title', 'إدارة الطلبات')

@section('content')

<div class="space-y-5" x-data="{
    selectedRows: [],
    bulkTech: '',
    toggleRow(id) {
        const idx = this.selectedRows.indexOf(id);
        idx === -1 ? this.selectedRows.push(id) : this.selectedRows.splice(idx, 1);
    },
    toggleAll(ids) {
        this.selectedRows = this.selectedRows.length === ids.length ? [] : [...ids];
    },
    expandedRow: null,
    toggleExpand(id) {
        this.expandedRow = this.expandedRow === id ? null : id;
    }
}">

    {{-- ===== FILTER BAR ===== --}}
    <form method="GET" action="{{ route('admin.requests.index') }}"
          class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3 items-end">

            {{-- Status filter --}}
            <div>
                <label class="block text-xs font-bold text-accent mb-1.5">الحالة</label>
                <select name="status"
                        class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-brand bg-white">
                    <option value="">جميع الحالات</option>
                    <option value="pending"          {{ request('status') === 'pending'          ? 'selected' : '' }}>قيد الانتظار</option>
                    <option value="assigned"         {{ request('status') === 'assigned'         ? 'selected' : '' }}>تم التعيين</option>
                    <option value="on_way"           {{ request('status') === 'on_way'           ? 'selected' : '' }}>في الطريق</option>
                    <option value="arrived"          {{ request('status') === 'arrived'          ? 'selected' : '' }}>وصل الفني</option>
                    <option value="in_progress"      {{ request('status') === 'in_progress'      ? 'selected' : '' }}>جاري التنفيذ</option>
                    <option value="awaiting_approval"{{ request('status') === 'awaiting_approval'? 'selected' : '' }}>ينتظر الموافقة</option>
                    <option value="completed"        {{ request('status') === 'completed'        ? 'selected' : '' }}>مكتمل</option>
                    <option value="cancelled"        {{ request('status') === 'cancelled'        ? 'selected' : '' }}>ملغي</option>
                </select>
            </div>

            {{-- Service type filter --}}
            <div>
                <label class="block text-xs font-bold text-accent mb-1.5">نوع الخدمة</label>
                <select name="service_type"
                        class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-brand bg-white">
                    <option value="">جميع الخدمات</option>
                    <option value="plumbing"   {{ request('service_type') === 'plumbing'   ? 'selected' : '' }}>سباكة</option>
                    <option value="electrical" {{ request('service_type') === 'electrical' ? 'selected' : '' }}>كهرباء</option>
                    <option value="hvac"       {{ request('service_type') === 'hvac'       ? 'selected' : '' }}>تكييف</option>
                    <option value="general"    {{ request('service_type') === 'general'    ? 'selected' : '' }}>صيانة عامة</option>
                </select>
            </div>

            {{-- Date from --}}
            <div>
                <label class="block text-xs font-bold text-accent mb-1.5">من تاريخ</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-brand bg-white">
            </div>

            {{-- Date to --}}
            <div>
                <label class="block text-xs font-bold text-accent mb-1.5">إلى تاريخ</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-brand bg-white">
            </div>

            {{-- Search + buttons --}}
            <div class="col-span-2 md:col-span-4 lg:col-span-1 flex gap-2">
                <div class="flex-1 relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="بحث باسم العميل..."
                           class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 pr-9 focus:outline-none focus:border-brand bg-white">
                    <svg class="w-4 h-4 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <button type="submit"
                        class="bg-brand hover:bg-brand-dark text-white font-bold px-5 py-2.5 rounded-xl transition-colors text-sm whitespace-nowrap">
                    بحث
                </button>
                @if(request()->hasAny(['status','service_type','date_from','date_to','search']))
                <a href="{{ route('admin.requests.index') }}"
                   class="border border-gray-200 text-gray-500 hover:bg-gray-50 font-semibold px-4 py-2.5 rounded-xl transition-colors text-sm whitespace-nowrap">
                    مسح
                </a>
                @endif
            </div>
        </div>
    </form>

    {{-- ===== BULK ACTIONS BAR ===== --}}
    <div x-show="selectedRows.length > 0" x-cloak x-transition
         class="bg-brand-light border border-brand/30 rounded-2xl p-4 flex flex-wrap items-center gap-3">
        <p class="text-sm font-bold text-brand-dark">
            تم تحديد <span x-text="selectedRows.length"></span> طلب
        </p>
        <div class="flex items-center gap-2 flex-1">
            <select x-model="bulkTech"
                    class="text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:border-brand bg-white">
                <option value="">اختر فنياً للتعيين...</option>
                @foreach($technicians as $tech)
                    @if($tech->technicianProfile?->status === 'available')
                    <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                    @endif
                @endforeach
            </select>
            <button class="bg-brand hover:bg-brand-dark text-white font-bold px-4 py-2 rounded-xl transition-colors text-sm">
                عيّن فنياً
            </button>
        </div>
        <button class="flex items-center gap-2 border border-gray-300 hover:bg-white text-gray-600 font-semibold px-4 py-2 rounded-xl transition-colors text-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            تصدير
        </button>
    </div>

    {{-- ===== TABLE ===== --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        @php
            $allIds = $requests->pluck('id')->toArray();
        @endphp

        @if($requests->isEmpty())
            <div class="py-16 text-center px-6">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">📋</div>
                <h3 class="font-bold text-accent mb-1">لا توجد طلبات</h3>
                <p class="text-sm text-gray-500">لم يتم العثور على طلبات تطابق معايير البحث</p>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-right">
                        <th class="px-4 py-3 w-8">
                            <input type="checkbox"
                                   @change="toggleAll({{ json_encode($allIds) }})"
                                   :checked="selectedRows.length === {{ count($allIds) }} && {{ count($allIds) }} > 0"
                                   class="rounded border-gray-300 text-brand focus:ring-brand w-4 h-4">
                        </th>
                        <th class="px-4 py-3 text-xs font-bold text-gray-500 whitespace-nowrap">رقم الطلب</th>
                        <th class="px-4 py-3 text-xs font-bold text-gray-500 whitespace-nowrap">العميل</th>
                        <th class="px-4 py-3 text-xs font-bold text-gray-500 whitespace-nowrap">الخدمة</th>
                        <th class="px-4 py-3 text-xs font-bold text-gray-500 whitespace-nowrap">الحالة</th>
                        <th class="px-4 py-3 text-xs font-bold text-gray-500 whitespace-nowrap">الفني</th>
                        <th class="px-4 py-3 text-xs font-bold text-gray-500 whitespace-nowrap">التاريخ</th>
                        <th class="px-4 py-3 text-xs font-bold text-gray-500 whitespace-nowrap">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($requests as $request)
                    @php
                        $statusColors = [
                            'pending'           => 'bg-yellow-100 text-yellow-700',
                            'assigned'          => 'bg-blue-100 text-blue-700',
                            'on_way'            => 'bg-purple-100 text-purple-700',
                            'arrived'           => 'bg-orange-100 text-orange-700',
                            'in_progress'       => 'bg-brand-light text-brand-dark',
                            'awaiting_approval' => 'bg-amber-100 text-amber-700',
                            'completed'         => 'bg-green-100 text-green-700',
                            'cancelled'         => 'bg-red-100 text-red-600',
                        ];
                        $badgeClass = $statusColors[$request->status] ?? 'bg-gray-100 text-gray-600';
                    @endphp

                    {{-- Main row --}}
                    <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer"
                        :class="expandedRow === {{ $request->id }} ? 'bg-brand-light/30' : ''">
                        <td class="px-4 py-3" @click.stop>
                            <input type="checkbox"
                                   @change="toggleRow({{ $request->id }})"
                                   :checked="selectedRows.includes({{ $request->id }})"
                                   class="rounded border-gray-300 text-brand focus:ring-brand w-4 h-4">
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-500 whitespace-nowrap"
                            @click="toggleExpand({{ $request->id }})">
                            {{ $request->request_number }}
                        </td>
                        <td class="px-4 py-3" @click="toggleExpand({{ $request->id }})">
                            <p class="font-semibold text-accent">{{ $request->client->name ?? '—' }}</p>
                            <p class="text-xs text-gray-400">{{ $request->client->phone ?? '' }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap"
                            @click="toggleExpand({{ $request->id }})">
                            {{ $request->service_type_label }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap" @click="toggleExpand({{ $request->id }})">
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $badgeClass }}">
                                {{ $request->status_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3" @click="toggleExpand({{ $request->id }})">
                            @if($request->technician)
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 bg-brand-light rounded-full flex items-center justify-center text-xs font-bold text-brand flex-shrink-0">
                                        {{ mb_substr($request->technician->name, 0, 1) }}
                                    </div>
                                    <span class="text-sm text-accent">{{ Str::words($request->technician->name, 2, '') }}</span>
                                </div>
                            @elseif($request->status === 'pending')
                                <form action="{{ route('admin.requests.assign', $request) }}" method="POST"
                                      class="flex items-center gap-1.5" @click.stop>
                                    @csrf
                                    <select name="technician_id"
                                            class="text-xs border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:border-brand bg-white max-w-[120px]">
                                        <option value="">اختر...</option>
                                        @foreach($technicians as $tech)
                                            @if($tech->technicianProfile?->status === 'available')
                                            <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <button type="submit"
                                            class="bg-brand hover:bg-brand-dark text-white text-xs font-bold px-2.5 py-1.5 rounded-lg transition-colors whitespace-nowrap">
                                        عيّن
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap"
                            @click="toggleExpand({{ $request->id }})">
                            {{ $request->created_at->format('d/m/Y') }}<br>
                            <span class="text-gray-400">{{ $request->created_at->format('h:i A') }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap" @click.stop>
                            <div class="flex items-center gap-1.5">
                                <button @click="toggleExpand({{ $request->id }})"
                                        class="text-xs font-semibold text-brand hover:text-brand-dark transition-colors px-2.5 py-1.5 rounded-lg hover:bg-brand-light">
                                    سريع
                                </button>
                                <a href="{{ route('admin.requests.show', $request) }}"
                                   class="text-xs font-semibold text-white bg-brand hover:bg-brand-dark transition-colors px-2.5 py-1.5 rounded-lg">
                                    عرض
                                </a>
                            </div>
                        </td>
                    </tr>

                    {{-- Expandable details row --}}
                    <tr x-show="expandedRow === {{ $request->id }}" x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0">
                        <td colspan="8" class="px-6 pb-5 pt-2 bg-brand-light/20">
                            <div class="bg-white rounded-xl border border-gray-200 p-5 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                                <div>
                                    <p class="text-xs text-gray-500 font-medium mb-1">رقم الطلب</p>
                                    <p class="font-bold text-accent font-mono">{{ $request->request_number }}</p>
                                </div>
                                @if($request->description)
                                <div class="sm:col-span-2">
                                    <p class="text-xs text-gray-500 font-medium mb-1">الوصف</p>
                                    <p class="text-accent">{{ $request->description }}</p>
                                </div>
                                @endif
                                @if($request->scheduled_at)
                                <div>
                                    <p class="text-xs text-gray-500 font-medium mb-1">الموعد المحدد</p>
                                    <p class="font-semibold text-accent">{{ $request->scheduled_at->format('d/m/Y h:i A') }}</p>
                                </div>
                                @endif
                                @if($request->client_notes)
                                <div class="sm:col-span-2">
                                    <p class="text-xs text-gray-500 font-medium mb-1">ملاحظات العميل</p>
                                    <p class="text-gray-700">{{ $request->client_notes }}</p>
                                </div>
                                @endif
                                @if($request->priority)
                                <div>
                                    <p class="text-xs text-gray-500 font-medium mb-1">الأولوية</p>
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                                        {{ $request->priority === 'urgent' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $request->priority === 'urgent' ? 'طارئ' : 'عادي' }}
                                    </span>
                                </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- ===== PAGINATION ===== --}}
    @if($requests->hasPages())
    <div class="flex justify-center">
        {{ $requests->withQueryString()->links() }}
    </div>
    @endif

</div>
@endsection
