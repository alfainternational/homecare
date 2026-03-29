<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index(){
        $stats=[
            'active_subscriptions'=>Subscription::where('status','active')->count(),
            'today_requests'=>ServiceRequest::whereDate('created_at',today())->count(),
            'available_technicians'=>User::where('role','technician')->whereHas('technicianProfile',fn($q)=>$q->where('status','available'))->count(),
            'monthly_revenue'=>Order::where('payment_status','paid')->whereMonth('created_at',now()->month)->sum('total'),
        ];
        $pendingRequests=ServiceRequest::where('status','pending')->with('client')->latest()->limit(10)->get();
        $technicians=User::where('role','technician')->with('technicianProfile')->get();
        $criticalRequests=ServiceRequest::whereIn('status',['pending'])->orWhere(fn($q)=>$q->where('status','awaiting_approval'))->with('client','technician')->latest()->limit(5)->get();
        return view('admin.dashboard',compact('stats','pendingRequests','technicians','criticalRequests'));
    }
}
