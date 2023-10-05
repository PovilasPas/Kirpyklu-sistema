<?php

namespace App\Http\Controllers;

use App\Models\HairSalon;
use App\Models\Hairstyle;
use App\Models\Hairdresser;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use App\Http\Requests\HairstyleRequest;
use App\Http\Resources\HairstyleResource;
use App\Http\Requests\StoreHairstyleRequest;
use App\Http\Requests\UpdateHairstyleRequest;

class HairstyleController extends Controller
{
    public function index(HairSalon $salon, Hairdresser $hairdresser)
    {
        if($salon->id != $hairdresser->hair_salon_id) return response(['message' => 'Resource not found'],404);
        return response(HairstyleResource::collection($hairdresser->hairstyles),200);
    }

    public function show(HairSalon $salon, Hairdresser $hairdresser, Hairstyle $hairstyle)
    {
        if($salon->id != $hairdresser->hair_salon_id || $hairdresser->id != $hairstyle->hairdresser_id) return response(['message' => 'Resource not found'], 404); 
        return response(new HairstyleResource($hairstyle), 200);
    }

    public function store(HairSalon $salon, Hairdresser $hairdresser, StoreHairstyleRequest $request)
    {
        if($salon->id != $hairdresser->hair_salon_id) return response(['message' => 'Resource not found'],404);
        $request->merge([
            'hairdresser_id' => $hairdresser->id
        ]);
        $data = $request->all();
        if(isset($data['image']) && $data['image'])
        {
            $path = 'storage/images/' . Str::random(40) . '.jpg';
            Image::make($data['image'])->save(public_path($path));
            $data['image'] = $path;
        }
        return response(new HairstyleResource(Hairstyle::create($data)));
    }

    public function update(HairSalon $salon, Hairdresser $hairdresser, Hairstyle $hairstyle, UpdateHairstyleRequest $request)
    {
        if($salon->id != $hairdresser->hair_salon_id || $hairdresser->id != $hairstyle->hairdresser_id) return response(['message' => 'Resource not found'], 404); 
        $data = $request->all();
        if(isset($data['image']) && $data['image'])
        {
            $path = 'storage/images/' . Str::random(40) . '.jpg';
            Image::make($data['image'])->save(public_path($path));
            $data['image'] = $path;
            if(File::exists($hairstyle->image)) File::delete($hairstyle->image);
        }
        $hairstyle->update($data);
        return response(new HairstyleResource($hairstyle));
    }

    public function delete(HairSalon $salon, Hairdresser $hairdresser, Hairstyle $hairstyle)
    {
        if($salon->id != $hairdresser->hair_salon_id || $hairdresser->id != $hairstyle->hairdresser_id) return response(['message' => 'Resource not found'], 404);
        if(File::exists($hairstyle->image)) File::delete($hairstyle->image);
        $hairstyle->delete();
        return response('', 204);
    }
}
