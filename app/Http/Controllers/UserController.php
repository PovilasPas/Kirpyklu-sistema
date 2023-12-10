<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\AuthenticateUserRequest;
use Illuminate\Auth\AuthenticationException;

class UserController extends Controller
{
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

    public function authenticate(AuthenticateUserRequest $request)
    {
        if(!$token = auth()->attempt($request->all())) {
            return response(['message' => 'Invalid credentials'], 401);
        }
        return response([
            'message' => 'Successfully logged in',
            'accessToken' => $token,
            'tokenType' => 'bearer',
            'expiresIn' => auth()->factory()->getTTL() * 60
        ], 200);
    }

    public function logout()
    {
        auth()->logout();
        return response(['message' => 'Successfully logged out'], 200);
    }

    public function refresh()
    {
        try
        {
            return response([
                'message' => 'Token successfully refreshed',
                'accessToken' => auth()->claims(['iss' => 'hair_salon_app'])->refresh(),
                'tokenType' => 'bearer',
                'expiresIn' => auth()->factory()->getTTL() * 60,
            ], 200);
        }
        catch(Exception $e)
        {
            return response([
                'message' => 'Invalid refresh token'
            ], 401);
        }
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
        auth()->invalidate();
        return response('', 204);
    }
}
