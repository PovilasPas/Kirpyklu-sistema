<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;
use App\Http\Resources\StatusResource;

class StatusController extends Controller
{
    public function index()
    {
        return response(StatusResource::collection(Status::all()), 200);
    }
}
