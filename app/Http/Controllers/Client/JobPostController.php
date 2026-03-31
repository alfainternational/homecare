<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\JobBid;
use App\Models\JobPost;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class JobPostController extends Controller
{
    public function index(Request $request)
    {
        $posts = JobPost::where('client_id', Auth::id())
            ->with(['serviceCategory:id,name_ar,icon', 'bids', 'winningBid.technician:id,name,avatar'])
            ->latest()
            ->paginate(10);

        return view('client.marketplace.my-posts', compact('posts'));
    }

    public function create()
    {
        $categories = ServiceCategory::where('is_active', true)
            ->where('is_marketplace', true)
            ->orderBy('sort_order')
            ->get();

        $addresses = Auth::user()->addresses()->orderByDesc('is_primary')->get();

        return view('client.marketplace.create', compact('categories', 'addresses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'required|string|max:3000',
            'service_category_id' => 'required|exists:service_categories,id',
            'address_id'          => 'nullable|exists:addresses,id',
            'budget_min'          => 'nullable|numeric|min:0',
            'budget_max'          => 'nullable|numeric|min:0|gte:budget_min',
            'preferred_date'      => 'nullable|date|after:now',
            'media.*'             => 'nullable|file|mimes:jpg,jpeg,png,webp,mp4|max:20480',
        ], [
            'title.required'               => 'يرجى إدخال عنوان الطلب.',
            'description.required'         => 'يرجى وصف المشكلة بالتفصيل.',
            'service_category_id.required' => 'يرجى تحديد نوع الخدمة.',
            'budget_max.gte'               => 'الحد الأقصى للميزانية يجب أن يكون أكبر من الحد الأدنى.',
        ]);

        // Handle media uploads
        $mediaPaths = [];
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                if ($file->isValid()) {
                    $mediaPaths[] = $file->store('job-posts', 'public');
                }
            }
        }

        // Verify address belongs to user
        if (!empty($data['address_id'])) {
            abort_if(
                Auth::user()->addresses()->where('id', $data['address_id'])->doesntExist(),
                403, 'العنوان المحدد لا ينتمي لحسابك.'
            );
        }

        $post = JobPost::create(array_merge($data, [
            'client_id'   => Auth::id(),
            'media_paths' => $mediaPaths ?: null,
            'status'      => 'open',
            'expires_at'  => now()->addDays(7),
        ]));

        return redirect()->route('client.marketplace.show', $post)
            ->with('success', 'تم نشر طلبك بنجاح! يمكن للفنيين الآن تقديم عروضهم.');
    }

    public function show(JobPost $jobPost)
    {
        abort_if($jobPost->client_id !== Auth::id(), 403);

        $jobPost->load([
            'serviceCategory',
            'address',
            'bids.technician.technicianProfile',
        ]);

        return view('client.marketplace.show', compact('jobPost'));
    }

    public function selectBid(Request $request, JobPost $jobPost, JobBid $bid)
    {
        abort_if($jobPost->client_id !== Auth::id(), 403);
        abort_if($jobPost->status !== 'open', 422, 'هذا الطلب لم يعد مفتوحاً.');
        abort_if($bid->job_post_id !== $jobPost->id, 422, 'العرض لا ينتمي لهذا الطلب.');

        DB::transaction(function () use ($jobPost, $bid) {
            // Accept winning bid
            $bid->update(['status' => 'accepted', 'accepted_at' => now()]);

            // Reject all others
            $jobPost->bids()->where('id', '!=', $bid->id)->update(['status' => 'rejected']);

            // Update post
            $jobPost->update([
                'winning_bid_id' => $bid->id,
                'status'         => 'assigned',
                'assigned_at'    => now(),
            ]);
        });

        return back()->with('success', "تم اختيار الفني {$bid->technician->name}. سيتواصل معك قريباً.");
    }

    public function destroy(JobPost $jobPost)
    {
        abort_if($jobPost->client_id !== Auth::id(), 403);
        abort_if($jobPost->status !== 'open', 422, 'لا يمكن حذف طلب غير مفتوح.');

        $jobPost->update(['status' => 'cancelled']);
        $jobPost->delete();

        return redirect()->route('client.marketplace.index')
            ->with('success', 'تم إلغاء الطلب.');
    }
}
