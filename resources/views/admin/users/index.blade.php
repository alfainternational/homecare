@extends('layouts.admin')
@section('title', 'إدارة المستخدمين')

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-black text-gray-800">إدارة المستخدمين</h1>
  </div>

  {{-- Stats --}}
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
      <div class="text-3xl font-black text-gray-800">{{ $stats['total'] }}</div>
      <div class="text-sm text-gray-500 mt-1">إجمالي المستخدمين</div>
    </div>
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5">
      <div class="text-3xl font-black text-blue-600">{{ $stats['clients'] }}</div>
      <div class="text-sm text-blue-500 mt-1">عملاء</div>
    </div>
    <div class="bg-orange-50 border border-orange-200 rounded-2xl p-5">
      <div class="text-3xl font-black text-[#D4881A]">{{ $stats['technicians'] }}</div>
      <div class="text-sm text-orange-500 mt-1">فنيون</div>
    </div>
    <div class="bg-purple-50 border border-purple-200 rounded-2xl p-5">
      <div class="text-3xl font-black text-purple-600">{{ $stats['admins'] }}</div>
      <div class="text-sm text-purple-500 mt-1">مديرون</div>
    </div>
  </div>

  {{-- Filters --}}
  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
    <form method="GET" class="flex flex-wrap gap-3 items-center">
      <select name="role" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#F5A623] outline-none">
        <option value="">جميع الأدوار</option>
        <option value="client" {{ request('role') === 'client' ? 'selected' : '' }}>عملاء</option>
        <option value="technician" {{ request('role') === 'technician' ? 'selected' : '' }}>فنيون</option>
        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>مديرون</option>
        <option value="supervisor" {{ request('role') === 'supervisor' ? 'selected' : '' }}>مشرفون</option>
      </select>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث بالاسم أو البريد أو الجوال..."
        class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#F5A623] outline-none w-64">
      <button type="submit" class="bg-[#F5A623] text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-[#D4881A] transition-all">بحث</button>
      <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-400 hover:text-[#D4881A]">مسح</a>
    </form>
  </div>

  {{-- Table --}}
  <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
          <tr>
            <th class="py-4 px-5 text-right font-bold text-gray-600">#</th>
            <th class="py-4 px-5 text-right font-bold text-gray-600">المستخدم</th>
            <th class="py-4 px-5 text-right font-bold text-gray-600">الجوال</th>
            <th class="py-4 px-5 text-center font-bold text-gray-600">الدور</th>
            <th class="py-4 px-5 text-center font-bold text-gray-600">الاشتراك</th>
            <th class="py-4 px-5 text-center font-bold text-gray-600">المحفظة</th>
            <th class="py-4 px-5 text-right font-bold text-gray-600">تاريخ التسجيل</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          @forelse($users as $u)
            @php
              $roleMap   = ['client'=>['عميل','bg-blue-100 text-blue-600'],'technician'=>['فني','bg-orange-100 text-orange-600'],'admin'=>['مدير','bg-purple-100 text-purple-600'],'supervisor'=>['مشرف','bg-indigo-100 text-indigo-600']];
              [$rLabel,$rClass] = $roleMap[$u->role] ?? ['غير معروف','bg-gray-100 text-gray-600'];
            @endphp
            <tr class="hover:bg-gray-50 transition-colors">
              <td class="py-4 px-5 text-gray-400 font-mono text-xs">{{ $u->id }}</td>
              <td class="py-4 px-5">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 bg-gray-100 rounded-full flex items-center justify-center text-gray-500 font-bold text-sm flex-shrink-0">
                    {{ mb_substr($u->name, 0, 1) }}
                  </div>
                  <div>
                    <div class="font-bold text-gray-800">{{ $u->name }}</div>
                    <div class="text-xs text-gray-400">{{ $u->email }}</div>
                  </div>
                </div>
              </td>
              <td class="py-4 px-5 text-gray-600 font-mono text-xs" dir="ltr">{{ $u->phone ?? '-' }}</td>
              <td class="py-4 px-5 text-center">
                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $rClass }}">{{ $rLabel }}</span>
              </td>
              <td class="py-4 px-5 text-center text-gray-600 text-xs">
                @if($u->subscription?->plan)
                  {{ $u->subscription->plan->name_ar }}
                @else
                  <span class="text-gray-300">—</span>
                @endif
              </td>
              <td class="py-4 px-5 text-center font-semibold text-gray-700">
                {{ $u->wallet ? number_format($u->wallet->balance, 2) . ' ر.س' : '—' }}
              </td>
              <td class="py-4 px-5 text-gray-500 text-xs">{{ $u->created_at->format('d/m/Y') }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="py-16 text-center">
                <div class="text-4xl mb-3 opacity-30">👥</div>
                <p class="text-gray-400">لا يوجد مستخدمون</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-50">{{ $users->withQueryString()->links() }}</div>
  </div>
</div>
@endsection
