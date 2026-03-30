<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\TechnicianProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceRequest::where('client_id', Auth::id())->with('technician')->latest();

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNotIn('status', ['completed', 'cancelled']);
            } elseif ($request->status === 'completed') {
                $query->where('status', 'completed');
            } elseif ($request->status === 'cancelled') {
                $query->where('status', 'cancelled');
            }
        }
        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('request_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $requests = $query->paginate(10);
        return view('client.requests.index', compact('requests'));
    }

    public function create()
    {
        return view('client.requests.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'service_type'  => 'required|in:plumbing,electrical,hvac,general',
            'description'   => 'nullable|string|max:2000',
            'client_notes'  => 'nullable|string|max:500',
            'media'         => 'nullable|array|max:5',
            'media.*'       => 'file|mimes:jpg,jpeg,png,mp4,mov|max:20480',
            'street'        => 'nullable|string|max:255',
            'district'      => 'nullable|string|max:255',
        ]);

        $sr = ServiceRequest::create([
            'service_type' => $data['service_type'],
            'description'  => $data['description'] ?? null,
            'client_notes' => $data['client_notes'] ?? null,
            'client_id'    => Auth::id(),
            'status'       => 'pending',
        ]);

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('request-media', 'public');
                $sr->media()->create([
                    'uploaded_by' => Auth::id(),
                    'path'        => $path,
                    'type'        => str_starts_with($file->getMimeType(), 'video') ? 'video' : 'image',
                ]);
            }
        }

        return redirect()->route('client.requests.show', $sr)->with('success', 'تم إرسال طلبك بنجاح! سنقوم بتعيين فني لك في أقرب وقت.');
    }

    public function show(ServiceRequest $serviceRequest)
    {
        abort_if($serviceRequest->client_id !== Auth::id(), 403);
        $serviceRequest->load(['technician.technicianProfile', 'reports.reporter', 'media', 'subscription.plan']);
        return view('client.requests.show', compact('serviceRequest'));
    }

    public function approveReport(ServiceRequest $serviceRequest)
    {
        abort_if($serviceRequest->client_id !== Auth::id(), 403);
        $report = $serviceRequest->initialReport;
        if ($report) {
            $report->update(['is_approved' => true, 'approved_at' => now()]);
            $serviceRequest->update(['status' => 'in_progress']);
        }
        return back()->with('success', 'تمت الموافقة على التقرير — الفني سيبدأ العمل الآن.');
    }

    public function rejectReport(ServiceRequest $serviceRequest)
    {
        abort_if($serviceRequest->client_id !== Auth::id(), 403);
        $report = $serviceRequest->initialReport;
        if ($report) {
            $report->update(['is_approved' => false]);
            $serviceRequest->update(['status' => 'assigned']);
        }
        return back()->with('success', 'تم رفض التقرير — سيتم إعادة تقييم الطلب.');
    }

    public function rate(Request $request, ServiceRequest $serviceRequest)
    {
        abort_if($serviceRequest->client_id !== Auth::id(), 403);
        abort_if($serviceRequest->status !== 'completed', 403);

        $request->validate([
            'rating'  => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:500',
        ]);

        // Update technician rating
        if ($serviceRequest->technician_id) {
            $profile = TechnicianProfile::where('user_id', $serviceRequest->technician_id)->first();
            if ($profile) {
                $newTotal = $profile->total_ratings + 1;
                $newAvg = (($profile->rating_average * $profile->total_ratings) + $request->rating) / $newTotal;
                $profile->update([
                    'rating_average' => round($newAvg, 2),
                    'total_ratings'  => $newTotal,
                ]);
            }
        }

        return back()->with('success', 'شكراً لتقييمك! تقييمك يساعدنا على تحسين الخدمة.');
    }
}
