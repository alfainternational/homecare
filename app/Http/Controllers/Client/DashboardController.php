<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user          = Auth::user()->load(['subscription.plan', 'wallet']);
        $latestRequest = ServiceRequest::where('client_id', Auth::id())
            ->with('technician')
            ->latest()
            ->first();
        $notifications = Notification::where('notifiable_id', Auth::id())
            ->latest()
            ->limit(5)
            ->get();

        $sub             = $user->subscription;
        $visitsRemaining = $sub ? $sub->visitsRemaining() : 0;
        $visitsTotal     = $sub->visits_total ?? 0;
        $visitsUsed      = $sub->visits_used  ?? 0;
        $visitsPercent   = $visitsTotal > 0 ? round(($visitsUsed / $visitsTotal) * 100) : 0;
        $renewalDate     = $sub?->ends_at;
        $daysLeft        = $renewalDate ? max(0, (int) now()->diffInDays($renewalDate, false)) : 0;
        $planName        = $sub?->plan?->name_ar ?? 'لا يوجد اشتراك';

        $totalRequests = ServiceRequest::where('client_id', Auth::id())->count();
        $avgRating     = ServiceRequest::where('client_id', Auth::id())->whereNotNull('rating')->avg('rating');
        $ratingCount   = ServiceRequest::where('client_id', Auth::id())->whereNotNull('rating')->count();

        return view('client.dashboard', compact(
            'user', 'latestRequest', 'notifications',
            'sub', 'visitsRemaining', 'visitsTotal', 'visitsUsed', 'visitsPercent',
            'renewalDate', 'daysLeft', 'planName',
            'totalRequests', 'avgRating', 'ratingCount',
        ));
    }
}
