<?php

namespace App\Observers;

use App\Models\Member;
use Ramsey\Uuid\Uuid;

class MemberObserver
{
    public function creating(Member $member): void
    {
        $member->UUID = Uuid::uuid4();
    }

    public function created(Member $member): void
    {
    }

    public function updating(Member $member): void
    {
    }

    public function updated(Member $member): void
    {
    }

    public function saving(Member $member): void
    {
    }

    public function saved(Member $member): void
    {
    }

    public function deleting(Member $member): void
    {
    }

    public function deleted(Member $member): void
    {
    }

    public function restoring(Member $member): void
    {
    }

    public function restored(Member $member): void
    {
    }

    public function retrieved(Member $member): void
    {
    }

    public function forceDeleting(Member $member): void
    {
    }

    public function forceDeleted(Member $member): void
    {
    }

    public function replicating(Member $member): void
    {
    }
}
