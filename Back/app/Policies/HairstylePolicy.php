<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Hairstyle;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class HairstylePolicy
{
    use HandlesAuthorization;

    public function viewAny(?User $user)
    {
        $hairdresser = request()->route('hairdresser');
        return $hairdresser->is_approved == 1
        ? Response::allow() : Response::deny('Unauthorized');
    }

    public function view(?User $user, Hairstyle $hairstyle)
    {
        $hairdresser = request()->route('hairdresser');
        return $hairdresser->is_approved == 1
        ? Response::allow() : Response::deny('Unauthorized');
    }

    public function create(User $user)
    {
        $hairdresser = request()->route('hairdresser');
        return $user->status_id == 2 
        && $hairdresser->is_approved == 1 && $hairdresser->id == $user->id 
        ? Response::allow() : Response::deny('Unauthorized');
    }

    public function update(User $user, Hairstyle $hairstyle)
    {
        $hairdresser = request()->route('hairdresser');
        return $user->status_id == 2 
        && $hairdresser->is_approved == 1 && $hairdresser->id == $user->id 
        && $hairstyle->hairdresser_id == $hairdresser->id
        ? Response::allow() : Response::deny('Unauthorized');
    }

    public function delete(User $user, Hairstyle $hairstyle)
    {
        $hairdresser = request()->route('hairdresser');
        return $user->status_id == 2 
        && $hairdresser->is_approved == 1 && $hairdresser->id == $user->id 
        && $hairstyle->hairdresser_id == $hairdresser->id 
        ? Response::allow() : Response::deny('Unauthorized');
    }
}
