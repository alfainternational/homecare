<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function index(Request $request){
        $query=ServiceRequest::with(['client','technician'])->latest();
        if($request->filled('status'))$query->where('status',$request->status);
        if($request->filled('service_type'))$query->where('service_type',$request->service_type);
        $requests=$query->paginate(20);
        $technicians=User::where('role','technician')->with('technicianProfile')->get();
        return view('admin.requests.index',compact('requests','technicians'));
    }
    public function assignTechnician(Request $request,ServiceRequest $serviceRequest){
        $request->validate(['technician_id'=>'required|exists:users,id']);
        $serviceRequest->update(['technician_id'=>$request->technician_id,'status'=>'assigned']);
        return back()->with('success','تم تعيين الفني بنجاح.');
    }
}
