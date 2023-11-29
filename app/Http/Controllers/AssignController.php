<?php

namespace App\Http\Controllers;

use App\Http\Resources\AssignResource;
use App\Models\Assign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignController extends Controller
{
    public function index()
    {
        $assigns = Assign::with([
            'assignBy:id,name',
            'area:id,area_name,location_id',
            'area.location:id,location_name', 
            'supervisor:id,name'
        ])->get();
    
        return AssignResource::collection($assigns)->additional([
            'success' => true,
            'message' => 'Data fetched successfully',
        ]);
    }

    public function show($id)
    {
        $assign = Assign::where('id',$id)->get()->first();

        if(!$assign) {
            return response()->json(['message' => 'Data not found'], 404);
        }

        return new AssignResource($assign->loadMissing([
            'assignBy:id,name',
            'area:id,area_name,location_id',
            'supervisor:id,name'
        ]));
    }

    public function update(Request $request)
    {
    }

    public function destroy(Request $request)
    {
    }
}
