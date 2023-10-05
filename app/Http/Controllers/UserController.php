<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    public function index()
    {
        return response(UserResource::collection(User::all()), 200);
    }

    public function show(User $user)
    {
        return response(new UserResource($user), 200);
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        return response(new UserResource(User::create($data)), 201);
    }

    public function update(User $user, UpdateUserRequest $request)
    {
        $data = $request->all();
        if(isset($data['password'])) $data['password'] = Hash::make($data['password']);
        $user->update($data);
        return response(new UserResource($user), 200);
    }

    public function delete(User $user)
    {
        $user->delete();
        return response('', 204);
    }
}
