<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;

class SubscriptionController extends Controller
{
    public function index(){
        $subscriptions=Subscription::with(['user','plan'])->latest()->paginate(20);
        return view('admin.subscriptions.index',compact('subscriptions'));
    }
    public function grantVisit(Subscription $subscription){
        $subscription->increment('visits_total');
        return back()->with('success','تم منح زيارة مجانية.');
    }
}
