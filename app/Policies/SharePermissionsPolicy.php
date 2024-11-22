<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\SharePermissions;
use Illuminate\Auth\Access\HandlesAuthorization;

class SharePermissionsPolicy
{
    use HandlesAuthorization;

    public function viewAny(Member $user): bool
    {

    }

    public function view(Member $user, SharePermissions $sharePermissions): bool
    {
    }

    public function create(Member $user): bool
    {
    }

    public function update(Member $user, SharePermissions $sharePermissions): bool
    {
    }

    public function delete(Member $user, SharePermissions $sharePermissions): bool
    {
    }

    public function restore(Member $user, SharePermissions $sharePermissions): bool
    {
    }

    public function forceDelete(Member $user, SharePermissions $sharePermissions): bool
    {
    }
}
