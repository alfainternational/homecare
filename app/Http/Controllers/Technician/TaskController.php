<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
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

    public function submitInitialReport(Request $request, ServiceRequest $serviceRequest)
    {
        abort_if($serviceRequest->technician_id !== Auth::id(), 403);
        $data = $request->validate([
            'problem_description' => 'required|string|max:2000',
            'severity'            => 'required|in:low,medium,high',
            'estimated_duration'  => 'nullable|integer|min:1',
            'parts_needed'        => 'nullable|string|max:1000',
        ]);

        $partsArray = null;
        if (!empty($data['parts_needed'])) {
            $partsArray = array_map('trim', explode("\n", $data['parts_needed']));
        }

        RequestReport::create([
            'request_id'          => $serviceRequest->id,
            'type'                => 'initial',
            'reported_by'         => Auth::id(),
            'problem_description' => $data['problem_description'],
            'severity'            => $data['severity'],
            'estimated_duration'  => $data['estimated_duration'] ?? null,
            'parts_needed'        => $partsArray ? json_encode($partsArray) : null,
        ]);
        $serviceRequest->update(['status' => 'awaiting_approval']);

        return back()->with('success', 'تم إرسال التقرير الأولي — بانتظار موافقة العميل.');
    }

    public function submitFinalReport(Request $request, ServiceRequest $serviceRequest)
    {
        abort_if($serviceRequest->technician_id !== Auth::id(), 403);
        $data = $request->validate([
            'work_done'       => 'required|string|max:2000',
            'recommendations' => 'nullable|string|max:1000',
        ]);

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
