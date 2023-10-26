<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Hairstyle;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class HairstylePolicy
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
        $hairdresser = request()->route('hairdresser');
        return $user->status_id == 2 
        && $hairdresser->id == $user->id 
        ? Response::allow() : Response::deny('Unauthorized');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Hairstyle  $hairstyle
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Hairstyle $hairstyle)
    {
        $hairdresser = request()->route('hairdresser');
        return $user->status_id == 2 
        && $hairdresser->id == $user->id 
        && $hairstyle->hairdresser_id == $hairdresser->id
        ? Response::allow() : Response::deny('Unauthorized');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Hairstyle  $hairstyle
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Hairstyle $hairstyle)
    {
        $hairdresser = request()->route('hairdresser');
        return $user->status_id == 2 
        && $hairdresser->id == $user->id 
        && $hairstyle->hairdresser_id == $hairdresser->id 
        ? Response::allow() : Response::deny('Unauthorized');
    }
}
