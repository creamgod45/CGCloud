<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\DashVideos;
use App\Models\Member;
use App\Models\SharePermissions;
use App\Models\VirtualFile;
use App\Policies\DashVideosPolicy;
use App\Policies\SharePermissionsPolicy;
use App\Policies\VirtualFilePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
        Gate::define('viewPulse', function (Member $user) {
            // 在這裡定義誰可以訪問 Pulse
            // 例如：只有 ID 為 1 的使用者
            return true; // 暫時開放給所有登入成員，建議之後根據需求修改
        });
    }
}
