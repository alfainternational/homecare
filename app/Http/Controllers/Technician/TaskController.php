<?php
namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\RequestReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function show(ServiceRequest $serviceRequest){
        abort_if($serviceRequest->technician_id!==Auth::id(),403);
        $serviceRequest->load(['client','reports','media']);
        return view('technician.task',compact('serviceRequest'));
    }
    public function updateRequestStatus(Request $request,ServiceRequest $serviceRequest){
        abort_if($serviceRequest->technician_id!==Auth::id(),403);
        $request->validate(['status'=>'required|in:on_way,arrived,in_progress,completed']);
        $serviceRequest->update(['status'=>$request->status]);
        if($request->status==='completed')$serviceRequest->update(['completed_at'=>now()]);
        return back()->with('success','تم تحديث حالة الطلب.');
    }
    public function submitInitialReport(Request $request,ServiceRequest $serviceRequest){
        abort_if($serviceRequest->technician_id!==Auth::id(),403);
        $data=$request->validate(['problem_description'=>'required|string','severity'=>'required|in:low,medium,high','estimated_duration'=>'nullable|integer']);
        RequestReport::create([...$data,'request_id'=>$serviceRequest->id,'type'=>'initial','reported_by'=>Auth::id()]);
        $serviceRequest->update(['status'=>'awaiting_approval']);
        return back()->with('success','تم إرسال التقرير الأولي.');
    }
    public function submitFinalReport(Request $request,ServiceRequest $serviceRequest){
        abort_if($serviceRequest->technician_id!==Auth::id(),403);
        $data=$request->validate(['work_done'=>'required|string','recommendations'=>'nullable|string']);
        RequestReport::create([...$data,'request_id'=>$serviceRequest->id,'type'=>'final','reported_by'=>Auth::id(),'problem_description'=>'','severity'=>'low']);
        $serviceRequest->update(['status'=>'completed','completed_at'=>now()]);
        return back()->with('success','تم إغلاق المهمة بنجاح.');
    }
}
