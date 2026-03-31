<?php

namespace App\Policies;

use App\Models\ServiceRequest;
use App\Models\User;

class ServiceRequestPolicy
{
    /**
     * Admins and supervisors can do everything.
     */
    public function before(User $user): ?bool
    {
        if (in_array($user->role, ['admin', 'supervisor'])) {
            return true;
        }
        return null;
    }

    /** Client can view their own requests. */
    public function view(User $user, ServiceRequest $serviceRequest): bool
    {
        return $user->id === $serviceRequest->client_id;
    }

    /** Any authenticated client can create. */
    public function create(User $user): bool
    {
        return $user->role === 'client';
    }

    /** Client owns it and it hasn't started yet. */
    public function delete(User $user, ServiceRequest $serviceRequest): bool
    {
        return $user->id === $serviceRequest->client_id
            && in_array($serviceRequest->status, ['pending', 'cancelled']);
    }

    /** Client can approve/reject initial report if awaiting their approval. */
    public function approve(User $user, ServiceRequest $serviceRequest): bool
    {
        return $user->id === $serviceRequest->client_id
            && $serviceRequest->status === 'awaiting_approval';
    }

    /** Client can rate a completed request that hasn't been rated yet. */
    public function rate(User $user, ServiceRequest $serviceRequest): bool
    {
        return $user->id === $serviceRequest->client_id
            && $serviceRequest->status === 'completed'
            && $serviceRequest->rating === null;
    }

    /** Technician can update status of their assigned requests. */
    public function updateStatus(User $user, ServiceRequest $serviceRequest): bool
    {
        return $user->role === 'technician'
            && $user->id === $serviceRequest->technician_id;
    }

    /** Technician can submit reports for their assigned requests. */
    public function submitReport(User $user, ServiceRequest $serviceRequest): bool
    {
        return $user->role === 'technician'
            && $user->id === $serviceRequest->technician_id;
    }
}
