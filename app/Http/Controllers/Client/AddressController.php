<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = Auth::user()->addresses()->orderByDesc('is_primary')->orderByDesc('created_at')->get();
        return view('client.addresses.index', compact('addresses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'label'          => 'required|string|max:100',
            'type'           => 'required|in:home,office,rest_house',
            'street'         => 'required|string|max:255',
            'address_number' => 'nullable|string|max:50',
            'district'       => 'nullable|string|max:255',
            'city'           => 'required|string|max:100',
            'building_number'=> 'nullable|string|max:50',
            'floor'          => 'nullable|string|max:20',
            'extra_notes'    => 'nullable|string|max:500',
            'latitude'       => 'nullable|numeric|between:-90,90',
            'longitude'      => 'nullable|numeric|between:-180,180',
            'map_place_id'   => 'nullable|string|max:255',
            'is_primary'     => 'boolean',
        ], [
            'label.required'  => 'يرجى إدخال اسم العنوان.',
            'type.required'   => 'يرجى تحديد نوع العنوان.',
            'street.required' => 'يرجى إدخال اسم الشارع.',
            'city.required'   => 'يرجى إدخال اسم المدينة.',
        ]);

        $user      = Auth::user();
        $isPrimary = $request->boolean('is_primary') || $user->addresses()->count() === 0;

        // Remove primary from others if this will be primary
        if ($isPrimary) {
            $user->addresses()->update(['is_primary' => false]);
        }

        $address = $user->addresses()->create(array_merge($data, [
            'is_primary' => $isPrimary,
        ]));

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'address' => $address]);
        }

        return back()->with('success', 'تم إضافة العنوان بنجاح.');
    }

    public function update(Request $request, Address $address)
    {
        abort_if($address->user_id !== Auth::id(), 403);

        $data = $request->validate([
            'label'          => 'sometimes|string|max:100',
            'type'           => 'sometimes|in:home,office,rest_house',
            'street'         => 'sometimes|string|max:255',
            'address_number' => 'nullable|string|max:50',
            'district'       => 'nullable|string|max:255',
            'city'           => 'sometimes|string|max:100',
            'building_number'=> 'nullable|string|max:50',
            'floor'          => 'nullable|string|max:20',
            'extra_notes'    => 'nullable|string|max:500',
            'latitude'       => 'nullable|numeric|between:-90,90',
            'longitude'      => 'nullable|numeric|between:-180,180',
            'map_place_id'   => 'nullable|string|max:255',
        ]);

        $address->update($data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'address' => $address->fresh()]);
        }

        return back()->with('success', 'تم تحديث العنوان بنجاح.');
    }

    public function setPrimary(Address $address)
    {
        abort_if($address->user_id !== Auth::id(), 403);
        $address->makePrimary();
        return back()->with('success', 'تم تعيين «' . $address->label . '» كعنوان افتراضي.');
    }

    public function destroy(Address $address)
    {
        abort_if($address->user_id !== Auth::id(), 403);
        abort_if($address->is_primary && Auth::user()->addresses()->count() > 1, 422,
            'لا يمكن حذف العنوان الافتراضي. حدد عنواناً آخر أولاً.');

        $address->delete();
        return back()->with('success', 'تم حذف العنوان.');
    }
}
