<?php

namespace App\Providers;

use App\Models\JobPost;
use App\Models\ServiceRequest;
use App\Policies\JobPostPolicy;
use App\Policies\ServiceRequestPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Paginator::useTailwind();

        // Authorization Policies
        Gate::policy(ServiceRequest::class, ServiceRequestPolicy::class);
        Gate::policy(JobPost::class, JobPostPolicy::class);
    }
}
