<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;
use App\Http\Resources\CityResource;

class CityController extends Controller
{
    public function index()
    {
        return response(CityResource::collection(City::all()), 200);
    }

    public function show(City $city)
    {
        return response(new CityResource($city), 200);
    }
}
