<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\User;
use App\Models\HairSalon;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Http\Resources\HairSalonResource;
use App\Http\Requests\StoreHairSalonRequest;
use App\Http\Requests\UpdateHairSalonRequest;

class HairSalonController extends Controller
{
    public function index(?City $city, ?User $manager)
    {
        if($city->exists)
            return response(HairSalonResource::collection($city->hairSalons), 200);
        else
            return response(HairSalonResource::collection($manager->hairSalons), 200);

    }

    public function show(City $city, HairSalon $salon)
    {
        if($city->id != $salon->city_id) return response(['message' => 'Resource not found'], 404);
        return response(new HairSalonResource($salon), 200);
    }

    public function store(City $city, StoreHairSalonRequest $request)
    {
        $payload = auth()->payload();
        $request->merge([
            'city_id' => $city->id,
            'manager_id' => (int)$payload['sub']
        ]);
        return response(new HairSalonResource(HairSalon::create($request->all())), 201);
    }

    public function update(City $city, HairSalon $salon, UpdateHairSalonRequest $request)
    {
        if($city->id != $salon->city_id) return response(['message' => 'Resource not found'], 404);
        $salon->update($request->all());
        return response(new HairSalonResource($salon),200);
    }

    public function delete(City $city, HairSalon $salon)
    {
        if($city->id != $salon->city_id) return response(['message' => 'Resource not found'], 404);
        $salon->delete();
        return response('', 204);
    }
}
