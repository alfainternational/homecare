@props(['request', 'showClient' => false, 'showTechnician' => false])

<tr class="hover:bg-gray-50 transition-colors">
  <td class="px-6 py-4 text-sm font-mono text-gray-600">{{ $request->request_number }}</td>
  <td class="px-6 py-4 text-sm text-gray-700">{{ $request->service_type }}</td>
  @if($showClient)
    <td class="px-6 py-4 text-sm text-gray-700">{{ $request->client?->name ?? '—' }}</td>
  @endif
  @if($showTechnician)
    <td class="px-6 py-4 text-sm text-gray-700">{{ $request->technician?->name ?? 'غير معين' }}</td>
  @endif
  <td class="px-6 py-4">
    <x-status-badge :status="$request->status" />
  </td>
  <td class="px-6 py-4 text-sm text-gray-400">{{ $request->created_at->format('Y/m/d') }}</td>
  <td class="px-6 py-4">
    {{ $slot }}
  </td>
</tr>
