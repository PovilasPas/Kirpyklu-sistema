<?php

namespace App\Http\Controllers;

use App\Models\HairSalon;
use App\Models\Hairdresser;
use Illuminate\Http\Request;
use App\Http\Requests\HairdresserRequest;
use App\Http\Resources\HairdresserResource;

class HairdresserController extends Controller
{
    public function index(HairSalon $salon)
    {
        return response(HairdresserResource::collection($salon->hairdressers),200);
    }

    public function show(HairSalon $salon, Hairdresser $hairdresser)
    {
        if($salon->id != $hairdresser->hair_salon_id) return response(['message' => 'resource not found'], 404);
        return response(new HairdresserResource($hairdresser), 200);
    }

    public function store(HairSalon $salon, HairdresserRequest $request)
    {
        $request->merge(['hair_salon_id' => $salon->id]);
        return response(new HairdresserResource(Hairdresser::create($request->all())), 201);
    }

    public function update(HairSalon $salon, Hairdresser $hairdresser, HairdresserRequest $request)
    {
        if($salon->id != $hairdresser->hair_salon_id) return response(['message' => 'resource not found'], 404);
        $hairdresser->update($request->all());
        return response(new HairdresserResource($hairdresser),200);
    }

    public function delete(HairSalon $salon, Hairdresser $hairdresser)
    {
        if($salon->id != $hairdresser->hair_salon_id) return response(['message' => 'resource not found'], 404);
        $hairdresser->delete();
        return response('',204);
    }
}
