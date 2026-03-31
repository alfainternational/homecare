<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\JobBid;
use App\Models\JobPost;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BidController extends Controller
{
    /**
     * Browse open job posts available for bidding.
     */
    public function browse(Request $request)
    {
        $query = JobPost::open()
            ->with(['client:id,name', 'serviceCategory:id,name_ar,icon', 'bids'])
            ->withCount('bids');

        if ($request->filled('category')) {
            $query->where('service_category_id', $request->category);
        }
        if ($request->filled('city')) {
            $query->whereHas('address', fn ($q) => $q->where('city', 'like', "%{$request->city}%"));
        }

        $posts      = $query->latest()->paginate(12);
        $categories = ServiceCategory::where('is_active', true)->where('is_marketplace', true)->get();

        return view('technician.marketplace.browse', compact('posts', 'categories'));
    }

    /**
     * Show a single job post detail.
     */
    public function show(JobPost $jobPost)
    {
        abort_if($jobPost->status !== 'open', 404);

        $jobPost->load(['client:id,name', 'serviceCategory', 'address', 'bids' => function ($q) {
            $q->where('technician_id', Auth::id());
        }]);

        $myBid = $jobPost->bids->first();

        return view('technician.marketplace.show', compact('jobPost', 'myBid'));
    }

    /**
     * Submit a bid on a job post.
     */
    public function store(Request $request, JobPost $jobPost)
    {
        abort_if($jobPost->status !== 'open', 422, 'هذا الطلب لم يعد مفتوحاً.');

        // Check technician is subscribed or on commission model
        $this->ensureCanBid();

        // Check hasn't already bid
        if ($jobPost->bids()->where('technician_id', Auth::id())->exists()) {
            return back()->with('error', 'لقد قدمت عرضاً على هذا الطلب مسبقاً.');
        }

        $data = $request->validate([
            'price'              => 'required|numeric|min:1|max:99999',
            'estimated_duration' => 'nullable|integer|min:15|max:1440',
            'message'            => 'required|string|min:20|max:1000',
        ], [
            'price.required'   => 'يرجى تحديد سعر العرض.',
            'message.required' => 'يرجى كتابة رسالة تعريفية بعرضك.',
            'message.min'      => 'الرسالة يجب أن تكون 20 حرفاً على الأقل.',
        ]);

        $jobPost->bids()->create(array_merge($data, [
            'technician_id' => Auth::id(),
            'status'        => 'pending',
        ]));

        return back()->with('success', 'تم إرسال عرضك بنجاح! سيتم إشعارك عند استجابة العميل.');
    }

    /**
     * Withdraw a pending bid.
     */
    public function withdraw(JobBid $bid)
    {
        abort_if($bid->technician_id !== Auth::id(), 403);
        abort_if($bid->status !== 'pending', 422, 'لا يمكن سحب هذا العرض.');

        $bid->update(['status' => 'withdrawn']);
        return back()->with('success', 'تم سحب عرضك.');
    }

    /**
     * My submitted bids.
     */
    public function myBids(Request $request)
    {
        $bids = JobBid::where('technician_id', Auth::id())
            ->with(['jobPost.serviceCategory', 'jobPost.client:id,name'])
            ->latest()
            ->paginate(15);

        return view('technician.marketplace.my-bids', compact('bids'));
    }

    // ─── Private ───────────────────────────────────────────────────────────────

    private function ensureCanBid(): void
    {
        $tech = Auth::user();

        $hasActiveSub = \App\Models\TechnicianSubscription::where('technician_id', $tech->id)
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->exists();

        if (!$hasActiveSub) {
            abort(403, 'يجب الاشتراك في باقة الفنيين لتتمكن من تقديم العروض.');
        }
    }
}
