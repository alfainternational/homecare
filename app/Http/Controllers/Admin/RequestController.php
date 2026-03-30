<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignTechnicianRequest;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceRequest::with(['client', 'technician'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }
        if ($request->filled('technician_id')) {
            $query->where('technician_id', $request->technician_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('request_number', 'like', "%{$search}%")
                  ->orWhereHas('client', fn ($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $requests = $query->paginate(20);
        $technicians = User::where('role', 'technician')->with('technicianProfile')->get();

        return view('admin.requests.index', compact('requests', 'technicians'));
    }

    public function show(ServiceRequest $serviceRequest)
    {
        $serviceRequest->load(['client', 'technician.technicianProfile', 'reports.reporter', 'media', 'subscription.plan']);
        $technicians = User::where('role', 'technician')->with('technicianProfile')->get();
        return view('admin.requests.show', compact('serviceRequest', 'technicians'));
    }

    public function assignTechnician(AssignTechnicianRequest $request, ServiceRequest $serviceRequest)
    {
        $serviceRequest->update([
            'technician_id' => $request->validated()['technician_id'],
            'status'        => 'assigned',
        ]);
        return back()->with('success', 'تم تعيين الفني بنجاح.');
    }

    public function addNote(Request $request, ServiceRequest $serviceRequest)
    {
        $request->validate(['note' => 'required|string|max:1000']);
        $existing = $serviceRequest->admin_notes ?? '';
        $timestamp = now()->format('Y-m-d H:i');
        $newNote = "[{$timestamp}] {$request->note}";
        $serviceRequest->update([
            'admin_notes' => $existing ? "{$existing}\n{$newNote}" : $newNote,
        ]);
        return back()->with('success', 'تمت إضافة الملاحظة.');
    }
}
