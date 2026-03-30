@extends('layouts.tech')
@section('title', 'جميع المهام')
@section('page-title', 'جميع المهام')

@section('content')
<div class="space-y-6">
  @php
    $typeMap   = ['plumbing'=>'سباكة','electrical'=>'كهرباء','hvac'=>'تكييف','general'=>'عام'];
    $statusMap = ['pending'=>['قيد الانتظار','bg-yellow-100 text-yellow-700'],'assigned'=>['معيّن','bg-blue-100 text-blue-700'],'on_way'=>['في الطريق','bg-indigo-100 text-indigo-700'],'arrived'=>['وصل','bg-teal-100 text-teal-700'],'in_progress'=>['جاري','bg-purple-100 text-purple-700'],'awaiting_approval'=>['بانتظار الموافقة','bg-orange-100 text-orange-700'],'completed'=>['مكتمل','bg-green-100 text-green-700'],'cancelled'=>['ملغي','bg-red-100 text-red-600']];
  @endphp

  @forelse($tasks as $task)
    @php [$sLabel,$sClass] = $statusMap[$task->status] ?? ['','bg-gray-100 text-gray-600']; @endphp
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:border-[#F5A623] transition-all">
      <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-4">
          <div class="w-11 h-11 bg-orange-50 rounded-xl flex items-center justify-center text-lg">
            {{ ['plumbing'=>'🔧','electrical'=>'⚡','hvac'=>'❄️','general'=>'🏠'][$task->service_type] ?? '🔧' }}
          </div>
          <div>
            <p class="font-bold text-gray-800">{{ $typeMap[$task->service_type] ?? $task->service_type }} — #{{ $task->request_number }}</p>
            <p class="text-sm text-gray-500">{{ $task->client?->name }} · {{ $task->created_at->format('d/m/Y') }}</p>
          </div>
        </div>
        <div class="flex items-center gap-3">
          <span class="px-3 py-1 rounded-full text-xs font-bold {{ $sClass }}">{{ $sLabel }}</span>
          <a href="{{ route('tech.tasks.show', $task) }}" class="bg-[#F5A623] hover:bg-[#D4881A] text-white px-4 py-2 rounded-xl text-sm font-bold transition-all">
            التفاصيل
          </a>
        </div>
      </div>
    </div>
  @empty
    <div class="text-center py-20">
      <div class="text-6xl mb-4 opacity-30">📋</div>
      <p class="text-gray-400 font-medium">لا توجد مهام</p>
    </div>
  @endforelse

  <div>{{ $tasks->links() }}</div>
</div>
@endsection
