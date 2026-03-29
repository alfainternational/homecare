<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceRequestController extends Controller
{
    public function index(){
        $requests=ServiceRequest::where('client_id',Auth::id())->with('technician')->latest()->paginate(10);
        return view('client.requests.index',compact('requests'));
    }
    public function create(){return view('client.requests.create');}
    public function store(Request $request){
        $data=$request->validate([
            'service_type'=>'required|in:plumbing,electrical,hvac,general',
            'description'=>'nullable|string|max:2000',
            'client_notes'=>'nullable|string|max:500',
        ]);
        $sr=ServiceRequest::create([...$data,'client_id'=>Auth::id(),'status'=>'pending']);
        if($request->hasFile('media')){
            foreach($request->file('media') as $file){
                $path=$file->store('request-media','public');
                $sr->media()->create(['uploaded_by'=>Auth::id(),'path'=>$path,'type'=>str_starts_with($file->getMimeType(),'video')?'video':'image']);
            }
        }
        return redirect()->route('client.requests.show',$sr)->with('success','تم إرسال طلبك بنجاح!');
    }
    public function show(ServiceRequest $serviceRequest){
        abort_if($serviceRequest->client_id!==Auth::id(),403);
        $serviceRequest->load(['technician','reports.reporter','media','subscription.plan']);
        return view('client.requests.show',compact('serviceRequest'));
    }
    public function approveReport(ServiceRequest $serviceRequest){
        abort_if($serviceRequest->client_id!==Auth::id(),403);
        $report=$serviceRequest->initialReport;
        if($report){
            $report->update(['is_approved'=>true,'approved_at'=>now()]);
            $serviceRequest->update(['status'=>'in_progress']);
        }
        return back()->with('success','تمت الموافقة على التقرير.');
    }
}
