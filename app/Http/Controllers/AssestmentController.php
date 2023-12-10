<?php

namespace App\Http\Controllers;

use App\Exports\AssestmentExport;
use App\Http\Resources\AssestmentResource;
use App\Models\Assestment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class AssestmentController extends Controller
{
    public function index()
    {
        $assestments = Assestment::with(['leaders:id,name', 'cleaners:id,name', 'locations:id,location_name'])->get();
        return response()->json([
            'message' => 'Success',
            'data' => $assestments
        ]);
    }

    public function calculateAssestments($id)
    {
        $assestment = Assestment::with(['leaders:id,name', 'cleaners:id,name', 'locations:id,location_name'])->find($id);

        if (!$assestment) {
            return response()->json([
                'message' => 'Assestment not found'
            ], 404);
        }


        return response()->json([
            'message' => 'Success',
            'data' => new AssestmentResource($assestment)
        ]);
    }

    public function calculateAssestmentsPerCleaner($id)
    {
        $assestment = Assestment::with(['leaders:id,name', 'cleaners:id,name', 'locations:id,location_name'])->where('cleaner', $id)->get();

        if (!$assestment) {
            return response()->json([
                'message' => 'Assestment not found'
            ], 404);
        }


        return response()->json([
            'message' => 'Success',
            'data' => AssestmentResource::collection($assestment)
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'cleaner' => 'required|exists:users,id',
            'location' => 'required|exists:locations,id',
            'plk_s' => 'required',
            'plk_ddb' => 'required',
            'sik_mptu' => 'required',
            'sik_ktp' => 'required',
            'sik_kdtma' => 'required',
            'sik_mw' => 'required',
            'sik_rmtp' => 'required',
            'pnm_r' => 'required',
            'pnm_mslc' => 'required',
            'pnm_q' => 'required',
            'tj_ktw' => 'required',
            'tj_kwdmp' => 'required',
            'tj_kd' => 'required',
            'tj_mpsj' => 'required',
            'tj_mpmp' => 'required',
            'kom_k' => 'required',
            'kom_p' => 'required',
            'kom_kdb' => 'required',
            'kom_ptp' => 'required',
            'kom_kmk' => 'required',
            'kom_s' => 'required'
        ]);

        $request['leader'] = Auth::user()->id;

        $assestmentsStore = Assestment::create($request->all());
        return response()->json([
            "message" => "Success",
            "data" => $assestmentsStore
        ]);
    }

    public function exportAssestments(Request $request) {
        return (new AssestmentExport($request->cleaner_id))->download('assestments.xlsx');
    }
}
