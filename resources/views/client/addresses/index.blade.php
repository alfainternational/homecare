@extends('layouts.dashboard')
@section('title', 'عناويني')

@section('content')
<div x-data="addressManager()" class="max-w-4xl mx-auto">

  {{-- Header --}}
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-black text-gray-800">عناويني</h1>
      <p class="text-sm text-gray-500 mt-1">يمكنك إضافة عدة عناوين واختيار الافتراضي</p>
    </div>
    <button @click="showModal = true"
            class="flex items-center gap-2 bg-brand hover:bg-brand-dark text-white font-bold px-5 py-2.5 rounded-xl transition-all shadow-sm">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
      إضافة عنوان
    </button>
  </div>

  @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-2xl px-4 py-3 mb-5 text-green-700 text-sm font-medium">
      {{ session('success') }}
    </div>
  @endif

  {{-- Address Cards --}}
  @if($addresses->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
      <div class="text-5xl mb-4">📍</div>
      <h3 class="text-lg font-bold text-gray-700 mb-2">لا توجد عناوين بعد</h3>
      <p class="text-gray-500 text-sm mb-6">أضف عنوانك الأول لتتمكن من طلب الخدمة</p>
      <button @click="showModal = true" class="btn-primary">إضافة عنوانك الأول</button>
    </div>
  @else
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      @foreach($addresses as $address)
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 relative
                  {{ $address->is_primary ? 'ring-2 ring-brand' : '' }}">

        @if($address->is_primary)
          <span class="absolute top-3 left-3 bg-brand text-white text-xs font-bold px-2.5 py-1 rounded-full">
            افتراضي
          </span>
        @endif

        <div class="flex items-start gap-3 mb-4">
          <div class="w-11 h-11 bg-orange-50 rounded-xl flex items-center justify-center text-2xl flex-shrink-0">
            {{ $address->type_icon }}
          </div>
          <div>
            <p class="font-bold text-gray-800">{{ $address->label }}</p>
            <p class="text-xs text-gray-400">{{ $address->type_label }}</p>
          </div>
        </div>

        <div class="text-sm text-gray-600 mb-4 leading-relaxed">
          {{ $address->full_address }}
          @if($address->extra_notes)
            <br><span class="text-gray-400 text-xs">{{ $address->extra_notes }}</span>
          @endif
        </div>

        @if($address->latitude && $address->longitude)
          <a href="https://maps.google.com/?q={{ $address->latitude }},{{ $address->longitude }}"
             target="_blank"
             class="text-xs text-brand hover:underline flex items-center gap-1 mb-4">
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            عرض على الخريطة
          </a>
        @endif

        <div class="flex items-center gap-2 pt-3 border-t border-gray-50">
          @unless($address->is_primary)
            <form method="POST" action="{{ route('client.addresses.primary', $address) }}" class="flex-1">
              @csrf
              <button class="w-full text-xs font-medium text-brand border border-brand rounded-lg px-3 py-2 hover:bg-brand-light transition-all">
                تعيين افتراضي
              </button>
            </form>
          @endunless

          <button @click="editAddress({{ $address->toJson() }})"
                  class="flex-1 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg px-3 py-2 hover:bg-gray-50 transition-all">
            تعديل
          </button>

          <form method="POST" action="{{ route('client.addresses.destroy', $address) }}"
                onsubmit="return confirm('هل أنت متأكد من حذف هذا العنوان؟')">
            @csrf @method('DELETE')
            <button class="text-xs font-medium text-red-500 border border-red-200 rounded-lg px-3 py-2 hover:bg-red-50 transition-all">
              حذف
            </button>
          </form>
        </div>
      </div>
      @endforeach
    </div>
  @endif

  {{-- Add/Edit Address Modal --}}
  <div x-show="showModal" x-cloak
       class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
       @click.self="closeModal()">

    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-xl max-h-[90vh] overflow-y-auto p-6"
         @click.stop>

      <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-black text-gray-800" x-text="editingId ? 'تعديل العنوان' : 'إضافة عنوان جديد'"></h3>
        <button @click="closeModal()" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-500 transition-all">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>

      <form :action="editingId ? '/dashboard/addresses/' + editingId : '/dashboard/addresses'"
            method="POST" class="space-y-4">
        @csrf
        <template x-if="editingId"><input type="hidden" name="_method" value="PUT"></template>

        {{-- Map picker --}}
        <div class="bg-gray-50 rounded-2xl overflow-hidden" style="height:220px" id="map-picker">
          <div id="map" class="w-full h-full"></div>
        </div>
        <input type="hidden" name="latitude" id="lat-input" :value="form.latitude">
        <input type="hidden" name="longitude" id="lng-input" :value="form.longitude">
        <input type="hidden" name="map_place_id" :value="form.map_place_id">
        <p class="text-xs text-gray-400 -mt-2">انقر على الخريطة لتحديد موقعك بدقة</p>

        {{-- Row 1 --}}
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="form-label">اسم العنوان</label>
            <input type="text" name="label" x-model="form.label" required placeholder="البيت، العمل..."
              class="form-input">
          </div>
          <div>
            <label class="form-label">نوع العنوان</label>
            <select name="type" x-model="form.type" required class="form-input">
              <option value="home">🏠 منزل</option>
              <option value="office">🏢 مكتب</option>
              <option value="rest_house">🏕️ استراحة</option>
            </select>
          </div>
        </div>

        {{-- Row 2 --}}
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="form-label">اسم الشارع</label>
            <input type="text" name="street" x-model="form.street" required placeholder="شارع الأمير سلطان"
              class="form-input">
          </div>
          <div>
            <label class="form-label">رقم المنزل / الشقة</label>
            <input type="text" name="address_number" x-model="form.address_number" placeholder="١٢٣"
              class="form-input">
          </div>
        </div>

        {{-- Row 3 --}}
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="form-label">الحي</label>
            <input type="text" name="district" x-model="form.district" placeholder="حي النزهة"
              class="form-input">
          </div>
          <div>
            <label class="form-label">المدينة</label>
            <input type="text" name="city" x-model="form.city" required placeholder="الرياض"
              class="form-input">
          </div>
        </div>

        {{-- Notes --}}
        <div>
          <label class="form-label">ملاحظات إضافية</label>
          <textarea name="extra_notes" x-model="form.extra_notes" rows="2" placeholder="مثال: الدور الثالث، الجهة اليمنى"
            class="form-input resize-none"></textarea>
        </div>

        {{-- Primary --}}
        <label class="flex items-center gap-3 cursor-pointer p-3 rounded-xl border border-gray-100 hover:bg-gray-50">
          <input type="checkbox" name="is_primary" value="1" x-model="form.is_primary" class="w-4 h-4 text-brand rounded">
          <div>
            <p class="text-sm font-semibold text-gray-700">جعله العنوان الافتراضي</p>
            <p class="text-xs text-gray-400">سيُستخدم هذا العنوان تلقائياً عند طلب الخدمة</p>
          </div>
        </label>

        <button type="submit"
                class="w-full bg-brand hover:bg-brand-dark text-white font-bold py-3.5 rounded-xl transition-all shadow-sm">
          <span x-text="editingId ? 'حفظ التعديلات' : 'إضافة العنوان'"></span>
        </button>
      </form>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
function addressManager() {
  return {
    showModal: false,
    editingId: null,
    form: {
      label: '', type: 'home', street: '', address_number: '',
      district: '', city: '', extra_notes: '',
      latitude: null, longitude: null, map_place_id: '', is_primary: false,
    },
    editAddress(address) {
      this.editingId  = address.id;
      this.form       = { ...address, is_primary: !!address.is_primary };
      this.showModal  = true;
      this.$nextTick(() => this.initMap());
    },
    closeModal() {
      this.showModal = false;
      this.editingId = null;
      this.form = { label:'',type:'home',street:'',address_number:'',district:'',city:'',extra_notes:'',latitude:null,longitude:null,map_place_id:'',is_primary:false };
    },
    initMap() {
      if (typeof L === 'undefined') return;
      const lat = this.form.latitude  || 24.7136;
      const lng = this.form.longitude || 46.6753;
      const map = L.map('map').setView([lat, lng], 13);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
      }).addTo(map);
      const marker = L.marker([lat, lng], { draggable: true }).addTo(map);
      const updateCoords = (latlng) => {
        this.form.latitude  = latlng.lat.toFixed(8);
        this.form.longitude = latlng.lng.toFixed(8);
        document.getElementById('lat-input').value = this.form.latitude;
        document.getElementById('lng-input').value = this.form.longitude;
      };
      marker.on('dragend', (e) => updateCoords(e.target.getLatLng()));
      map.on('click', (e) => { marker.setLatLng(e.latlng); updateCoords(e.latlng); });
    },
  }
}
</script>
{{-- Leaflet.js for map (open-source, no API key needed) --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const lat = {{ request()->old('latitude', 24.7136) }};
    const lng = {{ request()->old('longitude', 46.6753) }};
    const map = L.map('map').setView([lat, lng], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    const marker = L.marker([lat, lng], { draggable: true }).addTo(map);
    marker.on('dragend', (e) => {
      document.getElementById('lat-input').value = e.target.getLatLng().lat.toFixed(8);
      document.getElementById('lng-input').value = e.target.getLatLng().lng.toFixed(8);
    });
    map.on('click', (e) => {
      marker.setLatLng(e.latlng);
      document.getElementById('lat-input').value = e.latlng.lat.toFixed(8);
      document.getElementById('lng-input').value = e.latlng.lng.toFixed(8);
    });
  });
</script>
@endpush
