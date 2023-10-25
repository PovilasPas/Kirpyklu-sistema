<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\AuthenticateUserRequest;

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
        User::create($data);
        return response(['message' => 'Successfully registered'], 201);
    }

    public function authenticate(AuthenticateUserRequest $request)
    {
        if(!$token = auth()->attempt($request->all())) {
            return response(['message' => 'Invalid credentials'], 401);
        }
        return response([
            'message' => 'Successfully logged in',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ], 200);
    }

    public function logout()
    {
        auth()->logout();
        return response(['message' => 'Successfully logged out'], 200);
    }

    public function refresh()
    {
        return response([
            'message' => 'Token successfully refreshed',
            'access_token' => auth()->refresh(),
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
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
