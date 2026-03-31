<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PaymentGatewayController extends Controller
{
    public function index()
    {
        $gateways = PaymentGateway::orderBy('sort_order')->get();
        return view('admin.payment-gateways.index', compact('gateways'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'        => 'required|string|unique:payment_gateways,code',
            'name_ar'     => 'required|string|max:100',
            'name_en'     => 'required|string|max:100',
            'icon'        => 'nullable|string|max:500',
            'mode'        => 'required|in:test,live',
            'sort_order'  => 'integer|min:0',
            'min_amount'  => 'nullable|numeric|min:0',
            'max_amount'  => 'nullable|numeric|min:0',
        ]);

        PaymentGateway::create($data);
        return back()->with('success', 'تمت إضافة بوابة الدفع.');
    }

    public function update(Request $request, PaymentGateway $paymentGateway)
    {
        $data = $request->validate([
            'name_ar'     => 'required|string|max:100',
            'name_en'     => 'required|string|max:100',
            'is_enabled'  => 'boolean',
            'mode'        => 'required|in:test,live',
            'api_key'     => 'nullable|string',
            'secret_key'  => 'nullable|string',
            'merchant_id' => 'nullable|string',
            'entity_id'   => 'nullable|string',
            'sort_order'  => 'integer|min:0',
            'min_amount'  => 'nullable|numeric|min:0',
            'max_amount'  => 'nullable|numeric|min:0',
            'settings'    => 'nullable|array',
        ]);

        // Only update credential fields if provided (don't overwrite with empty)
        foreach (['api_key', 'secret_key', 'merchant_id', 'entity_id'] as $field) {
            if (empty($data[$field])) {
                unset($data[$field]);
            }
        }

        $data['is_enabled'] = $request->boolean('is_enabled');
        $paymentGateway->update($data);

        Cache::forget('payment_gateways_enabled');
        return back()->with('success', 'تم تحديث إعدادات البوابة.');
    }

    public function toggle(PaymentGateway $paymentGateway)
    {
        $paymentGateway->update(['is_enabled' => !$paymentGateway->is_enabled]);
        Cache::forget('payment_gateways_enabled');

        $status = $paymentGateway->is_enabled ? 'تفعيل' : 'تعطيل';
        return back()->with('success', "تم {$status} بوابة {$paymentGateway->name_ar}.");
    }
}
