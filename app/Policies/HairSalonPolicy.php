<?php

namespace App\Policies;

use App\Models\User;
use App\Models\HairSalon;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class HairSalonPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        $manager = request()->route('manager');
        return $user->status_id == 1 && $user->id == $manager->id
        ? Response::allow() 
        : ($manager->status_id == 1 ? Response::deny('Unauthorized') : Response::denyAsNotFound('Resource not found'));
    }

    public function create(User $user)
    {
        return $user->status_id == 1 
        ? Response::allow() : Response::deny('Unauthorized');
    }

    public function update(User $user, HairSalon $hairSalon)
    {
        return $user->status_id == 1 
        && $user->id == $hairSalon->manager_id 
        ? Response::allow() : Response::deny('Unauthorized');
    }

    public function delete(User $user, HairSalon $hairSalon)
    {
        return $user->status_id == 1 
        && $user->id == $hairSalon->manager_id 
        ? Response::allow() : Response::deny('Unauthorized');
    }
}
