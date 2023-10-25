<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Hairdresser;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class HairdresserPolicy
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
        return $user->status_id == 2 
        ? Response::allow() : Response::deny('unauthorized');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Hairdresser  $hairdresser
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Hairdresser $hairdresser)
    {
        $salon = request()->route('salon');
        return $user->status_id == 2 
        && $user->id == $hairdresser->id
        || $user->status_id == 1 
        && $user->id == $salon->manager_id
        ? Response::allow() : Response::deny('unauthorized');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Hairdresser  $hairdresser
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Hairdresser $hairdresser)
    {
        $salon = request()->route('salon');
        return $user->status_id == 2 
        && $user->id == $hairdresser->id 
        || $user->status_id == 1 
        && $user->id == $salon->manager_id
        ? Response::allow() : Response::deny('unauthorized');
    }
}
