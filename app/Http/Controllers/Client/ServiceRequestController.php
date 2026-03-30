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
        $user = Auth::user()->load('primaryAddress');
        return view('client.requests.create', compact('user'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            // the form sends 'type' from Alpine hidden input; accept both
            'service_type'  => 'nullable|in:plumbing,electrical,hvac,general',
            'type'          => 'nullable|in:plumbing,electrical,hvac,general',
            'description'   => 'nullable|string|max:2000',
            'client_notes'  => 'nullable|string|max:1000',
            'notes'         => 'nullable|string|max:1000',
            'media'         => 'nullable|array|max:8',
            'media.*'       => 'file|mimes:jpg,jpeg,png,mp4,mov|max:20480',
            'street'        => 'nullable|string|max:255',
            'district'      => 'nullable|string|max:255',
            'city'          => 'nullable|string|max:100',
        ]);

        $serviceType = $data['service_type'] ?? $data['type'] ?? null;
        if (!$serviceType) {
            return back()->withErrors(['service_type' => 'يرجى اختيار نوع الخدمة.'])->withInput();
        }

        $sr = ServiceRequest::create([
            'service_type' => $serviceType,
            'description'  => $data['description'] ?? null,
            'client_notes' => $data['client_notes'] ?? $data['notes'] ?? null,
            'street'       => $data['street'] ?? null,
            'district'     => $data['district'] ?? null,
            'city'         => $data['city'] ?? null,
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

        // Save rating on the service request
        $serviceRequest->update(['rating' => $request->rating]);

        // Update technician profile rating average
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

    public function destroy(ServiceRequest $serviceRequest)
    {
        abort_if($serviceRequest->client_id !== Auth::id(), 403);
        abort_if(!in_array($serviceRequest->status, ['pending', 'cancelled']), 403, 'لا يمكن حذف طلب نشط.');
        $serviceRequest->delete();
        return redirect()->route('client.requests.index')->with('success', 'تم حذف الطلب بنجاح.');
    }
}
