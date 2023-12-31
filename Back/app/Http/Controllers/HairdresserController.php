<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\HairSalon;
use App\Models\Hairdresser;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Http\Resources\HairdresserResource;
use App\Http\Requests\StoreHairdresserRequest;
use App\Http\Requests\UpdateHairdresserRequest;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class HairdresserController extends Controller
{
    public function index(City $city, HairSalon $salon)
    {
        if($city->id != $salon->city_id) return response(['message' => 'Resource not found'], 404);
        $user = auth()->user();
        if($user && $user->status_id == 1 && $salon->manager_id == $user->id)
            return response(HairdresserResource::collection($salon->hairdressers), 200);
        else if($user && $user->status_id == 2 && $user->hairdresser && $user->hairdresser->hair_salon_id == $salon->id && !$user->hairdresser->is_approved)
            return response(HairdresserResource::collection($salon->approved_hairdressers->concat([$user->hairdresser])), 200);
        return response(HairdresserResource::collection($salon->approved_hairdressers),200);
    }

    public function show(City $city, HairSalon $salon, Hairdresser $hairdresser)
    {
        if($city->id !=  $salon->city_id || $salon->id != $hairdresser->hair_salon_id) return response(['message' => 'Resource not found'], 404);
        return response(new HairdresserResource($hairdresser), 200);
    }

    public function store(City $city, HairSalon $salon, StoreHairdresserRequest $request)
    {
        if($city->id != $salon->city_id) return response(['message' => 'Resource not found'], 404);
        $payload = auth()->payload();
        $request->merge([
            'id' => (int)$payload['sub'],
            'hair_salon_id' => $salon->id
        ]);
        try
        {
            return response(new HairdresserResource(Hairdresser::create($request->all())), 201);
        }
        catch(QueryException $e)
        {
            return response(['message' => 'Hairdresser can only work at one hair salon'], 409);
        }
    }

    public function update(City $city, HairSalon $salon, Hairdresser $hairdresser, UpdateHairdresserRequest $request)
    {
        if($city->id != $salon->city_id || $salon->id != $hairdresser->hair_salon_id) return response(['message' => 'Resource not found'], 404);
        
        $payload = auth()->payload();
        if($payload['role'] == 1)
        {
            $data = $request->all();
            $newSalon = HairSalon::find($data['hair_salon_id']);
            if($newSalon->manager_id != $payload['sub'] || $data['phone_nr'] != $hairdresser->phone_nr)
                return response(['message' => 'Unauthorized'], 403);
        }
        else if($payload['role'] == 2)
        {
            $data = $request->all();
            if($data['hair_salon_id'] != $hairdresser->hair_salon_id || $data['is_approved'] != $hairdresser->is_approved)
                return response(['message' => 'Unauthorized'], 403);
        }

        $hairdresser->update($request->all());
        return response(new HairdresserResource($hairdresser),200);
    }

    public function delete(City $city, HairSalon $salon, Hairdresser $hairdresser)
    {
        if($city->id != $salon->city_id || $salon->id != $hairdresser->hair_salon_id) return response(['message' => 'Resource not found'], 404);
        $hairdresser->delete();
        return response('',204);
    }
}
