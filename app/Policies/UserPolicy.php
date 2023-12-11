<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function view(User $user, User $model)
    {
        return $user->id == $model->id
        ? Response::allow() : Response::deny('Unauthorized');
    }

    public function update(User $user, User $model)
    {
        return $user->id == $model->id 
        ? Response::allow() : Response::deny('Unauthorized');
    }

    public function delete(User $user, User $model)
    {
        return $user->id == $model->id 
        ? Response::allow() : Response::deny('Unauthorized');
    }
}
