<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\ParentProfile;
use App\Models\User;

class ParentProfilePolicy
{
    use HandlesAuthorization;
    
    /**
     * Determine whether the user can view any parent profiles.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
        return true;
    }

    /**
     * Determine whether the user can view the parent profile.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ParentProfile  $parentProfile
     * @return mixed
     */
    public function view(User $user, ParentProfile $parentProfile)
    {
        //
        return true;
    }

    /**
     * Determine whether the user can create parent profiles.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
        return true;
    }

    /**
     * Determine whether the user can update the parent profile.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ParentProfile  $parentProfile
     * @return mixed
     */
    public function update(User $user, ParentProfile $parentProfile)
    {
        //
        return true;
    }

    /**
     * Determine whether the user can delete the parent profile.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ParentProfile  $parentProfile
     * @return mixed
     */
    public function delete(User $user, ParentProfile $parentProfile)
    {
        //
    }

    /**
     * Determine whether the user can restore the parent profile.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ParentProfile  $parentProfile
     * @return mixed
     */
    public function restore(User $user, ParentProfile $parentProfile)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the parent profile.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ParentProfile  $parentProfile
     * @return mixed
     */
    public function forceDelete(User $user, ParentProfile $parentProfile)
    {
        //
    }
}
