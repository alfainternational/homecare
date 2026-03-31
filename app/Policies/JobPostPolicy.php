<?php

namespace App\Policies;

use App\Models\JobPost;
use App\Models\User;

class JobPostPolicy
{
    public function before(User $user): ?bool
    {
        if (in_array($user->role, ['admin', 'supervisor'])) {
            return true;
        }
        return null;
    }

    public function view(User $user, JobPost $jobPost): bool
    {
        // Clients see their own posts; technicians see open posts
        return $user->id === $jobPost->client_id
            || ($user->role === 'technician' && $jobPost->status === 'open');
    }

    public function create(User $user): bool
    {
        return $user->role === 'client';
    }

    public function delete(User $user, JobPost $jobPost): bool
    {
        return $user->id === $jobPost->client_id
            && $jobPost->status === 'open';
    }

    public function selectBid(User $user, JobPost $jobPost): bool
    {
        return $user->id === $jobPost->client_id
            && $jobPost->status === 'open';
    }

    public function bid(User $user, JobPost $jobPost): bool
    {
        return $user->role === 'technician'
            && $jobPost->status === 'open';
    }
}
