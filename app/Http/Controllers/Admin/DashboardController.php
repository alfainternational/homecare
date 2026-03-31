<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ServiceRequest;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        // Cache static counters for 2 minutes to reduce DB load
        $stats = Cache::remember('admin_dashboard_stats', 120, function () {
            return [
                'active_subscriptions'  => Subscription::where('status', 'active')->count(),
                'today_requests'        => ServiceRequest::whereDate('created_at', today())->count(),
                'available_technicians' => User::where('role', 'technician')
                    ->whereHas('technicianProfile', fn ($q) => $q->where('status', 'available'))
                    ->count(),
                'monthly_revenue'       => Order::where('payment_status', 'paid')
                    ->whereMonth('created_at', now()->month)
                    ->sum('total'),
                'pending_requests'      => ServiceRequest::where('status', 'pending')->count(),
                'low_stock_products'    => Product::where('stock', '<=', 5)->where('is_active', true)->count(),
            ];
        });

        // Fix N+1: eager-load client and technician in one query
        $pendingRequests = ServiceRequest::where('status', 'pending')
            ->with(['client:id,name,phone', 'serviceCategory:id,name_ar'])
            ->latest()
            ->limit(10)
            ->get();

        // Eager-load technician profiles — no N+1
        $technicians = User::where('role', 'technician')
            ->with('technicianProfile:id,user_id,status,rating_average,total_ratings')
            ->select('id', 'name', 'phone', 'avatar')
            ->get();

        // Critical requests — eager-load both client and technician
        $criticalRequests = ServiceRequest::whereIn('status', ['pending', 'awaiting_approval'])
            ->with([
                'client:id,name,phone',
                'technician:id,name',
                'serviceCategory:id,name_ar',
            ])
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'pendingRequests', 'technicians', 'criticalRequests'
        ));
    }
}
