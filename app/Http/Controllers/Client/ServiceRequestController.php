<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\RateServiceRequestRequest;
use App\Http\Requests\Client\StoreServiceRequestRequest;
use App\Models\ServiceRequest;
use App\Services\MediaUploadService;
use App\Services\RatingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceRequestController extends Controller
{
    public function __construct(
        private readonly MediaUploadService $mediaService,
        private readonly RatingService $ratingService,
    ) {}

    public function index(Request $request)
    {
        $query = ServiceRequest::where('client_id', Auth::id())->with('technician')->latest();

        match($request->status) {
            'active'    => $query->whereNotIn('status', ['completed', 'cancelled']),
            'completed' => $query->where('status', 'completed'),
            'cancelled' => $query->where('status', 'cancelled'),
            default     => null,
        };

        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) =>
                $q->where('request_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
            );
        }

        $requests = $query->paginate(10)->withQueryString();
        return view('client.requests.index', compact('requests'));
    }

    public function create()
    {
        $user = Auth::user()->load('primaryAddress');
        return view('client.requests.create', compact('user'));
    }

    public function store(StoreServiceRequestRequest $request)
    {
        $serviceType = $request->resolvedServiceType();

        if (!$serviceType) {
            return back()->withErrors(['service_type' => 'يرجى اختيار نوع الخدمة.'])->withInput();
        }

        $sr = ServiceRequest::create([
            'service_type' => $serviceType,
            'description'  => $request->description,
            'client_notes' => $request->resolvedNotes(),
            'street'       => $request->street,
            'district'     => $request->district,
            'city'         => $request->city,
            'client_id'    => Auth::id(),
            'status'       => 'pending',
        ]);

        if ($request->hasFile('media')) {
            $this->mediaService->storeForRequest($sr, $request->file('media'));
        }

        return redirect()
            ->route('client.requests.show', $sr)
            ->with('success', 'تم إرسال طلبك بنجاح! سنقوم بتعيين فني لك في أقرب وقت.');
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

    public function rate(RateServiceRequestRequest $request, ServiceRequest $serviceRequest)
    {
        $this->ratingService->rate($serviceRequest, $request->validated()['rating']);
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
