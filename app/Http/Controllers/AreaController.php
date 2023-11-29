<?php

namespace App\Http\Controllers;

use App\Http\Resources\AreaRescource;
use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index()
    {
        $areas = Area::all();
        return AreaRescource::collection($areas->loadMissing(['location:id,location_name']));
    }

    public function areaByLocation($location_id)
    {
        $areas = Area::where('location_id', $location_id)->get();
        return AreaRescource::collection($areas->loadMissing(['location:id,location_name']));
    }
}
