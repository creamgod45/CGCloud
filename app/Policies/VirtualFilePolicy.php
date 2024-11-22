<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\VirtualFile;
use Illuminate\Auth\Access\HandlesAuthorization;

class VirtualFilePolicy
{
    use HandlesAuthorization;

    public function viewAny(Member $user): bool
    {

    }

    public function view(Member $user, VirtualFile $virtualFile): bool
    {
    }

    public function create(Member $user): bool
    {
    }

    public function update(Member $user, VirtualFile $virtualFile): bool
    {
    }

    public function delete(Member $user, VirtualFile $virtualFile): bool
    {
    }

    public function restore(Member $user, VirtualFile $virtualFile): bool
    {
    }

    public function forceDelete(Member $user, VirtualFile $virtualFile): bool
    {
    }
}
