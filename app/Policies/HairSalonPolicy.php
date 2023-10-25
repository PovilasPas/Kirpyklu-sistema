<?php

namespace App\Policies;

use App\Models\User;
use App\Models\HairSalon;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class HairSalonPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->status_id == 1 
        ? Response::allow() : Response::deny('unauthorized');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\HairSalon  $hairSalon
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, HairSalon $hairSalon)
    {
        return $user->status_id == 1 
        && $user->id == $hairSalon->manager_id 
        ? Response::allow() : Response::deny('unauthorized');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\HairSalon  $hairSalon
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, HairSalon $hairSalon)
    {
        return $user->status_id == 1 
        && $user->id == $hairSalon->manager_id 
        ? Response::allow() : Response::deny('unauthorized');
    }
}
