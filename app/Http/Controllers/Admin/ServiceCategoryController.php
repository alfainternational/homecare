<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceCategoryController extends Controller
{
    public function index()
    {
        $categories = ServiceCategory::withCount(['serviceRequests', 'jobPosts'])
            ->orderBy('sort_order')
            ->get();
        $parents = ServiceCategory::whereNull('parent_id')->where('is_active', true)->get();
        return view('admin.service-categories.index', compact('categories', 'parents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'parent_id'        => 'nullable|exists:service_categories,id',
            'name_ar'          => 'required|string|max:200',
            'name_en'          => 'nullable|string|max:200',
            'icon'             => 'nullable|string|max:10',
            'description_ar'   => 'nullable|string|max:1000',
            'is_on_demand'     => 'boolean',
            'is_marketplace'   => 'boolean',
            'is_subscription'  => 'boolean',
            'base_price'       => 'nullable|numeric|min:0',
            'price_on_request' => 'boolean',
            'sort_order'       => 'integer|min:0',
        ], [
            'name_ar.required' => 'يرجى إدخال اسم الفئة.',
        ]);

        $data['is_on_demand']    = $request->boolean('is_on_demand');
        $data['is_marketplace']  = $request->boolean('is_marketplace');
        $data['is_subscription'] = $request->boolean('is_subscription', true);
        $data['price_on_request']= $request->boolean('price_on_request');

        ServiceCategory::create($data);
        return back()->with('success', 'تمت إضافة فئة الخدمة.');
    }

    public function update(Request $request, ServiceCategory $serviceCategory)
    {
        $data = $request->validate([
            'parent_id'        => 'nullable|exists:service_categories,id',
            'name_ar'          => 'required|string|max:200',
            'name_en'          => 'nullable|string|max:200',
            'icon'             => 'nullable|string|max:10',
            'description_ar'   => 'nullable|string|max:1000',
            'is_active'        => 'boolean',
            'is_on_demand'     => 'boolean',
            'is_marketplace'   => 'boolean',
            'is_subscription'  => 'boolean',
            'base_price'       => 'nullable|numeric|min:0',
            'price_on_request' => 'boolean',
            'sort_order'       => 'integer|min:0',
        ]);

        $data['is_active']       = $request->boolean('is_active');
        $data['is_on_demand']    = $request->boolean('is_on_demand');
        $data['is_marketplace']  = $request->boolean('is_marketplace');
        $data['is_subscription'] = $request->boolean('is_subscription');
        $data['price_on_request']= $request->boolean('price_on_request');

        $serviceCategory->update($data);
        return back()->with('success', 'تم تحديث فئة الخدمة.');
    }

    public function destroy(ServiceCategory $serviceCategory)
    {
        $serviceCategory->delete();
        return back()->with('success', 'تم حذف الفئة.');
    }
}
