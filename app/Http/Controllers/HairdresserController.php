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
        if($city->id != $salon->city_id) return response(['message' => 'resource not found'], 404);
        return response(HairdresserResource::collection($salon->hairdressers),200);
    }

    public function show(City $city, HairSalon $salon, Hairdresser $hairdresser)
    {
        if($city->id !=  $salon->city_id || $salon->id != $hairdresser->hair_salon_id) return response(['message' => 'resource not found'], 404);
        return response(new HairdresserResource($hairdresser), 200);
    }

    public function store(City $city, HairSalon $salon, StoreHairdresserRequest $request)
    {
        if($city->id != $salon->city_id) return response(['message' => 'resource not found'], 404);
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
            return response(['message' => 'hairdresser can only work at one hair salon'], 409);
        }
    }

    public function update(City $city, HairSalon $salon, Hairdresser $hairdresser, UpdateHairdresserRequest $request)
    {
        if($city->id != $salon->city_id || $salon->id != $hairdresser->hair_salon_id) return response(['message' => 'resource not found'], 404);
        
        $payload = auth()->payload();
        if($payload['role'] == 1)
        {
            $data = $request->all();
            $newSalon = HairSalon::find($data['hair_salon_id']);
            if($newSalon->manager_id != $payload['sub'] || $data['phone_nr'] != $hairdresser->phone_nr) throw new AccessDeniedHttpException('Unauthorized');
        }
        else if($payload['role'] == 2)
        {
            $data = $request->all();
            if($data['hair_salon_id'] != $hairdresser->hair_salon_id || $data['is_approved'] != $hairdresser->is_approved) throw new AccessDeniedHttpException('Unauthorized');
        }

        $hairdresser->update($request->all());
        return response(new HairdresserResource($hairdresser),200);
    }

    public function delete(City $city, HairSalon $salon, Hairdresser $hairdresser)
    {
        if($city->id != $salon->city_id || $salon->id != $hairdresser->hair_salon_id) return response(['message' => 'resource not found'], 404);
        $hairdresser->delete();
        return response('',204);
    }
}
