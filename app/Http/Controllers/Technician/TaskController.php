<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Http\Requests\Technician\FinalReportRequest;
use App\Http\Requests\Technician\InitialReportRequest;
use App\Models\ServiceRequest;
use App\Models\RequestReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = ServiceRequest::where('technician_id', Auth::id())
            ->with('client')
            ->latest()
            ->paginate(15);

        return view('technician.tasks', compact('tasks'));
    }

    public function show(ServiceRequest $serviceRequest)
    {
        abort_if($serviceRequest->technician_id !== Auth::id(), 403);
        $serviceRequest->load(['client', 'reports.reporter', 'media']);
        return view('technician.task', compact('serviceRequest'));
    }

    public function accept(ServiceRequest $serviceRequest)
    {
        abort_if($serviceRequest->technician_id !== Auth::id(), 403);
        $serviceRequest->update(['status' => 'assigned']);
        return back()->with('success', 'تم قبول المهمة.');
    }

    public function reject(ServiceRequest $serviceRequest)
    {
        abort_if($serviceRequest->technician_id !== Auth::id(), 403);
        $serviceRequest->update(['technician_id' => null, 'status' => 'pending']);
        return redirect()->route('tech.dashboard')->with('success', 'تم الاعتذار عن المهمة.');
    }

    public function updateRequestStatus(Request $request, ServiceRequest $serviceRequest)
    {
        abort_if($serviceRequest->technician_id !== Auth::id(), 403);
        $request->validate(['status' => 'required|in:on_way,arrived,in_progress,completed']);
        $serviceRequest->update(['status' => $request->status]);
        if ($request->status === 'completed') {
            $serviceRequest->update(['completed_at' => now()]);
        }
        return back()->with('success', 'تم تحديث حالة الطلب.');
    }

    public function submitInitialReport(InitialReportRequest $request, ServiceRequest $serviceRequest)
    {
        $data = $request->validated();

        RequestReport::create([
            'request_id'          => $serviceRequest->id,
            'type'                => 'initial',
            'reported_by'         => Auth::id(),
            'problem_description' => $data['problem_description'],
            'severity'            => $data['severity'],
            'estimated_duration'  => $data['estimated_duration'] ?? null,
            'estimated_cost'      => $data['estimated_cost'] ?? null,
            'parts_needed'        => $request->parsedParts(),
        ]);
        $serviceRequest->update(['status' => 'awaiting_approval']);

        return back()->with('success', 'تم إرسال التقرير الأولي — بانتظار موافقة العميل.');
    }

    public function submitFinalReport(FinalReportRequest $request, ServiceRequest $serviceRequest)
    {
        $data = $request->validated();

        RequestReport::create([
            'request_id'          => $serviceRequest->id,
            'type'                => 'final',
            'reported_by'         => Auth::id(),
            'problem_description' => $data['work_done'],
            'severity'            => 'low',
            'work_done'           => $data['work_done'],
            'recommendations'     => $data['recommendations'] ?? null,
        ]);
        $serviceRequest->update(['status' => 'completed', 'completed_at' => now()]);

        return back()->with('success', 'تم إغلاق المهمة بنجاح — سيتم إشعار العميل.');
    }
}
