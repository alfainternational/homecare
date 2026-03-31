@extends('layouts.admin')

@section('title', 'فئات الخدمات')
@section('page-title', 'فئات الخدمات')

@section('content')
<div class="space-y-6" x-data="categoriesPage()">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-accent">إدارة فئات الخدمات</h2>
            <p class="text-sm text-gray-500 mt-1">إضافة وتعديل فئات الخدمات والأسعار</p>
        </div>
        <button @click="openCreate()"
            class="bg-brand text-white text-sm font-semibold px-4 py-2 rounded-xl hover:bg-brand-dark transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            فئة جديدة
        </button>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    {{-- Categories Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-right">
                        <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">الفئة</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">النوع</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">السعر الأساسي</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">الفئة الأم</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-5 py-3.5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($categories as $category)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-brand-light rounded-xl flex items-center justify-center text-lg flex-shrink-0">
                                    {{ $category->icon ?? '🔧' }}
                                </div>
                                <div>
                                    <p class="font-semibold text-accent">{{ $category->name_ar }}</p>
                                    <p class="text-xs text-gray-400">{{ $category->name_en }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-wrap gap-1">
                                @if($category->is_subscription)
                                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">اشتراك</span>
                                @endif
                                @if($category->is_on_demand)
                                <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">طلب فوري</span>
                                @endif
                                @if($category->is_marketplace)
                                <span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">سوق حر</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-4 text-gray-600">
                            @if($category->price_on_request)
                                <span class="text-xs text-gray-400 italic">حسب الطلب</span>
                            @else
                                {{ $category->base_price ? number_format($category->base_price).' ر.س' : '—' }}
                            @endif
                        </td>
                        <td class="px-5 py-4 text-gray-500 text-xs">
                            {{ $category->parent?->name_ar ?? '—' }}
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-1 text-xs font-medium px-2.5 py-1 rounded-full
                                {{ $category->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $category->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                {{ $category->is_active ? 'نشطة' : 'معطلة' }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2 justify-end">
                                <button @click="openEdit({{ $category->id }}, @js($category))"
                                    class="text-xs text-brand hover:text-brand-dark font-medium px-3 py-1.5 bg-brand-light rounded-lg transition-colors">
                                    تعديل
                                </button>
                                <form action="{{ route('admin.service-categories.destroy', $category) }}" method="POST"
                                    onsubmit="return confirm('هل أنت متأكد من حذف هذه الفئة؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="text-xs text-red-600 hover:text-red-800 font-medium px-3 py-1.5 bg-red-50 rounded-lg transition-colors">
                                        حذف
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-gray-400">
                            لا توجد فئات حتى الآن
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($categories->hasPages())
        <div class="px-5 py-4 border-t border-gray-50">{{ $categories->links() }}</div>
        @endif
    </div>

    {{-- Modal --}}
    <div x-show="showModal" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
        @click.self="closeModal()">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto" @click.stop>
            <div class="flex items-center justify-between p-6 border-b border-gray-100">
                <h3 class="font-bold text-accent text-lg" x-text="modalTitle"></h3>
                <button @click="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form :action="formAction" method="POST" class="p-6 space-y-4">
                @csrf
                <span x-html="methodField"></span>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">الاسم بالعربية *</label>
                        <input type="text" name="name_ar" x-model="form.name_ar" required
                            class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-brand">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">الاسم بالإنجليزية</label>
                        <input type="text" name="name_en" x-model="form.name_en"
                            class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-brand">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">الأيقونة (إيموجي)</label>
                        <input type="text" name="icon" x-model="form.icon" placeholder="🔧"
                            class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-brand">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">الفئة الأم</label>
                        <select name="parent_id" x-model="form.parent_id"
                            class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-brand">
                            <option value="">— بدون فئة أم —</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name_ar }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">الوصف</label>
                    <textarea name="description" x-model="form.description" rows="2"
                        class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-brand resize-none"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">السعر الأساسي (ر.س)</label>
                        <input type="number" name="base_price" x-model="form.base_price" step="0.01" min="0"
                            class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-brand">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">الترتيب</label>
                        <input type="number" name="sort_order" x-model="form.sort_order" min="0"
                            class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-brand">
                    </div>
                </div>

                {{-- Flags --}}
                <div class="space-y-3 pt-1">
                    <p class="text-xs font-semibold text-gray-600">نوع الخدمة</p>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_subscription" value="1" x-model="form.is_subscription"
                            class="w-4 h-4 text-brand rounded border-gray-300 focus:ring-brand">
                        <span class="text-sm text-gray-700">اشتراك شهري / سنوي</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_on_demand" value="1" x-model="form.is_on_demand"
                            class="w-4 h-4 text-brand rounded border-gray-300 focus:ring-brand">
                        <span class="text-sm text-gray-700">خدمة عند الطلب (On-Demand)</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_marketplace" value="1" x-model="form.is_marketplace"
                            class="w-4 h-4 text-brand rounded border-gray-300 focus:ring-brand">
                        <span class="text-sm text-gray-700">سوق حر (Marketplace)</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="price_on_request" value="1" x-model="form.price_on_request"
                            class="w-4 h-4 text-brand rounded border-gray-300 focus:ring-brand">
                        <span class="text-sm text-gray-700">السعر حسب الطلب</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" x-model="form.is_active"
                            class="w-4 h-4 text-brand rounded border-gray-300 focus:ring-brand">
                        <span class="text-sm text-gray-700">فئة نشطة</span>
                    </label>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-brand text-white text-sm font-semibold py-2.5 rounded-xl hover:bg-brand-dark transition-colors">
                        حفظ
                    </button>
                    <button type="button" @click="closeModal()"
                        class="flex-1 border border-gray-200 text-sm text-gray-600 font-medium py-2.5 rounded-xl hover:bg-gray-50 transition-colors">
                        إلغاء
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
function categoriesPage() {
    return {
        showModal: false,
        modalTitle: '',
        formAction: '',
        methodField: '',
        form: {
            name_ar: '', name_en: '', icon: '', parent_id: '',
            description: '', base_price: '', sort_order: 0,
            is_subscription: false, is_on_demand: false,
            is_marketplace: false, price_on_request: false, is_active: true,
        },
        openCreate() {
            this.modalTitle = 'إضافة فئة جديدة';
            this.formAction = '{{ route("admin.service-categories.store") }}';
            this.methodField = '';
            this.form = { name_ar: '', name_en: '', icon: '', parent_id: '', description: '', base_price: '', sort_order: 0, is_subscription: false, is_on_demand: false, is_marketplace: false, price_on_request: false, is_active: true };
            this.showModal = true;
        },
        openEdit(id, category) {
            this.modalTitle = 'تعديل الفئة';
            this.formAction = `/admin/service-categories/${id}`;
            this.methodField = '<input type="hidden" name="_method" value="PUT">';
            this.form = {
                name_ar: category.name_ar ?? '',
                name_en: category.name_en ?? '',
                icon: category.icon ?? '',
                parent_id: category.parent_id ?? '',
                description: category.description ?? '',
                base_price: category.base_price ?? '',
                sort_order: category.sort_order ?? 0,
                is_subscription: !!category.is_subscription,
                is_on_demand: !!category.is_on_demand,
                is_marketplace: !!category.is_marketplace,
                price_on_request: !!category.price_on_request,
                is_active: !!category.is_active,
            };
            this.showModal = true;
        },
        closeModal() { this.showModal = false; },
    }
}
</script>
@endpush
@endsection
