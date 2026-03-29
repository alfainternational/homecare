<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function index(){
        $subscription=Auth::user()->subscription()->with('plan')->first();
        $plans=Plan::where('is_active',true)->get();
        return view('client.subscription',compact('subscription','plans'));
    }
}
