@extends('layouts.admin')
@section('title', 'تفاصيل الطلب #' . $serviceRequest->request_number)

@section('content')
<div class="space-y-6">
  {{-- Header --}}
  <div class="flex items-center justify-between">
    <div>
      <a href="{{ route('admin.requests.index') }}" class="text-sm text-[#D4881A] hover:underline">← العودة للطلبات</a>
      <h1 class="text-2xl font-black text-gray-800 mt-2">طلب #{{ $serviceRequest->request_number }}</h1>
    </div>
    @php
      $statusMap = ['pending'=>['قيد الانتظار','bg-yellow-100 text-yellow-700'],'assigned'=>['تم تعيين فني','bg-blue-100 text-blue-700'],'on_way'=>['في الطريق','bg-indigo-100 text-indigo-700'],'arrived'=>['وصل الموقع','bg-teal-100 text-teal-700'],'in_progress'=>['جاري التنفيذ','bg-purple-100 text-purple-700'],'awaiting_approval'=>['بانتظار الموافقة','bg-orange-100 text-orange-700'],'completed'=>['مكتمل','bg-green-100 text-green-700'],'cancelled'=>['ملغي','bg-red-100 text-red-600']];
      [$sLabel,$sClass] = $statusMap[$serviceRequest->status] ?? ['غير معروف','bg-gray-100 text-gray-600'];
      $typeMap = ['plumbing'=>'سباكة','electrical'=>'كهرباء','hvac'=>'تكييف','general'=>'عام'];
    @endphp
    <span class="px-4 py-2 rounded-2xl text-sm font-bold {{ $sClass }}">{{ $sLabel }}</span>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Main Content --}}
    <div class="lg:col-span-2 space-y-6">
      {{-- Request Info --}}
      <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
        <h3 class="font-bold text-gray-800 mb-4">معلومات الطلب</h3>
        <div class="grid grid-cols-2 gap-4 text-sm">
          <div><span class="text-gray-400">نوع الخدمة:</span> <strong>{{ $typeMap[$serviceRequest->service_type] ?? $serviceRequest->service_type }}</strong></div>
          <div><span class="text-gray-400">الأولوية:</span> <strong>{{ ['low'=>'منخفضة','medium'=>'متوسطة','high'=>'عالية'][$serviceRequest->priority] ?? $serviceRequest->priority }}</strong></div>
          <div><span class="text-gray-400">تاريخ الطلب:</span> <strong>{{ $serviceRequest->created_at->format('d/m/Y H:i') }}</strong></div>
          <div><span class="text-gray-400">آخر تحديث:</span> <strong>{{ $serviceRequest->updated_at->diffForHumans() }}</strong></div>
        </div>
        @if($serviceRequest->description)
          <div class="mt-4 pt-4 border-t border-gray-100">
            <p class="text-gray-400 text-sm mb-1">وصف المشكلة:</p>
            <p class="text-gray-700">{{ $serviceRequest->description }}</p>
          </div>
        @endif
      </div>

      {{-- Reports --}}
      @foreach($serviceRequest->reports as $report)
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
          <h3 class="font-bold text-gray-800 mb-3">{{ $report->type === 'initial' ? '📋 التقرير الأولي' : '✅ التقرير النهائي' }}</h3>
          <div class="text-sm text-gray-600 space-y-2">
            <p><strong>الوصف:</strong> {{ $report->problem_description }}</p>
            @if($report->work_done)
              <p><strong>ما تم إنجازه:</strong> {{ $report->work_done }}</p>
            @endif
            @if($report->recommendations)
              <p><strong>التوصيات:</strong> {{ $report->recommendations }}</p>
            @endif
            <p><strong>بواسطة:</strong> {{ $report->reporter?->name }} — {{ $report->created_at->format('d/m/Y H:i') }}</p>
          </div>
        </div>
      @endforeach

      {{-- Admin Notes --}}
      <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
        <h3 class="font-bold text-gray-800 mb-3">📝 ملاحظات داخلية</h3>
        @if($serviceRequest->admin_notes)
          <pre class="text-sm text-gray-600 bg-gray-50 rounded-xl p-4 whitespace-pre-wrap font-sans mb-4">{{ $serviceRequest->admin_notes }}</pre>
        @endif
        <form method="POST" action="{{ route('admin.requests.note', $serviceRequest) }}" class="flex gap-2">
          @csrf
          <input type="text" name="note" placeholder="أضف ملاحظة داخلية..." required
            class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#F5A623] outline-none">
          <button type="submit" class="bg-[#F5A623] text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-[#D4881A] transition-all">إضافة</button>
        </form>
      </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">
      {{-- Client --}}
      <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
        <h3 class="font-bold text-gray-800 mb-3">👤 العميل</h3>
        <p class="font-bold text-gray-800">{{ $serviceRequest->client->name }}</p>
        <p class="text-sm text-gray-500">{{ $serviceRequest->client->email }}</p>
        <p class="text-sm text-gray-500" dir="ltr">{{ $serviceRequest->client->phone }}</p>
      </div>

      {{-- Assign Technician --}}
      <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
        <h3 class="font-bold text-gray-800 mb-3">👷 الفني</h3>
        @if($serviceRequest->technician)
          <p class="font-bold text-gray-800">{{ $serviceRequest->technician->name }}</p>
          <p class="text-sm text-gray-500">{{ $serviceRequest->technician->technicianProfile?->rating_average ?? '-' }} ⭐</p>
        @else
          <p class="text-gray-400 text-sm mb-3">لم يُعيَّن فني بعد</p>
        @endif
        @if($serviceRequest->status === 'pending')
          <form method="POST" action="{{ route('admin.requests.assign', $serviceRequest) }}" class="mt-3">
            @csrf
            <select name="technician_id" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm mb-2 focus:ring-2 focus:ring-[#F5A623] outline-none">
              <option value="">اختر فني...</option>
              @foreach($technicians->filter(fn($t)=>$t->technicianProfile?->status==='available') as $tech)
                <option value="{{ $tech->id }}">{{ $tech->name }} ({{ $tech->technicianProfile->rating_average ?? '-' }}⭐)</option>
              @endforeach
            </select>
            <button type="submit" class="w-full bg-[#F5A623] text-white py-2.5 rounded-xl text-sm font-bold hover:bg-[#D4881A] transition-all">عيّن الفني</button>
          </form>
        @endif
      </div>

      {{-- Media --}}
      @if($serviceRequest->media->count())
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
          <h3 class="font-bold text-gray-800 mb-3">📷 الوسائط</h3>
          <div class="grid grid-cols-2 gap-2">
            @foreach($serviceRequest->media as $m)
              <div class="aspect-square bg-gray-100 rounded-xl overflow-hidden">
                @if($m->type === 'image')
                  <img src="{{ asset('storage/' . $m->path) }}" class="w-full h-full object-cover" alt="">
                @else
                  <div class="w-full h-full flex items-center justify-center text-2xl">🎥</div>
                @endif
              </div>
            @endforeach
          </div>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
