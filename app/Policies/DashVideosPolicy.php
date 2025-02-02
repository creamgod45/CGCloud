<?php

namespace App\Policies;

use App\Models\DashVideos;
use App\Models\Member;
use Illuminate\Auth\Access\HandlesAuthorization;

class DashVideosPolicy
{
    use HandlesAuthorization;

    public function viewAny(Member $user): bool
    {

    }

    public function view(Member $user, DashVideos $dashVideos): bool
    {
    }

    public function create(Member $user): bool
    {
    }

    public function update(Member $user, DashVideos $dashVideos): bool
    {
    }

    public function delete(Member $user, DashVideos $dashVideos): bool
    {
    }

    public function restore(Member $user, DashVideos $dashVideos): bool
    {
    }

    public function forceDelete(Member $user, DashVideos $dashVideos): bool
    {
    }
}
