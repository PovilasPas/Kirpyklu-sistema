<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Hairdresser;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class HairdresserPolicy
{
    use HandlesAuthorization;

    public function view(?User $user, Hairdresser $hairdresser)
    {
        $salon = request()->route('salon');
        return $hairdresser->is_approved == 1 
        || $hairdresser->is_approved == 0 
        && ($user && $user->status_id == 1 && $user->id== $salon->manager_id 
        || $user && $user->status_id == 2 && $user->id == $hairdresser->id)
        ? Response::allow() : Response::deny('Unauthorized');
    }

    public function create(User $user)
    {
        return $user->status_id == 2 
        ? Response::allow() : Response::deny('Unauthorized');
    }

    public function update(User $user, Hairdresser $hairdresser)
    {
        $salon = request()->route('salon');
        return $user->status_id == 2 
        && $user->id == $hairdresser->id
        || $user->status_id == 1 
        && $user->id == $salon->manager_id
        ? Response::allow() : Response::deny('Unauthorized');
    }

    public function delete(User $user, Hairdresser $hairdresser)
    {
        $salon = request()->route('salon');
        return $user->status_id == 2 
        && $user->id == $hairdresser->id 
        || $user->status_id == 1 
        && $user->id == $salon->manager_id
        ? Response::allow() : Response::deny('Unauthorized');
    }
}
