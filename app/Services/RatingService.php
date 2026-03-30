<?php

namespace App\Services;

use App\Models\ServiceRequest;
use App\Models\TechnicianProfile;

class RatingService
{
    /**
     * Apply a client rating to a completed service request.
     * Updates the service request rating and recalculates the technician's
     * cumulative rating average.
     */
    public function rate(ServiceRequest $serviceRequest, int $rating): void
    {
        // Persist rating on the request
        $serviceRequest->update(['rating' => $rating]);

        // Recalculate technician's cumulative average
        if (!$serviceRequest->technician_id) {
            return;
        }

        $profile = TechnicianProfile::where('user_id', $serviceRequest->technician_id)->first();
        if (!$profile) {
            return;
        }

        $newTotal = $profile->total_ratings + 1;
        $newAvg   = (($profile->rating_average * $profile->total_ratings) + $rating) / $newTotal;

        $profile->update([
            'rating_average' => round($newAvg, 2),
            'total_ratings'  => $newTotal,
        ]);
    }

    /**
     * Recalculate a technician's rating from scratch using all rated requests.
     * Useful for data integrity checks.
     */
    public function recalculate(int $technicianId): void
    {
        $profile = TechnicianProfile::where('user_id', $technicianId)->first();
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
    }
}
