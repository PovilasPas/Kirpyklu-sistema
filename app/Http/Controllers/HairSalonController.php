<?php

namespace App\Http\Controllers;

use App\Models\HairSalon;
use Illuminate\Http\Request;
use App\Http\Requests\HairSalonRequest;
use App\Http\Resources\HairSalonResource;
use App\Filters\HairSalonFilter;

class HairSalonController extends Controller
{
    public function index(Request $request)
    {
        $filter = new HairSalonFilter();
        $queries = $filter->transform($request);
        if(count($queries) != 0)
        {
            return response(HairSalonResource::collection(HairSalon::where($queries)->get()), 200);
        }
        return response(HairSalonResource::collection(HairSalon::all()), 200);
    }

    public function show(HairSalon $salon)
    {
        return response(new HairSalonResource($salon), 200);
    }

    public function store(HairSalonRequest $request)
    {
        return response(new HairSalonResource(HairSalon::create($request->all())), 201);
    }

    public function update(HairSalon $salon, HairSalonRequest $request)
    {
        $salon->update($request->all());
        return response(new HairSalonResource($salon),200);
    }

    public function delete(HairSalon $salon)
    {
        $salon->delete();
        return response('', 204);
    }
}
