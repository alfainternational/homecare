<?php
namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(){
        $user=Auth::user()->load('technicianProfile');
        $todayTasks=ServiceRequest::where('technician_id',Auth::id())->with('client')->whereIn('status',['assigned','on_way','arrived','in_progress'])->get();
        $activeTask=ServiceRequest::where('technician_id',Auth::id())->whereIn('status',['on_way','arrived','in_progress'])->with('client')->first();
        $monthTasks=ServiceRequest::where('technician_id',Auth::id())->whereMonth('created_at',now()->month)->count();
        return view('technician.dashboard',compact('user','todayTasks','activeTask','monthTasks'));
    }
    public function updateStatus(Request $request){
        $profile=Auth::user()->technicianProfile;
        $request->validate(['status'=>'required|in:available,busy,off']);
        $profile->update(['status'=>$request->status]);
        return back()->with('success','تم تحديث حالتك.');
    }
}
