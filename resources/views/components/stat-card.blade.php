@props([
    'icon'        => '📊',
    'label'       => '',
    'value'       => '',
    'sub'         => null,
    'color'       => 'orange',   // orange | blue | green | red | purple
    'href'        => null,
])

@php
$colorMap = [
    'orange' => ['bg' => 'bg-orange-50',  'icon' => 'bg-orange-100 text-orange-600', 'value' => 'text-[#D4881A]'],
    'blue'   => ['bg' => 'bg-blue-50',    'icon' => 'bg-blue-100 text-blue-600',     'value' => 'text-blue-700'],
    'green'  => ['bg' => 'bg-green-50',   'icon' => 'bg-green-100 text-green-600',   'value' => 'text-green-700'],
    'red'    => ['bg' => 'bg-red-50',     'icon' => 'bg-red-100 text-red-600',       'value' => 'text-red-700'],
    'purple' => ['bg' => 'bg-purple-50',  'icon' => 'bg-purple-100 text-purple-600', 'value' => 'text-purple-700'],
];
$c = $colorMap[$color] ?? $colorMap['orange'];
$tag = $href ? 'a' : 'div';
$attrs = $href ? "href=\"{$href}\"" : '';
@endphp

<{{ $tag }} {!! $attrs !!} class="{{ $c['bg'] }} rounded-2xl p-5 flex items-center gap-4 {{ $href ? 'hover:shadow-md transition-shadow' : '' }}">
  <div class="{{ $c['icon'] }} w-12 h-12 rounded-xl flex items-center justify-center text-2xl flex-shrink-0">
    {{ $icon }}
  </div>
  <div class="min-w-0">
    <p class="text-xs text-gray-500 font-medium truncate">{{ $label }}</p>
    <p class="{{ $c['value'] }} text-2xl font-black leading-tight">{{ $value }}</p>
    @if($sub)
      <p class="text-xs text-gray-400 mt-0.5">{{ $sub }}</p>
    @endif
  </div>
</{{ $tag }}>
