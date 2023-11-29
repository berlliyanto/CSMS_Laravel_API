<?php

namespace App\Http\Controllers;

use App\Http\Resources\LocationResource;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $location = Location::all();
        return LocationResource::collection($location->loadMissing('areas:id,area_name,location_id'));
    }
}
