<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\DashVideos;
use App\Models\SharePermissions;
use App\Models\VirtualFile;
use App\Policies\DashVideosPolicy;
use App\Policies\SharePermissionsPolicy;
use App\Policies\VirtualFilePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
        SharePermissions::class => SharePermissionsPolicy::class,
        VirtualFile::class => VirtualFilePolicy::class,
        DashVideos::class => DashVideosPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
