@props(['status' => 'pending'])

@php
$map = [
    'pending'          => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'label' => 'قيد الانتظار'],
    'assigned'         => ['bg' => 'bg-blue-100',   'text' => 'text-blue-700',   'label' => 'تم التعيين'],
    'on_way'           => ['bg' => 'bg-indigo-100',  'text' => 'text-indigo-700', 'label' => 'في الطريق'],
    'arrived'          => ['bg' => 'bg-cyan-100',    'text' => 'text-cyan-700',   'label' => 'وصل الفني'],
    'awaiting_approval'=> ['bg' => 'bg-purple-100',  'text' => 'text-purple-700', 'label' => 'بانتظار الموافقة'],
    'in_progress'      => ['bg' => 'bg-orange-100',  'text' => 'text-orange-700', 'label' => 'جارٍ التنفيذ'],
    'completed'        => ['bg' => 'bg-green-100',   'text' => 'text-green-700',  'label' => 'مكتمل'],
    'cancelled'        => ['bg' => 'bg-red-100',     'text' => 'text-red-700',    'label' => 'ملغي'],
];
$s = $map[$status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'label' => $status];
@endphp

<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $s['bg'] }} {{ $s['text'] }}">
  {{ $s['label'] }}
</span>
