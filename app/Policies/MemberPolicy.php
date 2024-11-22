<?php

namespace App\Policies;

use App\Models\Member;

class MemberPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Member $member): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Member $member, Member $otherMember): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Member $member, Member $otherMember): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Member $member, Member $otherMember): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Member $member, Member $otherMember): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Member $member, Member $otherMember): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Member $member, Member $otherMember): bool
    {
        //
    }
}
