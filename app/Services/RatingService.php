<?php

namespace App\Services;

use App\Models\ServiceRequest;
use App\Models\TechnicianProfile;
use Illuminate\Support\Facades\DB;

class RatingService
{
    /**
     * Apply a client rating to a completed service request.
     * Wrapped in a transaction to prevent partial updates.
     */
    public function rate(ServiceRequest $serviceRequest, int $rating): void
    {
        DB::transaction(function () use ($serviceRequest, $rating) {
            // Lock the request row to prevent duplicate ratings
            $serviceRequest = ServiceRequest::lockForUpdate()->find($serviceRequest->id);

            if ($serviceRequest->rating !== null) {
                return; // Already rated — idempotent guard
            }

            $serviceRequest->update(['rating' => $rating]);

            if (!$serviceRequest->technician_id) {
                return;
            }

            $profile = TechnicianProfile::lockForUpdate()
                ->where('user_id', $serviceRequest->technician_id)
                ->first();

            if (!$profile) {
                return;
            }

            $newTotal = $profile->total_ratings + 1;
            $newAvg   = (($profile->rating_average * $profile->total_ratings) + $rating) / $newTotal;

            $profile->update([
                'rating_average' => round($newAvg, 2),
                'total_ratings'  => $newTotal,
            ]);
        });
    }

    /**
     * Recalculate a technician's rating from scratch using all rated requests.
     */
    public function recalculate(int $technicianId): void
    {
        DB::transaction(function () use ($technicianId) {
            $profile = TechnicianProfile::lockForUpdate()
                ->where('user_id', $technicianId)
                ->first();

            if (!$profile) {
                return;
            }

            $rated = ServiceRequest::where('technician_id', $technicianId)
                ->whereNotNull('rating')
                ->get(['rating']);

            if ($rated->isEmpty()) {
                return;
            }

            $profile->update([
                'rating_average' => round($rated->avg('rating'), 2),
                'total_ratings'  => $rated->count(),
            ]);
        });
    }
}
