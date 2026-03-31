<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobBid;
use App\Models\JobPost;
use App\Models\PlatformSetting;
use App\Models\TechnicianSubscription;
use App\Models\User;
use Illuminate\Http\Request;

class MarketplaceSettingsController extends Controller
{
    public function index()
    {
        $settings = PlatformSetting::where('group', 'marketplace')->get()->keyBy('key');

        $stats = [
            'open_jobs'            => JobPost::where('status', 'open')->count(),
            'total_bids'           => JobBid::where('status', 'pending')->count(),
            'active_tech_subs'     => TechnicianSubscription::where('status', 'active')->count(),
            'total_technicians'    => User::where('role', 'technician')->count(),
        ];

        $recentJobs = JobPost::with(['client:id,name', 'serviceCategory:id,name_ar', 'bids'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.marketplace.settings', compact('settings', 'stats', 'recentJobs'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'technician_model'     => 'required|in:subscription,commission,both',
            'monthly_sub_price'    => 'required|numeric|min:0',
            'annual_sub_price'     => 'required|numeric|min:0',
            'commission_rate'      => 'required|numeric|min:0|max:100',
            'marketplace_enabled'  => 'boolean',
            'on_demand_enabled'    => 'boolean',
            'auto_expire_days'     => 'integer|min:1|max:90',
            'max_bids_per_post'    => 'integer|min:1|max:50',
        ]);

        foreach ($data as $key => $value) {
            PlatformSetting::set($key, $value);
        }

        return back()->with('success', 'تم حفظ إعدادات المنصة.');
    }

    public function technicianSubscriptions(Request $request)
    {
        $subs = TechnicianSubscription::with('technician:id,name,phone,email')
            ->latest()
            ->paginate(20);

        return view('admin.marketplace.technician-subscriptions', compact('subs'));
    }

    public function grantFreeTechSubscription(Request $request)
    {
        $data = $request->validate([
            'technician_id' => 'required|exists:users,id',
            'days'          => 'required|integer|min:1|max:365',
        ]);

        TechnicianSubscription::create([
            'technician_id'       => $data['technician_id'],
            'plan_type'           => 'monthly',
            'amount_paid'         => 0,
            'status'              => 'active',
            'starts_at'           => now(),
            'ends_at'             => now()->addDays($data['days']),
            'is_commission_model' => false,
        ]);

        return back()->with('success', 'تم منح الاشتراك المجاني للفني.');
    }
}
