<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscription::with(['user', 'plan'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('plan_id')) {
            $query->where('plan_id', $request->plan_id);
        }

        $subscriptions = $query->paginate(20);
        $plans = Plan::where('is_active', true)->get();

        $stats = [
            'active'    => Subscription::where('status', 'active')->count(),
            'expired'   => Subscription::where('status', 'expired')->count(),
            'suspended' => Subscription::where('status', 'suspended')->count(),
            'expiring_soon' => Subscription::where('status', 'active')
                ->where('ends_at', '<=', now()->addDays(30))
                ->where('ends_at', '>', now())
                ->count(),
        ];

        return view('admin.subscriptions.index', compact('subscriptions', 'plans', 'stats'));
    }

    public function grantVisit(Subscription $subscription)
    {
        $subscription->increment('visits_total');
        return back()->with('success', 'تم منح زيارة مجانية بنجاح.');
    }

    public function suspend(Subscription $subscription)
    {
        $subscription->update(['status' => 'suspended']);
        return back()->with('success', 'تم تعليق الاشتراك.');
    }

    public function activate(Subscription $subscription)
    {
        $subscription->update(['status' => 'active']);
        return back()->with('success', 'تم تفعيل الاشتراك.');
    }
}
