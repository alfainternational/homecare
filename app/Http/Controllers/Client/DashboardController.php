<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(){
        $user=Auth::user()->load(['subscription.plan','wallet']);
        $latestRequest=\App\Models\ServiceRequest::where('client_id',Auth::id())->with('technician')->latest()->first();
        $notifications=\App\Models\Notification::where('notifiable_id',Auth::id())->latest()->limit(5)->get();
        return view('client.dashboard',compact('user','latestRequest','notifications'));
    }
}
